import makeWASocket, {
  Browsers,
  DisconnectReason,
  fetchLatestBaileysVersion,
  useMultiFileAuthState,
  type ConnectionState,
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
  pairingRequestedAt?: number;
}

const sessions = new Map<string, SessionMeta>();
const startPromises = new Map<string, Promise<SessionMeta>>();
const finalizingSessions = new Set<string>();

const PAIRING_READY_DELAY_MS = Number(process.env.PAIRING_READY_DELAY_MS ?? 8000);
const PAIRING_SOCKET_READY_TIMEOUT_MS = Number(process.env.PAIRING_SOCKET_READY_TIMEOUT_MS ?? 45_000);

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

  // E.164: country code + national number without leading 0 (e.g. 20 + 1090537394)
  if (digits.startsWith('20') && digits.length > 3 && digits[2] === '0') {
    digits = `20${digits.slice(3)}`;
  }

  if (digits.startsWith('966') && digits.length > 4 && digits[3] === '0') {
    digits = `966${digits.slice(4)}`;
  }

  if (digits.startsWith('0') && digits.length >= 10 && digits.length <= 12) {
    digits = digits.slice(1);
  }

  return digits;
}

/** Raw 8 chars for storage; use formatPairingCodeDisplay() for WhatsApp UI (XXXX-XXXX). */
export function formatPairingCodeRaw(code: string): string {
  return code.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
}

export function formatPairingCodeDisplay(code: string): string {
  const raw = formatPairingCodeRaw(code);
  if (raw.length === 8) {
    return `${raw.slice(0, 4)}-${raw.slice(4)}`;
  }
  return raw;
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
  logger.info({ sessionId }, 'session auth wiped');
}

export async function waitForConnected(sessionId: string, timeoutMs: number): Promise<boolean> {
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
  timeoutMs = 90_000
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

async function waitUntilReadyForPairing(sock: WASocket, sessionId: string): Promise<void> {
  return new Promise((resolve, reject) => {
    const deadline = Date.now() + PAIRING_SOCKET_READY_TIMEOUT_MS;

    const onUpdate = (update: Partial<ConnectionState>) => {
      const { connection, qr } = update;

      if (connection === 'open') {
        cleanup();
        resolve();
        return;
      }

      if (connection === 'connecting' || qr) {
        logger.info({ sessionId, connection, hasQr: Boolean(qr) }, 'socket ready for pairing request');
        cleanup();
        resolve();
        return;
      }

      if (connection === 'close') {
        const code = (update.lastDisconnect?.error as Boom | undefined)?.output?.statusCode;
        if (code === DisconnectReason.loggedOut) {
          cleanup();
          reject(new Error('WhatsApp logged out this session'));
        }
      }
    };

    const poll = setInterval(() => {
      if (Date.now() > deadline) {
        cleanup();
        reject(
          new Error(
            `Socket not ready for pairing within ${PAIRING_SOCKET_READY_TIMEOUT_MS / 1000}s`
          )
        );
      }
    }, 500);

    const cleanup = () => {
      sock.ev.off('connection.update', onUpdate);
      clearInterval(poll);
    };

    sock.ev.on('connection.update', onUpdate);
  });
}

export async function isAuthRegistered(sessionId: string): Promise<boolean> {
  if (!sessionAuthExists(sessionId)) {
    return false;
  }

  try {
    const { state } = await useMultiFileAuthState(sessionPath(sessionId));
    return Boolean(state.creds.registered);
  } catch {
    return false;
  }
}

async function reconnectAfterRestart(sessionId: string, meta: SessionMeta): Promise<void> {
  if (meta.status === 'connected') {
    return;
  }

  if (finalizingSessions.has(sessionId)) {
    return;
  }

  finalizingSessions.add(sessionId);

  try {
    logger.info({ sessionId, previousStatus: meta.status }, 'reconnecting after pairing (saved auth)');
    meta.status = 'starting';
    meta.pairingCode = undefined;

    await createSocket(sessionId, meta);
    const ok = await waitForConnected(sessionId, 90_000);
    if (!ok) {
      logger.warn({ sessionId }, 'reconnect after pairing did not reach connected within timeout');
    }
  } catch (err) {
    logger.error({ sessionId, err }, 'reconnect after pairing failed');
    meta.status = 'disconnected';
  } finally {
    finalizingSessions.delete(sessionId);
  }
}

/**
 * After user enters pairing code, complete the link if auth was saved but socket dropped.
 */
export async function ensurePairingFinalized(sessionId: string): Promise<SessionMeta | undefined> {
  const meta = sessions.get(sessionId);
  if (!meta) {
    return undefined;
  }

  if (meta.status === 'connected') {
    return meta;
  }

  if (meta.status !== 'pending_pairing' && meta.status !== 'starting') {
    return meta;
  }

  const registered = await isAuthRegistered(sessionId);
  if (!registered) {
    logger.debug({ sessionId, status: meta.status }, 'pairing not finalized yet — creds not registered');
    return meta;
  }

  logger.info({ sessionId }, 'pairing creds registered — finalizing connection');
  await reconnectAfterRestart(sessionId, meta);
  return sessions.get(sessionId);
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
    browser: Browsers.macOS('Chrome'),
    markOnlineOnConnect: false,
    syncFullHistory: false,
  });

  meta.sock = sock;

  sock.ev.on('creds.update', () => {
    void saveCreds();
    if (meta.status === 'pending_pairing' && sock.authState.creds.registered) {
      logger.info({ sessionId }, 'creds.registered during pending_pairing — scheduling finalize');
      setTimeout(() => {
        void reconnectAfterRestart(sessionId, meta);
      }, 1500);
    }
  });

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr, isNewLogin } = update;
    const statusCode = (lastDisconnect?.error as Boom | undefined)?.output?.statusCode;

    if (isNewLogin && meta.status === 'pending_pairing') {
      logger.info({ sessionId }, 'isNewLogin during pairing — finalizing');
      void reconnectAfterRestart(sessionId, meta);
    }

    if (qr) {
      meta.qr = qr;
      if (meta.status !== 'pending_pairing') {
        meta.status = 'pending_qr';
      }
      logger.info({ sessionId, status: meta.status }, 'QR event (ignored during pairing if pending_pairing)');
    }

    if (connection === 'open') {
      meta.status = 'connected';
      meta.qr = undefined;
      meta.pairingCode = undefined;
      const user = sock.user;
      meta.phone = user?.id?.split(':')[0]?.split('@')[0];
      logger.info({ sessionId, phone: meta.phone }, 'WhatsApp connected');
    }

    if (connection === 'connecting') {
      logger.info({ sessionId, status: meta.status }, 'WhatsApp connecting');
    }

    if (connection === 'close') {
      const loggedOut = statusCode === DisconnectReason.loggedOut;
      const restartRequired = statusCode === DisconnectReason.restartRequired;
      const wasPairing = meta.status === 'pending_pairing';

      logger.warn(
        { sessionId, statusCode, loggedOut, restartRequired, wasPairing, status: meta.status },
        'connection closed'
      );

      meta.sock = undefined;

      if (wasPairing) {
        if (loggedOut) {
          meta.status = 'disconnected';
          meta.pairingCode = undefined;
          return;
        }

        if (restartRequired || isNewLogin) {
          void reconnectAfterRestart(sessionId, meta);
          return;
        }

        // WA often closes socket after code entry — reconnect if creds were saved
        setTimeout(() => {
          void (async () => {
            if (meta.status === 'connected') {
              return;
            }
            const registered = await isAuthRegistered(sessionId);
            if (registered) {
              logger.info({ sessionId, statusCode }, 'pairing close with saved creds — finalizing');
              await reconnectAfterRestart(sessionId, meta);
            } else {
              logger.info(
                { sessionId, statusCode },
                'pairing socket closed — waiting for user (creds not registered yet)'
              );
            }
          })();
        }, 2500);

        return;
      }

      meta.status = 'disconnected';
      meta.pairingCode = undefined;

      if (loggedOut) {
        return;
      }

      if (meta.linkPhone) {
        setTimeout(() => {
          void startSessionWithPairing(sessionId, meta.linkPhone!, true);
        }, 5000);
      } else {
        setTimeout(() => {
          void startSession(sessionId);
        }, 3000);
      }
    }
  });

  const { registerInboundHandler } = await import('./events.js');
  registerInboundHandler(sock, sessionId);

  return sock;
}

async function requestPairingCodeWithRetry(
  sock: WASocket,
  digits: string,
  sessionId: string
): Promise<string> {
  await waitUntilReadyForPairing(sock, sessionId);
  logger.info({ sessionId, delayMs: PAIRING_READY_DELAY_MS }, 'waiting before requestPairingCode');
  await sleep(PAIRING_READY_DELAY_MS);

  let lastError: unknown;

  for (let attempt = 1; attempt <= 4; attempt++) {
    try {
      const raw = await sock.requestPairingCode(digits);
      const code = formatPairingCodeRaw(raw);
      logger.info(
        { sessionId, attempt, codeLength: code.length, display: formatPairingCodeDisplay(code) },
        'pairing code generated'
      );
      return code;
    } catch (err) {
      lastError = err;
      const message = err instanceof Error ? err.message : String(err);
      logger.warn({ sessionId, attempt, message }, 'requestPairingCode attempt failed');
      await sleep(attempt * 2000);
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
    pairingRequestedAt: Date.now(),
  };
  sessions.set(sessionId, meta);

  logger.info({ sessionId, digits: digits.slice(-4), fresh }, 'starting pairing flow');

  const sock = await createSocket(sessionId, meta);

  if (sock.authState.creds.registered) {
    const reconnected = await waitForConnected(sessionId, 20_000);
    if (reconnected) {
      logger.info({ sessionId }, 'session already registered and connected');
      return meta;
    }
    logger.warn({ sessionId }, 'stale registered session — wiping for fresh pairing');
    wipeSessionAuth(sessionId);
    return runPairingFlow(sessionId, digits, true);
  }

  const code = await requestPairingCodeWithRetry(sock, digits, sessionId);
  meta.pairingCode = code;
  meta.status = 'pending_pairing';

  logger.info({ sessionId, status: meta.status }, 'pairing code ready — waiting for user to enter in WhatsApp');

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
  phone: string,
  fresh = true
): Promise<SessionMeta> {
  const digits = normalizePhoneDigits(phone);
  if (!digits || digits.length < 10) {
    throw new Error(`Invalid phone number for pairing (E.164 required): ${digits || 'empty'}`);
  }

  const existing = sessions.get(sessionId);
  if (existing?.status === 'connected') {
    return existing;
  }

  const inFlight = startPromises.get(sessionId);
  if (inFlight) {
    logger.info({ sessionId }, 'pairing already in progress — awaiting');
    return inFlight;
  }

  logger.info({ sessionId, digitsSuffix: digits.slice(-4), fresh }, 'startSessionWithPairing');

  const promise = runPairingFlow(sessionId, digits, fresh);

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
  startPromises.delete(sessionId);

  const dir = sessionPath(sessionId);
  if (fs.existsSync(dir)) {
    fs.rmSync(dir, { recursive: true, force: true });
  }

  logger.info({ sessionId }, 'session deleted');
}
