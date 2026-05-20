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

  const promise = (async (): Promise<SessionMeta> => {
    let meta = sessions.get(sessionId);
    if (!meta) {
      meta = {
        sessionId,
        status: 'starting',
        qr: undefined,
        pairingCode: undefined,
        linkPhone: digits,
        phone: undefined,
        sock: undefined,
      };
      sessions.set(sessionId, meta);
    } else {
      meta.status = 'starting';
      meta.linkPhone = digits;
      meta.pairingCode = undefined;
    }

    const sock = meta.sock ?? (await createSocket(sessionId, meta));
    await sleep(2000);

    if (meta.status !== 'connected' && !sock.authState.creds.registered) {
      try {
        const code = await sock.requestPairingCode(digits);
        meta.pairingCode = code;
        meta.status = 'pending_pairing';
        logger.info({ sessionId }, 'Pairing code ready');
      } catch (err) {
        logger.error({ sessionId, err }, 'requestPairingCode failed');
        throw err;
      }
    }

    return meta;
  })();

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
