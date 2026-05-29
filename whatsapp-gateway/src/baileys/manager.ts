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
  /** Set when WhatsApp accepts the code (me.id saved) — user may still need to tap "Link device". */
  pairingAcceptedAt?: number;
}

const sessions = new Map<string, SessionMeta>();
const startPromises = new Map<string, Promise<SessionMeta>>();
const finalizingSessions = new Set<string>();
const pairingKeepaliveTimers = new Map<string, ReturnType<typeof setInterval>>();

const PAIRING_READY_DELAY_MS = Number(process.env.PAIRING_READY_DELAY_MS ?? 3000);
const PAIRING_KEEPALIVE_MS = Number(process.env.PAIRING_KEEPALIVE_MS ?? 15_000);
const PAIRING_CODE_TTL_MS = Number(process.env.PAIRING_CODE_TTL_MS ?? 300_000);
const PAIRING_SOCKET_READY_TIMEOUT_MS = Number(process.env.PAIRING_SOCKET_READY_TIMEOUT_MS ?? 45_000);

function sessionsDir(): string {
  return process.env.SESSIONS_DIR ?? path.join(process.cwd(), 'sessions');
}

function sessionPath(sessionId: string): string {
  const safe = sessionId.replace(/[^a-zA-Z0-9_-]/g, '_');
  return path.join(sessionsDir(), safe);
}

export function sessionAuthExists(sessionId: string): boolean {
  const dir = sessionPath(sessionId);
  return fs.existsSync(path.join(dir, 'creds.json'));
}

export function normalizePhoneDigits(phone: string): string {
  let digits = phone.replace(/\D/g, '');
  if (digits.startsWith('00')) {
    digits = digits.slice(2);
  }

  let cc = '';
  let national = digits;

  if (digits.startsWith('966')) {
    cc = '966';
    national = digits.slice(3);
  } else if (digits.startsWith('20')) {
    cc = '20';
    national = digits.slice(2);
  }

  while (national.startsWith('0') && national.length > 1) {
    national = national.slice(1);
  }

  if (cc !== '') {
    digits = cc + national;
  } else {
    digits = national;
  }

  if (cc === '20' && !/^20(10|11|12|15)\d{8}$/.test(digits)) {
    logger.warn(
      { phoneSuffix: digits.slice(-4), length: digits.length },
      'Egypt pairing number may be invalid — expected 20 + 10/11/12/15 + 8 digits'
    );
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

function stopPairingKeepalive(sessionId: string): void {
  const timer = pairingKeepaliveTimers.get(sessionId);
  if (timer) {
    clearInterval(timer);
    pairingKeepaliveTimers.delete(sessionId);
  }
}

function startPairingKeepalive(sessionId: string, meta: SessionMeta): void {
  stopPairingKeepalive(sessionId);
  const timer = setInterval(() => {
    void maintainPairingSocketAlive(sessionId, meta);
  }, PAIRING_KEEPALIVE_MS);
  pairingKeepaliveTimers.set(sessionId, timer);
}

/**
 * Pairing codes only work while the gateway socket is online. Recreate it if WA closed it.
 * After the code is accepted, keep the same socket open until the user taps "Link device"
 * on WhatsApp's confirmation screen — reconnecting early breaks the link.
 */
async function maintainPairingSocketAlive(sessionId: string, meta: SessionMeta): Promise<void> {
  if (meta.status !== 'pending_pairing') {
    return;
  }

  const progress = await getPairingProgress(sessionId);
  const inPairingFlow = Boolean(meta.pairingCode) || progress.pairingAccepted;
  if (!inPairingFlow) {
    return;
  }

  if (meta.pairingRequestedAt && Date.now() - meta.pairingRequestedAt > PAIRING_CODE_TTL_MS) {
    logger.warn({ sessionId }, 'pairing code TTL expired');
    meta.status = 'disconnected';
    meta.pairingCode = undefined;
    stopPairingKeepalive(sessionId);
    return;
  }

  // Code accepted — user may be on "This may be a scam" screen; do not replace the socket
  if (progress.pairingAccepted && !progress.registered) {
    if (meta.sock) {
      return;
    }
    logger.info(
      { sessionId, waId: progress.waId },
      'pairing accepted but socket closed before Link device — waiting for restartRequired or recovery'
    );
    return;
  }

  if (meta.sock) {
    return;
  }

  if (finalizingSessions.has(sessionId)) {
    return;
  }

  logger.info(
    { sessionId, code: meta.pairingCode ? formatPairingCodeDisplay(meta.pairingCode) : null },
    'reopening socket so pairing code stays valid'
  );

  try {
    await createSocket(sessionId, meta);
  } catch (err) {
    logger.error({ sessionId, err }, 'maintainPairingSocketAlive failed');
  }
}

function wipeSessionAuth(sessionId: string): void {
  stopPairingKeepalive(sessionId);
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

export type PairingProgress = {
  registered: boolean;
  pairingAccepted: boolean;
  waId: string | null;
  pairingCodeOnDisk: string | null;
};

export async function getPairingProgress(sessionId: string): Promise<PairingProgress> {
  if (!sessionAuthExists(sessionId)) {
    return {
      registered: false,
      pairingAccepted: false,
      waId: null,
      pairingCodeOnDisk: null,
    };
  }

  try {
    const { state } = await useMultiFileAuthState(sessionPath(sessionId));
    const creds = state.creds as {
      registered?: boolean;
      me?: { id?: string };
      pairingCode?: string;
      pairingEphemeralKeyPair?: unknown;
    };

    const registered = Boolean(creds.registered);
    const waId = creds.me?.id ?? null;
    // me.id is set only after WhatsApp accepts the pairing code — not when the code is merely issued
    const pairingAccepted = Boolean(waId) && !registered;

    return {
      registered,
      pairingAccepted,
      waId,
      pairingCodeOnDisk: creds.pairingCode ?? null,
    };
  } catch {
    return {
      registered: false,
      pairingAccepted: false,
      waId: null,
      pairingCodeOnDisk: null,
    };
  }
}

export async function isAuthRegistered(sessionId: string): Promise<boolean> {
  return (await getPairingProgress(sessionId)).registered;
}

async function reconnectAfterRestart(sessionId: string, meta: SessionMeta): Promise<void> {
  if (meta.status === 'connected') {
    return;
  }

  logger.info({ sessionId, previousStatus: meta.status }, 'reconnecting after pairing (saved auth)');
  endSocket(meta);
  meta.status = 'starting';
  meta.pairingCode = undefined;

  try {
    await createSocket(sessionId, meta);
    const ok = await waitForConnected(sessionId, 90_000);
    if (!ok) {
      const progress = await getPairingProgress(sessionId);
      logger.warn(
        { sessionId, pairingAccepted: progress.pairingAccepted, registered: progress.registered },
        'reconnect after pairing did not reach connected within timeout'
      );
      if (progress.pairingAccepted && !progress.registered) {
        meta.status = 'pending_pairing';
      } else if (!progress.pairingAccepted) {
        meta.status = 'disconnected';
      }
    }
  } catch (err) {
    logger.error({ sessionId, err }, 'reconnect after pairing failed');
    const progress = await getPairingProgress(sessionId);
    meta.status = progress.pairingAccepted ? 'pending_pairing' : 'disconnected';
  }
}

export function ensureSessionMeta(sessionId: string): SessionMeta {
  let meta = sessions.get(sessionId);
  if (meta) {
    return meta;
  }

  meta = {
    sessionId,
    status: sessionAuthExists(sessionId) ? 'pending_pairing' : 'disconnected',
    pairingCode: undefined,
    linkPhone: undefined,
    phone: undefined,
    sock: undefined,
  };
  sessions.set(sessionId, meta);

  return meta;
}

/**
 * After user enters pairing code, complete the link (registered:true OR me.id in creds).
 */
export async function ensurePairingFinalized(sessionId: string): Promise<SessionMeta | undefined> {
  if (finalizingSessions.has(sessionId)) {
    return sessions.get(sessionId);
  }

  finalizingSessions.add(sessionId);

  try {
    const meta = ensureSessionMeta(sessionId);

    if (meta.status === 'connected') {
      return meta;
    }

    let progress = await getPairingProgress(sessionId);

    // User is on WhatsApp's "Link device" / scam-warning screen — keep socket open
    if (
      meta.status === 'pending_pairing' &&
      progress.pairingAccepted &&
      !progress.registered &&
      meta.sock
    ) {
      logger.info({ sessionId, waId: progress.waId }, 'awaiting user tap on Link device — not reconnecting');
      return meta;
    }

    if (meta.status === 'pending_pairing' && meta.pairingCode && !progress.pairingAccepted) {
      void maintainPairingSocketAlive(sessionId, meta);
      return meta;
    }

    if (progress.pairingAccepted && !meta.pairingAcceptedAt) {
      meta.pairingAcceptedAt = Date.now();
    }

    const needsRecovery =
      progress.registered || (progress.pairingAccepted && !meta.sock);

    if (needsRecovery) {
      stopPairingKeepalive(sessionId);
      logger.info(
        { sessionId, waId: progress.waId, registered: progress.registered, pairingAccepted: progress.pairingAccepted },
        progress.registered ? 'creds registered — finalizing connection' : 'recovering pairing after confirmation'
      );

      for (let attempt = 1; attempt <= 4; attempt++) {
        meta.status = 'pending_pairing';
        await reconnectAfterRestart(sessionId, meta);
        const connected = await waitForConnected(sessionId, 45_000);
        progress = await getPairingProgress(sessionId);
        const current = sessions.get(sessionId);
        if (connected || current?.status === 'connected') {
          logger.info({ sessionId, attempt, status: current?.status }, 'registration completed');
          break;
        }
        logger.warn({ sessionId, attempt, waId: progress.waId }, 'registration attempt did not connect — retrying');
        await sleep(3000);
      }
    } else if (meta.status === 'pending_pairing' || meta.status === 'starting') {
      void maintainPairingSocketAlive(sessionId, meta);
    }

    return sessions.get(sessionId);
  } finally {
    finalizingSessions.delete(sessionId);
  }
}

async function resolveSocketVersion(): Promise<[number, number, number]> {
  const raw = process.env.BAILEYS_VERSION?.trim();
  if (raw) {
    try {
      const parsed = JSON.parse(raw) as unknown;
      if (Array.isArray(parsed) && parsed.length === 3) {
        return [Number(parsed[0]), Number(parsed[1]), Number(parsed[2])];
      }
    } catch {
      logger.warn('BAILEYS_VERSION env invalid JSON — using fetchLatestBaileysVersion');
    }
  }

  const { version } = await fetchLatestBaileysVersion();
  return version;
}

async function createSocket(sessionId: string, meta: SessionMeta): Promise<WASocket> {
  const authPath = sessionPath(sessionId);
  fs.mkdirSync(authPath, { recursive: true });

  const { state, saveCreds } = await useMultiFileAuthState(authPath);
  const version = await resolveSocketVersion();

  const sock = makeWASocket({
    version,
    auth: state,
    logger: pino({ level: 'silent' }),
    printQRInTerminal: false,
    browser: Browsers.ubuntu('Chrome'),
    markOnlineOnConnect: false,
    syncFullHistory: false,
    connectTimeoutMs: 60_000,
    defaultQueryTimeoutMs: 60_000,
  });

  meta.sock = sock;

  sock.ev.on('creds.update', () => {
    void (async () => {
      await saveCreds();
      if (meta.status !== 'pending_pairing' && meta.status !== 'starting') {
        return;
      }

      const progress = await getPairingProgress(sessionId);
      if (progress.registered) {
        logger.info({ sessionId, waId: progress.waId }, 'creds.update: registered');
        stopPairingKeepalive(sessionId);
        if (!meta.sock) {
          setTimeout(() => void reconnectAfterRestart(sessionId, meta), 500);
        }
      } else if (progress.pairingAccepted) {
        if (!meta.pairingAcceptedAt) {
          meta.pairingAcceptedAt = Date.now();
        }
        logger.info(
          { sessionId, waId: progress.waId },
          'creds.update: code accepted — waiting for user to tap Link device (do not reconnect yet)'
        );
      }
    })();
  });

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr, isNewLogin } = update;
    const statusCode = (lastDisconnect?.error as Boom | undefined)?.output?.statusCode;

    if (isNewLogin && meta.status === 'pending_pairing') {
      logger.info({ sessionId }, 'isNewLogin during pairing — will reconnect on restartRequired close');
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
      stopPairingKeepalive(sessionId);
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

        if (restartRequired) {
          stopPairingKeepalive(sessionId);
          logger.info({ sessionId }, 'restartRequired after pairing — reconnecting to complete link');
          void reconnectAfterRestart(sessionId, meta);
          return;
        }

        // Socket must stay alive while user opens WhatsApp, enters code, and confirms Link device
        void maintainPairingSocketAlive(sessionId, meta);

        setTimeout(() => {
          void (async () => {
            if (meta.status === 'connected') {
              return;
            }
            const progress = await getPairingProgress(sessionId);
            if (progress.registered && !meta.sock) {
              logger.info({ sessionId, statusCode }, 'pairing close with registered creds — reconnecting');
              stopPairingKeepalive(sessionId);
              await reconnectAfterRestart(sessionId, meta);
            } else if (progress.pairingAccepted) {
              logger.info(
                { sessionId, statusCode, waId: progress.waId },
                'pairing accepted — keeping session open for Link device confirmation'
              );
            } else {
              void maintainPairingSocketAlive(sessionId, meta);
            }
          })();
        }, 1500);

        return;
      }

      meta.pairingCode = undefined;

      if (loggedOut) {
        meta.status = 'disconnected';
        return;
      }

      void (async () => {
        const progress = await getPairingProgress(sessionId);
        if (progress.registered) {
          meta.status = 'connected';
          logger.info({ sessionId }, 'registered session socket closed — reconnecting');
        } else {
          meta.status = 'disconnected';
        }

        if (meta.linkPhone) {
          setTimeout(() => {
            void startSessionWithPairing(sessionId, meta.linkPhone!, true);
          }, 5000);
        } else {
          setTimeout(() => {
            void startSession(sessionId);
          }, 1500);
        }
      })();
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
        { sessionId, attempt, codeLength: code.length, display: formatPairingCodeDisplay(code), pairingPhone: digits },
        'pairing code generated for phone (must match WhatsApp phone input)'
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
  meta.pairingRequestedAt = Date.now();
  startPairingKeepalive(sessionId, meta);

  const progress = await getPairingProgress(sessionId);
  const diskRaw = progress.pairingCodeOnDisk
    ? formatPairingCodeRaw(progress.pairingCodeOnDisk)
    : null;
  const issuedRaw = formatPairingCodeRaw(code);
  if (diskRaw && diskRaw !== issuedRaw) {
    logger.error(
      { sessionId, issued: formatPairingCodeDisplay(code), onDisk: progress.pairingCodeOnDisk },
      'pairing code mismatch on disk — wiping and retrying once'
    );
    wipeSessionAuth(sessionId);
    return runPairingFlow(sessionId, digits, true);
  }

  logger.info(
    {
      sessionId,
      status: meta.status,
      display: formatPairingCodeDisplay(code),
      registeredOnDisk: progress.registered,
      diskCode: progress.pairingCodeOnDisk,
    },
    'pairing code ready — open WhatsApp immediately and enter code'
  );

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

  if (digits.startsWith('20') && !/^20(10|11|12|15)\d{8}$/.test(digits)) {
    throw new Error(
      `Invalid Egypt WhatsApp number "${digits}". Use 2010xxxxxxxx (must match the SIM on this phone).`
    );
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

export function isPairingSocketAlive(sessionId: string): boolean {
  return Boolean(sessions.get(sessionId)?.sock);
}

export function isSessionStartInFlight(sessionId: string): boolean {
  return startPromises.has(sessionId);
}

export function getPairingCodeAgeSeconds(sessionId: string): number | null {
  const at = sessions.get(sessionId)?.pairingRequestedAt;
  if (!at) {
    return null;
  }
  return Math.floor((Date.now() - at) / 1000);
}

/**
 * QR linking needs a clean auth folder. Stale pairing creds prevent QR generation.
 */
export async function ensureQrLinkingSession(sessionId: string): Promise<void> {
  const meta = sessions.get(sessionId);
  if (meta?.status === 'connected') {
    return;
  }

  if (!sessionAuthExists(sessionId)) {
    return;
  }

  const progress = await getPairingProgress(sessionId);
  const blocksQr =
    progress.registered ||
    progress.pairingAccepted ||
    Boolean(progress.pairingCodeOnDisk);

  if (blocksQr) {
    logger.info({ sessionId, progress }, 'wiping stale auth before QR linking');
    await deleteSession(sessionId);
  }
}

export async function deleteSession(sessionId: string): Promise<void> {
  stopPairingKeepalive(sessionId);
  finalizingSessions.delete(sessionId);

  const inFlight = startPromises.get(sessionId);
  if (inFlight) {
    try {
      await Promise.race([inFlight, sleep(5000)]);
    } catch {
      // pairing flow aborted by delete
    }
  }
  startPromises.delete(sessionId);

  const meta = sessions.get(sessionId);
  if (meta) {
    endSocket(meta);
    try {
      await Promise.race([
        meta.sock?.logout() ?? Promise.resolve(),
        sleep(2000),
      ]);
    } catch {
      // ignore
    }
  }
  sessions.delete(sessionId);

  const dir = sessionPath(sessionId);
  if (fs.existsSync(dir)) {
    fs.rmSync(dir, { recursive: true, force: true });
  }

  if (sessionAuthExists(sessionId)) {
    logger.warn({ sessionId }, 'session dir still present after delete — forcing remove');
    fs.rmSync(dir, { recursive: true, force: true });
  }

  logger.info({ sessionId }, 'session deleted');
}
