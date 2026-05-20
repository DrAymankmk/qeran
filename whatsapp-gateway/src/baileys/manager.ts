import makeWASocket, {
  DisconnectReason,
  fetchLatestBaileysVersion,
  useMultiFileAuthState,
  type WASocket,
} from '@whiskeysockets/baileys';
import { Boom } from '@hapi/boom';
import path from 'node:path';
import fs from 'node:fs';
import pino from 'pino';

const logger = pino({ level: process.env.LOG_LEVEL ?? 'info' });

export type SessionStatus =
  | 'pending_qr'
  | 'pending_pairing'
  | 'connected'
  | 'disconnected'
  | 'starting';

export interface SessionMeta {
  sessionId: string;
  status: SessionStatus;
  qr?: string;
  pairingCode?: string;
  linkPhone?: string;
  phone?: string;
  sock?: WASocket;
}

const sessions = new Map<string, SessionMeta>();
const startPromises = new Map<string, Promise<SessionMeta>>();

function sessionsDir(): string {
  return process.env.SESSIONS_DIR ?? path.join(process.cwd(), 'sessions');
}

function sessionPath(sessionId: string): string {
  const safe = sessionId.replace(/[^a-zA-Z0-9_-]/g, '_');
  return path.join(sessionsDir(), safe);
}

function sessionAuthExists(sessionId: string): boolean {
  const dir = sessionPath(sessionId);
  return fs.existsSync(path.join(dir, 'creds.json'));
}

export function normalizePhoneDigits(phone: string): string {
  let digits = phone.replace(/\D/g, '');
  if (digits.startsWith('00')) {
    digits = digits.slice(2);
  }
  return digits;
}

export function getSessionMeta(sessionId: string): SessionMeta | undefined {
  return sessions.get(sessionId);
}

export function getSocket(sessionId: string): WASocket | undefined {
  return sessions.get(sessionId)?.sock;
}

export function getQr(sessionId: string): string | undefined {
  return sessions.get(sessionId)?.qr;
}

export function getPairingCode(sessionId: string): string | undefined {
  return sessions.get(sessionId)?.pairingCode;
}

function sleep(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function endSocket(meta: SessionMeta): void {
  if (meta.sock) {
    try {
      meta.sock.end(undefined);
    } catch {
      // ignore
    }
    meta.sock = undefined;
  }
}

function wipeSessionAuth(sessionId: string): void {
  const meta = sessions.get(sessionId);
  if (meta) {
    endSocket(meta);
    sessions.delete(sessionId);
  }
  const dir = sessionPath(sessionId);
  if (fs.existsSync(dir)) {
    fs.rmSync(dir, { recursive: true, force: true });
  }
}

async function waitForConnected(sessionId: string, timeoutMs: number): Promise<boolean> {
  const deadline = Date.now() + timeoutMs;
  while (Date.now() < deadline) {
    const meta = sessions.get(sessionId);
    if (meta?.status === 'connected') {
      return true;
    }
    await sleep(500);
  }
  return false;
}

export async function waitForQrOrConnected(
  sessionId: string,
  timeoutMs = 60_000
): Promise<SessionMeta> {
  const deadline = Date.now() + timeoutMs;

  while (Date.now() < deadline) {
    const meta = sessions.get(sessionId);
    if (meta?.status === 'connected') {
      return meta;
    }
    if (meta?.qr) {
      return meta;
    }
    await sleep(500);
  }

  throw new Error(
    `Timed out waiting for QR (${timeoutMs / 1000}s). DELETE /sessions/${sessionId} and try again.`
  );
}

export async function waitForPairingOrConnected(
  sessionId: string,
  timeoutMs = 60_000
): Promise<SessionMeta> {
  const deadline = Date.now() + timeoutMs;

  while (Date.now() < deadline) {
    const meta = sessions.get(sessionId);
    if (meta?.status === 'connected') {
      return meta;
    }
    if (meta?.pairingCode) {
      return meta;
    }
    await sleep(500);
  }

  throw new Error(
    `Timed out waiting for pairing code (${timeoutMs / 1000}s). DELETE /sessions/${sessionId} and try again.`
  );
}

async function createSocket(sessionId: string, meta: SessionMeta): Promise<WASocket> {
  const authPath = sessionPath(sessionId);
  fs.mkdirSync(authPath, { recursive: true });

  const { state, saveCreds } = await useMultiFileAuthState(authPath);
  const { version } = await fetchLatestBaileysVersion();

  const sock = makeWASocket({
    version,
    auth: state,
    logger: pino({ level: 'silent' }),
    printQRInTerminal: false,
  });

  meta.sock = sock;

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      meta.qr = qr;
      if (meta.status !== 'pending_pairing') {
        meta.status = 'pending_qr';
      }
      logger.info({ sessionId }, 'QR code ready');
    }

    if (connection === 'open') {
      meta.status = 'connected';
      meta.qr = undefined;
      meta.pairingCode = undefined;
      const user = sock.user;
      meta.phone = user?.id?.split(':')[0]?.split('@')[0];
      logger.info({ sessionId, phone: meta.phone }, 'WhatsApp connected');
    }

    if (connection === 'close') {
      meta.status = 'disconnected';
      meta.sock = undefined;
      meta.pairingCode = undefined;
      const code = (lastDisconnect?.error as Boom | undefined)?.output?.statusCode;
      const loggedOut = code === DisconnectReason.loggedOut;

      logger.warn({ sessionId, code, loggedOut }, 'connection closed');

      if (loggedOut) {
        return;
      }

      const linkPhone = meta.linkPhone;
      setTimeout(() => {
        if (linkPhone) {
          void startSessionWithPairing(sessionId, linkPhone);
        } else {
          void startSession(sessionId);
        }
      }, 3000);
    }
  });

  const { registerInboundHandler } = await import('./events.js');
  registerInboundHandler(sock, sessionId);

  return sock;
}

async function requestPairingCodeWithRetry(sock: WASocket, digits: string, sessionId: string): Promise<string> {
  let lastError: unknown;

  for (let attempt = 1; attempt <= 5; attempt++) {
    try {
      await sleep(attempt * 1500);
      const code = await sock.requestPairingCode(digits);
      logger.info({ sessionId, attempt }, 'Pairing code ready');
      return code;
    } catch (err) {
      lastError = err;
      logger.warn({ sessionId, attempt, err }, 'requestPairingCode attempt failed');
    }
  }

  const message = lastError instanceof Error ? lastError.message : 'requestPairingCode failed';
  throw new Error(message);
}

async function runPairingFlow(sessionId: string, digits: string, fresh: boolean): Promise<SessionMeta> {
  if (fresh) {
    wipeSessionAuth(sessionId);
  } else {
    const existing = sessions.get(sessionId);
    if (existing?.sock) {
      endSocket(existing);
    }
  }

  const meta: SessionMeta = {
    sessionId,
    status: 'starting',
    qr: undefined,
    pairingCode: undefined,
    linkPhone: digits,
    phone: undefined,
    sock: undefined,
  };
  sessions.set(sessionId, meta);

  const sock = await createSocket(sessionId, meta);

  if (sock.authState.creds.registered) {
    const reconnected = await waitForConnected(sessionId, 15_000);
    if (reconnected) {
      return meta;
    }
    logger.warn({ sessionId }, 'Stale registered session — wiping for fresh pairing');
    wipeSessionAuth(sessionId);
    return runPairingFlow(sessionId, digits, true);
  }

  const code = await requestPairingCodeWithRetry(sock, digits, sessionId);
  meta.pairingCode = code;
  meta.status = 'pending_pairing';

  return meta;
}

export async function startSession(sessionId: string): Promise<SessionMeta> {
  const existing = sessions.get(sessionId);
  if (existing?.status === 'connected') {
    return existing;
  }

  if (existing?.sock) {
    return existing;
  }

  const inFlight = startPromises.get(sessionId);
  if (inFlight) {
    return inFlight;
  }

  const promise = (async (): Promise<SessionMeta> => {
    let meta = sessions.get(sessionId);
    if (!meta) {
      meta = {
        sessionId,
        status: 'starting',
        qr: undefined,
        pairingCode: undefined,
        linkPhone: undefined,
        phone: undefined,
        sock: undefined,
      };
      sessions.set(sessionId, meta);
    } else {
      meta.status = 'starting';
      meta.linkPhone = undefined;
    }

    await createSocket(sessionId, meta);
    return meta;
  })();

  startPromises.set(sessionId, promise);

  try {
    return await promise;
  } finally {
    startPromises.delete(sessionId);
  }
}

export async function startSessionWithPairing(
  sessionId: string,
  phone: string
): Promise<SessionMeta> {
  const digits = normalizePhoneDigits(phone);
  if (!digits) {
    throw new Error('Invalid phone number for pairing');
  }

  const existing = sessions.get(sessionId);
  if (existing?.status === 'connected') {
    return existing;
  }

  const inFlight = startPromises.get(sessionId);
  if (inFlight) {
    return inFlight;
  }

  const promise = runPairingFlow(sessionId, digits, sessionAuthExists(sessionId));

  startPromises.set(sessionId, promise);

  try {
    return await promise;
  } finally {
    startPromises.delete(sessionId);
  }
}

export async function deleteSession(sessionId: string): Promise<void> {
  const meta = sessions.get(sessionId);
  if (meta?.sock) {
    try {
      await meta.sock.logout();
    } catch {
      // ignore
    }
  }
  sessions.delete(sessionId);

  const dir = sessionPath(sessionId);
  if (fs.existsSync(dir)) {
    fs.rmSync(dir, { recursive: true, force: true });
  }
}
