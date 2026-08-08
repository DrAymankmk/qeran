import makeWASocket, {
  Browsers,
  DisconnectReason,
  fetchLatestBaileysVersion,
  fetchLatestWaWebVersion,
  useMultiFileAuthState,
  type ConnectionState,
  type WASocket,
} from '@whiskeysockets/baileys';
import { attachMessageReceiptListener } from './receipts.js';
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
  | 'starting'
  | 'reconnecting';

export interface SessionMeta {
  sessionId: string;
  status: SessionStatus;
  qr?: string;
  pairingCode?: string;
  linkPhone?: string;
  phone?: string;
  sock?: WASocket;
  pairingRequestedAt?: number;
  /** Set when WhatsApp pair-success fires (user entered code on phone) — not when requestPairingCode runs. */
  pairingConfirmedByPhone?: boolean;
  /** Set when phone confirms pairing (pair-success) — user may still need to tap "Link device". */
  pairingAcceptedAt?: number;
  /** When the current QR string was issued (do not wipe session while user may be scanning). */
  qrGeneratedAt?: number;
  /** Failed reconnect attempts after pairing/QR interrupt (stops infinite 401 loops). */
  reconnectFailures?: number;
  lastReconnectAt?: number;
  /** Unix ms when the socket last reached `open` (for uptime reporting). */
  connectedAt?: number;
  /** Bound to the active socket auth state — use to flush creds.json after pairing code issue. */
  saveCreds?: () => Promise<void>;
}

const sessions = new Map<string, SessionMeta>();
const startPromises = new Map<string, Promise<SessionMeta>>();
const finalizingSessions = new Set<string>();
const pairingKeepaliveTimers = new Map<string, ReturnType<typeof setInterval>>();
/** Blocks pairing keepalive/reconnect until the user starts a new link (POST /sessions). */
const abortedSessions = new Set<string>();

const SYSTEM_SESSION_ID = (process.env.BAILEYS_SYSTEM_SESSION ?? 'system').trim();

export function isSystemSession(sessionId: string): boolean {
  return sessionId === SYSTEM_SESSION_ID;
}

export function isSessionAborted(sessionId: string): boolean {
  return abortedSessions.has(sessionId);
}

export function clearSessionAbort(sessionId: string): void {
  abortedSessions.delete(sessionId);
}

export function abortSession(sessionId: string): void {
  abortedSessions.add(sessionId);
}

export function isLinkedOnWhatsApp(
  status: SessionStatus,
  socketAlive: boolean,
  registered: boolean
): boolean {
  return status === 'connected' && socketAlive && registered;
}

/** True only when status is connected AND the Baileys WebSocket is still open. */
export function isSessionLiveConnected(meta: SessionMeta | undefined): boolean {
  return meta?.status === 'connected' && Boolean(meta.sock);
}

export function disconnectedStatusPayload(sessionId: string): Record<string, unknown> {
  return {
    sessionId,
    status: 'disconnected' as SessionStatus,
    phone: null,
    pairingCode: null,
    registeredOnDisk: false,
    pairingAccepted: false,
    pairingProgress: 'awaiting_code',
    linkedOnWhatsApp: false,
    waId: null,
    socketAlive: false,
    reconnecting: false,
    unlinked: true,
    pairingCodeAgeSeconds: null,
  };
}

const PAIRING_READY_DELAY_MS = Number(process.env.PAIRING_READY_DELAY_MS ?? 1500);
const PAIRING_KEEPALIVE_MS = Number(process.env.PAIRING_KEEPALIVE_MS ?? 15_000);
const PAIRING_CODE_TTL_MS = Number(process.env.PAIRING_CODE_TTL_MS ?? 300_000);
/** Wait for user to tap "Link device" on WhatsApp before forcing reconnect to complete registration. */
const PAIRING_LINK_DEVICE_WAIT_MS = Number(process.env.PAIRING_LINK_DEVICE_WAIT_MS ?? 25_000);
const PAIRING_SOCKET_READY_TIMEOUT_MS = Number(process.env.PAIRING_SOCKET_READY_TIMEOUT_MS ?? 25_000);
const PAIRING_CODE_MAX_ATTEMPTS = Number(process.env.PAIRING_CODE_MAX_ATTEMPTS ?? 3);
/** WhatsApp 405 = client_too_old (stale WA version or WEB platform rejected). */
const WA_CLIENT_TOO_OLD_STATUS = 405;
/** 0 = never auto-wipe on reconnect failure (recommended). Set e.g. 12 to wipe after N failures. */
const RECONNECT_WIPE_THRESHOLD = Number(process.env.RECONNECT_WIPE_THRESHOLD ?? 0);
const RECONNECT_BACKOFF_MS = (process.env.RECONNECT_BACKOFF_MS ?? '5000,30000,120000')
  .split(',')
  .map((s) => Number(s.trim()))
  .filter((n) => Number.isFinite(n) && n > 0);
const RECONNECT_BACKOFF_SCHEDULE =
  RECONNECT_BACKOFF_MS.length > 0 ? RECONNECT_BACKOFF_MS : [30_000, 120_000, 600_000];
/** Periodic check for registered sessions with a dead socket (0 = disabled). */
const CONNECTED_SESSION_WATCHDOG_MS = Number(process.env.CONNECTED_SESSION_WATCHDOG_MS ?? 30_000);
/** Keep-alive ping for connected sockets (0 = disabled). Prevents idle linked-device drops. */
const CONNECTED_SESSION_HEARTBEAT_MS = Number(process.env.CONNECTED_SESSION_HEARTBEAT_MS ?? 45_000);
/** Retry reconnect before wiping creds on loggedOut (some servers send transient 401). */
const LOGGED_OUT_RECONNECT_ATTEMPTS = Number(process.env.LOGGED_OUT_RECONNECT_ATTEMPTS ?? 2);
/** Wipe client pairing sessions stuck at code-accepted but never registered (prevents infinite reconnect loops). */
const PAIRING_STUCK_WIPE_FAILURES = Number(process.env.PAIRING_STUCK_WIPE_FAILURES ?? 12);
/** Max age (ms) for pairing-accepted creds that never reach registered:true before watchdog wipe. */
const PAIRING_STUCK_MAX_AGE_MS = Number(process.env.PAIRING_STUCK_MAX_AGE_MS ?? 1_800_000);
/**
 * When true (default), OTP/system creds are only deleted on admin Disconnect or confirmed
 * WhatsApp loggedOut (Linked devices removed). Never for reconnect timeouts or Bad MAC.
 */
const SYSTEM_PROTECT_AUTO_WIPE = process.env.SYSTEM_PROTECT_AUTO_WIPE !== 'false';
const SYSTEM_LOGGED_OUT_RECONNECT_ATTEMPTS = Number(
  process.env.SYSTEM_LOGGED_OUT_RECONNECT_ATTEMPTS ?? 5
);
/** Refresh QR socket before WhatsApp idle timeout (408) while admin is scanning. */
const QR_KEEPALIVE_MS = Number(process.env.QR_KEEPALIVE_MS ?? 40_000);
const QR_RECONNECT_MIN_GAP_MS = Number(process.env.QR_RECONNECT_MIN_GAP_MS ?? 4000);

type WipeReason =
  | 'admin'
  | 'logged_out'
  | 'incomplete_link'
  | 'reconnect_failed'
  | 'pairing_mismatch'
  | 'stale';

const scheduledReconnects = new Set<string>();
const reconnectPromises = new Map<string, Promise<void>>();
const pairingReconnectPromises = new Map<string, Promise<void>>();
let cachedBaileysVersion: [number, number, number] | null = null;
let baileysVersionFetchPromise: Promise<[number, number, number]> | null = null;
const connectedHeartbeatTimers = new Map<string, ReturnType<typeof setInterval>>();
const qrKeepaliveTimers = new Map<string, ReturnType<typeof setInterval>>();
const loggedOutReconnectAttempts = new Map<string, number>();
let connectedSessionWatchdogTimer: ReturnType<typeof setInterval> | undefined;
let connectedSessionWatchdogTickInFlight = false;

export function isLinkingInProgress(sessionId: string): boolean {
  const meta = sessions.get(sessionId);
  if (!meta) {
    return false;
  }

  if (isPhonePairingFlow(meta)) {
    if (meta.pairingCode) {
      return true;
    }
    if (meta.pairingRequestedAt && Date.now() - meta.pairingRequestedAt < PAIRING_CODE_TTL_MS) {
      return true;
    }
  }

  if (startPromises.has(sessionId) || finalizingSessions.has(sessionId)) {
    return true;
  }

  if (meta.status === 'pending_pairing') {
    if (meta.pairingCode) {
      return true;
    }
    if (meta.pairingRequestedAt && Date.now() - meta.pairingRequestedAt < PAIRING_CODE_TTL_MS) {
      return true;
    }
    return false;
  }

  if (meta.status === 'pending_qr') {
    if (meta.qr && meta.qrGeneratedAt && Date.now() - meta.qrGeneratedAt < 120_000) {
      return true;
    }
    return false;
  }

  if (meta.status === 'starting') {
    if (meta.pairingCode) {
      return true;
    }
    if (meta.pairingRequestedAt && Date.now() - meta.pairingRequestedAt < PAIRING_CODE_TTL_MS) {
      return true;
    }
    return false;
  }

  if (meta.qr && meta.qrGeneratedAt && Date.now() - meta.qrGeneratedAt < 120_000) {
    return true;
  }

  return false;
}

/** Client mobile pairing (linkPhone) — must never be treated as admin QR linking. */
function isPhonePairingFlow(meta: SessionMeta): boolean {
  return Boolean(meta.linkPhone) || meta.status === 'pending_pairing';
}

/**
 * Drop orphan sockets / partial auth folders that never completed registration.
 * Returns true when stale state was removed.
 */
export async function cleanupStaleUnregisteredSession(sessionId: string): Promise<boolean> {
  if (isSessionAborted(sessionId) || isLinkingInProgress(sessionId)) {
    return false;
  }

  const meta = sessions.get(sessionId);
  if (meta && isPhonePairingFlow(meta)) {
    return false;
  }

  const progress = await getPairingProgress(sessionId);
  if (progress.registered || progress.pairingAccepted) {
    return false;
  }

  const hasLiveSocket = Boolean(meta?.sock);
  const hasAuthFolder = sessionAuthExists(sessionId);

  if (!hasLiveSocket && !hasAuthFolder) {
    return false;
  }

  logger.info(
    { sessionId, status: meta?.status, hasLiveSocket, hasAuthFolder },
    'cleaning stale unregistered WhatsApp session'
  );

  if (hasAuthFolder) {
    wipeSessionAuth(sessionId, 'stale');
  } else if (meta) {
    clearStaleSessionMeta(sessionId);
  }

  return true;
}

/**
 * Client pairing sessions can get stuck at pairingAccepted + !registered forever
 * (user never tapped "Link device"). They hammer reconnect and cause Bad MAC noise.
 */
export async function cleanupStuckPairingSession(sessionId: string): Promise<boolean> {
  if (isSystemSession(sessionId) || isSessionAborted(sessionId)) {
    return false;
  }

  if (!sessionAuthExists(sessionId)) {
    return false;
  }

  const progress = await getPairingProgress(sessionId);
  if (progress.registered || !progress.pairingAccepted) {
    return false;
  }

  const meta = sessions.get(sessionId);
  const failures = meta?.reconnectFailures ?? 0;
  const credsPath = path.join(sessionPath(sessionId), 'creds.json');
  const credsAgeMs = fs.existsSync(credsPath) ? Date.now() - fs.statSync(credsPath).mtimeMs : 0;
  const pairingAgeMs = meta?.pairingAcceptedAt ? Date.now() - meta.pairingAcceptedAt : credsAgeMs;

  if (failures < PAIRING_STUCK_WIPE_FAILURES && pairingAgeMs < PAIRING_STUCK_MAX_AGE_MS) {
    return false;
  }

  logger.warn(
    { sessionId, failures, pairingAgeMs, waId: progress.waId },
    'wiping stuck pairing session (code accepted but never registered)'
  );
  wipeSessionAuth(sessionId, 'stale');
  if (meta) {
    meta.status = 'disconnected';
    meta.reconnectFailures = 0;
  }

  return true;
}

function shouldWipeStuckPairing(
  sessionId: string,
  progress: PairingProgress,
  failures: number,
  meta: SessionMeta
): boolean {
  if (isSystemSession(sessionId) || progress.registered || !progress.pairingAccepted) {
    return false;
  }

  const credsPath = path.join(sessionPath(sessionId), 'creds.json');
  const credsAgeMs = fs.existsSync(credsPath) ? Date.now() - fs.statSync(credsPath).mtimeMs : 0;
  const pairingAgeMs = meta.pairingAcceptedAt ? Date.now() - meta.pairingAcceptedAt : credsAgeMs;

  return failures >= PAIRING_STUCK_WIPE_FAILURES || pairingAgeMs >= PAIRING_STUCK_MAX_AGE_MS;
}

function sessionsDir(): string {
  const raw = process.env.SESSIONS_DIR?.trim() || path.join(process.cwd(), 'sessions');

  return path.isAbsolute(raw) ? raw : path.resolve(process.cwd(), raw);
}

export function getSessionsDirPath(): string {
  return sessionsDir();
}

/** Ensure the sessions root exists and is writable (call once at gateway startup). */
export function ensureSessionsDir(): void {
  const dir = sessionsDir();
  fs.mkdirSync(dir, { recursive: true });

  try {
    fs.accessSync(dir, fs.constants.W_OK);
  } catch {
    logger.error({ dir }, 'SESSIONS_DIR is not writable — pairing/QR auth files cannot be saved');
  }
}

/** List session folder names that have creds.json on disk. */
export function listPersistedSessionIds(): string[] {
  const root = sessionsDir();
  if (!fs.existsSync(root)) {
    return [];
  }

  return fs
    .readdirSync(root, { withFileTypes: true })
    .filter((entry) => entry.isDirectory())
    .map((entry) => entry.name)
    .filter((name) => fs.existsSync(path.join(root, name, 'creds.json')));
}

function sessionPath(sessionId: string): string {
  const safe = sessionId.replace(/[^a-zA-Z0-9_-]/g, '_');

  return path.resolve(sessionsDir(), safe);
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
 * Keep the original pairing socket alive while the user enters the code.
 * Never recreate the socket after requestPairingCode — WhatsApp binds the code to that session's keys.
 */
async function maintainPairingSocketAlive(sessionId: string, meta: SessionMeta): Promise<void> {
  if (isSessionAborted(sessionId)) {
    return;
  }

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

  // Code accepted — user may be on "This may be a scam" screen; reconnect if socket dropped (515 expected)
  if (progress.pairingAccepted && !progress.registered) {
    if (meta.sock) {
      return;
    }
    logger.info(
      { sessionId, waId: progress.waId },
      'pairing accepted but socket closed — reconnecting to complete Link device'
    );
    await reconnectAfterRestart(sessionId, meta);
    return;
  }

  // Awaiting code entry — reconnect with saved creds if socket dropped (code is bound to creds, not TCP)
  if (meta.pairingCode && !progress.pairingAccepted) {
    if (meta.sock) {
      try {
        await meta.sock.sendPresenceUpdate('available');
      } catch {
        // ignore — will reconnect on next tick
      }
      return;
    }

    await reconnectPairingAwaitingCode(sessionId, meta);
    return;
  }

  if (meta.sock) {
    return;
  }

  if (finalizingSessions.has(sessionId)) {
    return;
  }

  logger.info({ sessionId }, 'pairing socket missing after code accepted — recovering');
  await reconnectAfterRestart(sessionId, meta);
}

function stopConnectedHeartbeat(sessionId: string): void {
  const timer = connectedHeartbeatTimers.get(sessionId);
  if (timer) {
    clearInterval(timer);
    connectedHeartbeatTimers.delete(sessionId);
  }
}

function startConnectedHeartbeat(sessionId: string): void {
  stopConnectedHeartbeat(sessionId);

  if (CONNECTED_SESSION_HEARTBEAT_MS <= 0) {
    return;
  }

  const timer = setInterval(() => {
    void (async () => {
      const meta = sessions.get(sessionId);
      if (!meta?.sock || meta.status !== 'connected') {
        return;
      }

      try {
        await meta.sock.sendPresenceUpdate('available');
      } catch (err) {
        logger.warn({ sessionId, err }, 'connected session heartbeat failed');
      }
    })();
  }, CONNECTED_SESSION_HEARTBEAT_MS);

  connectedHeartbeatTimers.set(sessionId, timer);
}

function stopQrKeepalive(sessionId: string): void {
  const timer = qrKeepaliveTimers.get(sessionId);
  if (timer) {
    clearInterval(timer);
    qrKeepaliveTimers.delete(sessionId);
  }
}

function startQrKeepalive(sessionId: string): void {
  stopQrKeepalive(sessionId);

  if (QR_KEEPALIVE_MS <= 0) {
    return;
  }

  const timer = setInterval(() => {
    void (async () => {
      const meta = sessions.get(sessionId);
      if (!meta || meta.status !== 'pending_qr') {
        stopQrKeepalive(sessionId);
        return;
      }

      const progress = await getPairingProgress(sessionId);
      if (progress.registered) {
        stopQrKeepalive(sessionId);
        return;
      }

      if (meta.sock && isPairingSocketAlive(sessionId)) {
        return;
      }

      logger.info({ sessionId }, 'QR keepalive: socket down — refreshing for scan');
      await reconnectForQrLinking(sessionId, meta);
    })();
  }, QR_KEEPALIVE_MS);

  qrKeepaliveTimers.set(sessionId, timer);
}

/**
 * While waiting for QR scan: refresh the socket without reconnect-failure backoff.
 * Using reconnectAfterRestart here waits for connected, increments failures, and
 * invalidates the QR every ~2 minutes (408 timeout).
 */
async function reconnectForQrLinkingImpl(sessionId: string, meta: SessionMeta): Promise<void> {
  if (isSessionLiveConnected(meta) || isSessionAborted(sessionId)) {
    return;
  }

  const progress = await getPairingProgress(sessionId);
  if (progress.registered) {
    await reconnectAfterRestart(sessionId, meta);
    return;
  }

  const now = Date.now();
  if (meta.lastReconnectAt && now - meta.lastReconnectAt < QR_RECONNECT_MIN_GAP_MS) {
    return;
  }
  meta.lastReconnectAt = now;

  logger.info({ sessionId, status: meta.status }, 'QR link: refreshing socket (waiting for scan)');
  endSocket(meta);
  meta.status = 'pending_qr';
  meta.pairingCode = undefined;

  try {
    await createSocket(sessionId, meta);
    const updated = await waitForQrOrConnected(sessionId, 60_000);
    if (updated.status === 'connected') {
      meta.reconnectFailures = 0;
      stopQrKeepalive(sessionId);
      loggedOutReconnectAttempts.delete(sessionId);
    } else {
      meta.status = 'pending_qr';
      startQrKeepalive(sessionId);
      logger.info({ sessionId, hasQr: Boolean(updated.qr) }, 'QR link: ready for scan');
    }
  } catch (err) {
    logger.warn({ sessionId, err }, 'QR link socket refresh failed');
    meta.status = 'pending_qr';
  }
}

async function reconnectForQrLinking(sessionId: string, meta: SessionMeta): Promise<void> {
  const inFlight = reconnectPromises.get(sessionId);
  if (inFlight) {
    return inFlight;
  }

  const promise = reconnectForQrLinkingImpl(sessionId, meta);
  reconnectPromises.set(sessionId, promise);

  try {
    await promise;
  } finally {
    reconnectPromises.delete(sessionId);
  }
}

function wipeSessionAuth(sessionId: string, reason: WipeReason = 'stale'): void {
  if (isSystemSession(sessionId) && SYSTEM_PROTECT_AUTO_WIPE) {
    const allowed =
      reason === 'admin' || reason === 'logged_out' || reason === 'incomplete_link';
    if (!allowed) {
      logger.warn(
        { sessionId, reason },
        'system OTP session protected — auto-wipe skipped (use admin Disconnect or remove Linked device)'
      );
      return;
    }
  }

  scheduledReconnects.delete(sessionId);
  stopPairingKeepalive(sessionId);
  stopConnectedHeartbeat(sessionId);
  stopQrKeepalive(sessionId);
  loggedOutReconnectAttempts.delete(sessionId);
  const meta = sessions.get(sessionId);
  if (meta) {
    endSocket(meta);
    sessions.delete(sessionId);
  }
  const dir = sessionPath(sessionId);
  if (fs.existsSync(dir)) {
    fs.rmSync(dir, { recursive: true, force: true });
  }
  logger.info({ sessionId, reason }, 'session auth wiped');
}

function reconnectBackoffMs(failures: number): number {
  const idx = Math.min(Math.max(0, failures - 1), RECONNECT_BACKOFF_SCHEDULE.length - 1);
  return RECONNECT_BACKOFF_SCHEDULE[idx]!;
}

function scheduleReconnectAfterRestart(sessionId: string, delayMs: number): void {
  if (isSessionAborted(sessionId) || scheduledReconnects.has(sessionId)) {
    return;
  }

  scheduledReconnects.add(sessionId);
  logger.info({ sessionId, delayMs }, 'scheduled reconnect after backoff');

  setTimeout(() => {
    scheduledReconnects.delete(sessionId);
    const meta = sessions.get(sessionId);
    if (!meta || isSessionLiveConnected(meta) || isSessionAborted(sessionId)) {
      return;
    }
    void reconnectAfterRestart(sessionId, meta);
  }, delayMs);
}

/** Clear in-memory session state without logging out or deleting disk credentials. */
export function clearStaleSessionMeta(sessionId: string): void {
  scheduledReconnects.delete(sessionId);
  stopPairingKeepalive(sessionId);
  stopConnectedHeartbeat(sessionId);
  stopQrKeepalive(sessionId);
  finalizingSessions.delete(sessionId);
  startPromises.delete(sessionId);
  const meta = sessions.get(sessionId);
  if (meta) {
    endSocket(meta);
  }
  sessions.delete(sessionId);
}

export function isSessionReconnecting(sessionId: string): boolean {
  if (isSessionStartInFlight(sessionId)) {
    return true;
  }
  if (scheduledReconnects.has(sessionId)) {
    return true;
  }
  const meta = sessions.get(sessionId);
  if (!meta) {
    return false;
  }
  return meta.status === 'starting' || (meta.reconnectFailures ?? 0) > 0;
}

/** Restore registered sessions from disk after gateway restart (PM2). */
export async function restorePersistedSessions(): Promise<void> {
  const dir = sessionsDir();
  if (!fs.existsSync(dir)) {
    return;
  }

  const toRestore: string[] = [];

  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    if (!entry.isDirectory()) {
      continue;
    }

    const sessionId = entry.name;
    if (!sessionAuthExists(sessionId) || isSessionAborted(sessionId)) {
      continue;
    }

    try {
      const progress = await getPairingProgress(sessionId);
      if (!progress.registered) {
        continue;
      }

      toRestore.push(sessionId);
    } catch (err) {
      logger.warn({ sessionId, err }, 'skipped startup restore for session');
    }
  }

  if (toRestore.length === 0) {
    return;
  }

  toRestore.sort((a, b) => {
    if (isSystemSession(a)) {
      return -1;
    }
    if (isSystemSession(b)) {
      return 1;
    }

    return a.localeCompare(b);
  });

  for (const sessionId of toRestore) {
    try {
      logger.info({ sessionId }, 'restoring persisted WhatsApp session on startup');
      await startSession(sessionId);
    } catch (err) {
      logger.warn({ sessionId, err }, 'startup session restore failed');
    }
  }
}

function collectKnownSessionIds(): string[] {
  const ids = new Set<string>();

  for (const sessionId of sessions.keys()) {
    ids.add(sessionId);
  }

  const dir = sessionsDir();
  if (fs.existsSync(dir)) {
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
      if (entry.isDirectory()) {
        ids.add(entry.name);
      }
    }
  }

  return [...ids];
}

async function recoverRegisteredSessionIfSocketDown(sessionId: string): Promise<void> {
  if (isSessionAborted(sessionId) || !sessionAuthExists(sessionId)) {
    return;
  }

  if (isSessionStartInFlight(sessionId) || scheduledReconnects.has(sessionId) || finalizingSessions.has(sessionId)) {
    return;
  }

  const progress = await getPairingProgress(sessionId);
  if (!progress.registered) {
    return;
  }

  const meta = ensureSessionMeta(sessionId);
  if (isSessionLiveConnected(meta)) {
    return;
  }

  if (meta.status === 'pending_pairing' && progress.pairingAccepted && !progress.registered) {
    return;
  }

  logger.info({ sessionId, status: meta.status }, 'watchdog: registered session socket down — reconnecting');
  void reconnectAfterRestart(sessionId, meta);
}

async function runConnectedSessionWatchdog(): Promise<void> {
  if (connectedSessionWatchdogTickInFlight) {
    return;
  }

  connectedSessionWatchdogTickInFlight = true;

  try {
    for (const sessionId of collectKnownSessionIds()) {
      try {
        await cleanupStaleUnregisteredSession(sessionId);
        await cleanupStuckPairingSession(sessionId);
        await recoverRegisteredSessionIfSocketDown(sessionId);
      } catch (err) {
        logger.warn({ sessionId, err }, 'watchdog session probe failed');
      }
    }
  } finally {
    connectedSessionWatchdogTickInFlight = false;
  }
}

/** Start periodic recovery for registered sessions whose Baileys socket dropped. */
export function startConnectedSessionWatchdog(): void {
  if (CONNECTED_SESSION_WATCHDOG_MS <= 0) {
    logger.info('connected session watchdog disabled (CONNECTED_SESSION_WATCHDOG_MS <= 0)');
    return;
  }

  if (connectedSessionWatchdogTimer) {
    return;
  }

  logger.info({ intervalMs: CONNECTED_SESSION_WATCHDOG_MS }, 'starting connected session watchdog');

  connectedSessionWatchdogTimer = setInterval(() => {
    void runConnectedSessionWatchdog().catch((err) => {
      logger.error({ err }, 'connected session watchdog tick failed');
    });
  }, CONNECTED_SESSION_WATCHDOG_MS);

  setTimeout(() => {
    void runConnectedSessionWatchdog().catch((err) => {
      logger.error({ err }, 'connected session watchdog initial probe failed');
    });
  }, 10_000);
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
      const { connection } = update;

      if (connection === 'open' || connection === 'connecting') {
        cleanup();
        resolve();
        return;
      }

      if (connection === 'close') {
        const code = (update.lastDisconnect?.error as Boom | undefined)?.output?.statusCode;
        cleanup();
        if (code === DisconnectReason.loggedOut) {
          reject(new Error('WhatsApp logged out this session'));
          return;
        }
        reject(new Error(`Connection closed before pairing ready (${code ?? 'unknown'})`));
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

/** True after pair-success on the phone — NOT when requestPairingCode pre-fills creds.me. */
function isPairingConfirmedOnPhone(
  creds: { registered?: boolean; account?: unknown },
  meta?: SessionMeta
): boolean {
  if (creds.registered) {
    return false;
  }

  return Boolean(creds.account) || Boolean(meta?.pairingConfirmedByPhone);
}

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
    const meta = sessions.get(sessionId);
    const creds = state.creds as {
      registered?: boolean;
      me?: { id?: string };
      pairingCode?: string;
      pairingEphemeralKeyPair?: unknown;
      account?: unknown;
    };

    const registered = Boolean(creds.registered);
    const waId = creds.me?.id ?? null;
    // requestPairingCode() always sets me.id — only creds.account (pair-success) means the phone accepted the code.
    const pairingAccepted = isPairingConfirmedOnPhone(creds, meta);

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

async function reconnectPairingAwaitingCodeImpl(sessionId: string, meta: SessionMeta): Promise<void> {
  if (isSessionAborted(sessionId)) {
    return;
  }

  const progress = await getPairingProgress(sessionId);
  if (progress.registered) {
    await reconnectAfterRestart(sessionId, meta);
    return;
  }

  if (progress.pairingAccepted) {
    await reconnectAfterRestart(sessionId, meta);
    return;
  }

  const codeOnDisk = progress.pairingCodeOnDisk;
  if (!meta.pairingCode && codeOnDisk) {
    meta.pairingCode = formatPairingCodeRaw(codeOnDisk);
  }

  if (!meta.pairingCode && !codeOnDisk) {
    logger.warn({ sessionId }, 'cannot reconnect pairing — no code on disk');
    return;
  }

  const now = Date.now();
  const minGap = 2000;
  if (meta.lastReconnectAt && now - meta.lastReconnectAt < minGap) {
    await sleep(minGap - (now - meta.lastReconnectAt));
  }
  meta.lastReconnectAt = Date.now();

  logger.info(
    {
      sessionId,
      code: meta.pairingCode ? formatPairingCodeDisplay(meta.pairingCode) : null,
    },
    'reconnecting pairing socket with saved creds (awaiting code entry on phone)'
  );

  endSocket(meta);
  meta.status = 'pending_pairing';
  meta.linkPhone = meta.linkPhone ?? progress.waId?.split('@')[0]?.replace(/\D/g, '') ?? undefined;

  try {
    await createSocket(sessionId, meta);
  } catch (err) {
    logger.error({ sessionId, err }, 'reconnectPairingAwaitingCode failed');
  }
}

/** Reopen socket with existing pairing creds — safe while user enters code (does not wipe keys). */
async function reconnectPairingAwaitingCode(sessionId: string, meta: SessionMeta): Promise<void> {
  const inFlight = pairingReconnectPromises.get(sessionId);
  if (inFlight) {
    return inFlight;
  }

  const promise = reconnectPairingAwaitingCodeImpl(sessionId, meta);
  pairingReconnectPromises.set(sessionId, promise);

  try {
    await promise;
  } finally {
    pairingReconnectPromises.delete(sessionId);
  }
}

async function reconnectAfterRestartImpl(sessionId: string, meta: SessionMeta): Promise<void> {
  if (isSessionLiveConnected(meta)) {
    return;
  }

  if (isSessionAborted(sessionId)) {
    return;
  }

  const failures = meta.reconnectFailures ?? 0;
  const minGap = failures === 0 ? 2500 : reconnectBackoffMs(failures);
  const now = Date.now();
  if (meta.lastReconnectAt && now - meta.lastReconnectAt < minGap) {
    scheduleReconnectAfterRestart(sessionId, minGap - (now - meta.lastReconnectAt));
    return;
  }
  meta.lastReconnectAt = now;

  const progressBefore = await getPairingProgress(sessionId);

  if (
    !progressBefore.registered &&
    !progressBefore.pairingAccepted &&
    !meta.linkPhone &&
    (meta.status === 'pending_qr' || meta.status === 'starting')
  ) {
    await reconnectForQrLinking(sessionId, meta);
    return;
  }

  if (isSystemSession(sessionId) && progressBefore.pairingAccepted && !progressBefore.registered) {
    const wasQr = meta.status === 'pending_qr' || meta.status === 'starting';
    if (!wasQr) {
      logger.error(
        { sessionId, progress: progressBefore },
        'system session has pairing-code creds — wiping (admin OTP must use QR only)'
      );
      wipeSessionAuth(sessionId, 'incomplete_link');
      meta.status = 'disconnected';
      return;
    }
  }

  logger.info(
    {
      sessionId,
      previousStatus: meta.status,
      pairingAccepted: progressBefore.pairingAccepted,
      registered: progressBefore.registered,
    },
    progressBefore.registered
      ? 'reconnecting registered session (saved auth on disk)'
      : 'reconnecting after link interrupt (saved auth)'
  );
  endSocket(meta);
  const phonePairingAwaitingCode =
    Boolean(meta.linkPhone) &&
    !progressBefore.registered &&
    !progressBefore.pairingAccepted &&
    Boolean(meta.pairingCode || progressBefore.pairingCodeOnDisk);

  if (phonePairingAwaitingCode) {
    meta.status = 'pending_pairing';
    if (!meta.pairingCode && progressBefore.pairingCodeOnDisk) {
      meta.pairingCode = formatPairingCodeRaw(progressBefore.pairingCodeOnDisk);
    }
  } else if (progressBefore.registered) {
    meta.status = 'starting';
    meta.pairingCode = undefined;
  } else if (meta.status === 'pending_qr') {
    meta.status = 'pending_qr';
    meta.pairingCode = undefined;
  } else if (progressBefore.pairingAccepted && !progressBefore.registered) {
    meta.status = isSystemSession(sessionId) ? 'pending_qr' : 'pending_pairing';
    meta.pairingCode = undefined;
  } else if (meta.linkPhone) {
    meta.status = 'pending_pairing';
  } else {
    meta.status = 'starting';
    meta.pairingCode = undefined;
  }

  try {
    await createSocket(sessionId, meta);

    if (phonePairingAwaitingCode) {
      logger.info({ sessionId }, 'pairing socket reopened — waiting for user to enter code on phone');
      return;
    }

    const ok = await waitForConnected(sessionId, isSystemSession(sessionId) ? 120_000 : 90_000);
    if (!ok) {
      const progress = await getPairingProgress(sessionId);
      meta.reconnectFailures = failures + 1;

      logger.warn(
        {
          sessionId,
          pairingAccepted: progress.pairingAccepted,
          registered: progress.registered,
          failures: meta.reconnectFailures,
        },
        'reconnect after link did not reach connected within timeout'
      );

      if (shouldWipeStuckPairing(sessionId, progress, meta.reconnectFailures, meta)) {
        logger.error(
          { sessionId, failures: meta.reconnectFailures },
          'stuck pairing session — wiping to stop reconnect loop'
        );
        wipeSessionAuth(sessionId, 'reconnect_failed');
        meta.status = 'disconnected';
        meta.reconnectFailures = 0;
        return;
      }

      if (RECONNECT_WIPE_THRESHOLD > 0 && meta.reconnectFailures >= RECONNECT_WIPE_THRESHOLD) {
        logger.error(
          { sessionId, failures: meta.reconnectFailures, threshold: RECONNECT_WIPE_THRESHOLD },
          'too many reconnect failures — wiping session'
        );
        wipeSessionAuth(sessionId, 'reconnect_failed');
        meta.status = 'disconnected';
        meta.reconnectFailures = 0;
        return;
      }

      if (progress.registered) {
        meta.status = 'starting';
      } else if (progress.pairingAccepted && !progress.registered) {
        meta.status = isSystemSession(sessionId) ? 'pending_qr' : 'pending_pairing';
      } else {
        meta.status = 'disconnected';
      }

      scheduleReconnectAfterRestart(sessionId, reconnectBackoffMs(meta.reconnectFailures));
    } else {
      meta.reconnectFailures = 0;
      loggedOutReconnectAttempts.delete(sessionId);
    }
  } catch (err) {
    logger.error({ sessionId, err }, 'reconnect after link failed');
    const progress = await getPairingProgress(sessionId);
    meta.reconnectFailures = failures + 1;
    if (shouldWipeStuckPairing(sessionId, progress, meta.reconnectFailures, meta)) {
      wipeSessionAuth(sessionId, 'reconnect_failed');
      meta.status = 'disconnected';
      meta.reconnectFailures = 0;
    } else if (RECONNECT_WIPE_THRESHOLD > 0 && meta.reconnectFailures >= RECONNECT_WIPE_THRESHOLD) {
      wipeSessionAuth(sessionId, 'reconnect_failed');
      meta.status = 'disconnected';
      meta.reconnectFailures = 0;
    } else if (progress.registered) {
      meta.status = 'starting';
      scheduleReconnectAfterRestart(sessionId, reconnectBackoffMs(meta.reconnectFailures));
    } else if (progress.pairingAccepted && !progress.registered) {
      meta.status = isSystemSession(sessionId) ? 'pending_qr' : 'pending_pairing';
      scheduleReconnectAfterRestart(sessionId, reconnectBackoffMs(meta.reconnectFailures));
    } else {
      meta.status = 'disconnected';
      scheduleReconnectAfterRestart(sessionId, reconnectBackoffMs(meta.reconnectFailures));
    }
  }
}

/** Serialize reconnect so Laravel status polls and the watchdog cannot race (Bad MAC). */
async function reconnectAfterRestart(sessionId: string, meta: SessionMeta): Promise<void> {
  const inFlight = reconnectPromises.get(sessionId);
  if (inFlight) {
    return inFlight;
  }

  const promise = reconnectAfterRestartImpl(sessionId, meta);
  reconnectPromises.set(sessionId, promise);

  try {
    await promise;
  } finally {
    reconnectPromises.delete(sessionId);
  }
}

export function ensureSessionMeta(sessionId: string): SessionMeta {
  let meta = sessions.get(sessionId);
  if (meta) {
    return meta;
  }

  meta = {
    sessionId,
    status: (() => {
      if (!sessionAuthExists(sessionId)) {
        return 'disconnected' as const;
      }
      if (isSystemSession(sessionId)) {
        return 'starting' as const;
      }
      // Saved creds on disk — socket must be restored, not a fresh pairing flow.
      return 'starting' as const;
    })(),
    pairingCode: undefined,
    linkPhone: undefined,
    phone: undefined,
    sock: undefined,
  };
  sessions.set(sessionId, meta);

  return meta;
}

/**
 * After user enters pairing code on phone (pair-success / creds.account), complete registration.
 */
export async function ensurePairingFinalized(sessionId: string): Promise<SessionMeta | undefined> {
  if (finalizingSessions.has(sessionId)) {
    return sessions.get(sessionId);
  }

  finalizingSessions.add(sessionId);

  try {
    const meta = ensureSessionMeta(sessionId);

    if (isSessionLiveConnected(meta)) {
      return meta;
    }

    let progress = await getPairingProgress(sessionId);

    const pairingAcceptedAgeMs =
      meta.pairingAcceptedAt && progress.pairingAccepted ? Date.now() - meta.pairingAcceptedAt : 0;
    const linkDeviceWaitExpired =
      progress.pairingAccepted &&
      !progress.registered &&
      pairingAcceptedAgeMs >= PAIRING_LINK_DEVICE_WAIT_MS;

    // User is on WhatsApp's "Link device" / scam-warning screen — keep socket open briefly
    if (
      meta.status === 'pending_pairing' &&
      progress.pairingAccepted &&
      !progress.registered &&
      meta.sock &&
      !linkDeviceWaitExpired
    ) {
      logger.info(
        { sessionId, waId: progress.waId, pairingAcceptedAgeMs },
        'awaiting user tap on Link device — not reconnecting yet'
      );
      return meta;
    }

    if (linkDeviceWaitExpired && meta.sock) {
      logger.warn(
        { sessionId, waId: progress.waId, pairingAcceptedAgeMs },
        'Link device wait expired — closing socket and forcing reconnect to complete registration'
      );
      endSocket(meta);
    }

    if (meta.status === 'pending_pairing' && meta.pairingCode && !progress.pairingAccepted) {
      void maintainPairingSocketAlive(sessionId, meta);
      return meta;
    }

    if (progress.pairingAccepted && !meta.pairingAcceptedAt) {
      meta.pairingAcceptedAt = Date.now();
    }

    const needsRecovery =
      progress.registered ||
      (progress.pairingAccepted && !meta.sock) ||
      linkDeviceWaitExpired;

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

/** WhatsApp rejects WEB/Desktop (DARWIN) fingerprints — use macOS Chrome for all linking flows. */
function browserConfigForSession(sessionId: string, meta: SessionMeta): [string, string, string] {
  if (
    meta.linkPhone ||
    meta.status === 'pending_pairing' ||
    meta.status === 'pending_qr' ||
    meta.status === 'starting' ||
    sessionId.startsWith('user_') ||
    sessionId === 'system'
  ) {
    return Browsers.macOS('Chrome');
  }

  return Browsers.macOS('Chrome');
}

async function resolveSocketVersion(forceRefresh = false): Promise<[number, number, number]> {
  if (!forceRefresh && cachedBaileysVersion) {
    return cachedBaileysVersion;
  }

  if (baileysVersionFetchPromise) {
    return baileysVersionFetchPromise;
  }

  baileysVersionFetchPromise = (async (): Promise<[number, number, number]> => {
    const raw = process.env.BAILEYS_VERSION?.trim();
    if (raw) {
      try {
        const parsed = JSON.parse(raw) as unknown;
        if (Array.isArray(parsed) && parsed.length === 3) {
          cachedBaileysVersion = [Number(parsed[0]), Number(parsed[1]), Number(parsed[2])];
          logger.info({ version: cachedBaileysVersion, source: 'BAILEYS_VERSION env' }, 'WA Web version resolved');
          return cachedBaileysVersion;
        }
      } catch {
        logger.warn('BAILEYS_VERSION env invalid JSON — fetching live WA Web version');
      }
    }

    try {
      const { version } = await fetchLatestWaWebVersion();
      cachedBaileysVersion = version;
      logger.info({ version, source: 'fetchLatestWaWebVersion' }, 'WA Web version resolved');
      return version;
    } catch (err) {
      logger.warn({ err }, 'fetchLatestWaWebVersion failed — falling back to fetchLatestBaileysVersion');
    }

    const { version } = await fetchLatestBaileysVersion();
    cachedBaileysVersion = version;
    logger.info({ version, source: 'fetchLatestBaileysVersion' }, 'WA Web version resolved');
    return version;
  })();

  try {
    return await baileysVersionFetchPromise;
  } finally {
    baileysVersionFetchPromise = null;
  }
}

export function invalidateCachedWaVersion(reason?: string): void {
  if (cachedBaileysVersion) {
    logger.warn({ previous: cachedBaileysVersion, reason }, 'invalidating cached WA Web version');
  }
  cachedBaileysVersion = null;
}

/** Pre-fetch WA version at gateway boot so first pairing code is faster. */
export async function warmBaileysVersion(): Promise<void> {
  await resolveSocketVersion(true);
}

async function flushSessionCreds(sessionId: string, meta: SessionMeta): Promise<void> {
  const authPath = sessionPath(sessionId);
  fs.mkdirSync(authPath, { recursive: true });

  if (meta.saveCreds) {
    await meta.saveCreds();
    return;
  }

  const { saveCreds } = await useMultiFileAuthState(authPath);
  await saveCreds();
}

async function createSocket(sessionId: string, meta: SessionMeta): Promise<WASocket> {
  const authPath = sessionPath(sessionId);
  fs.mkdirSync(authPath, { recursive: true });
  logger.info({ sessionId, authPath }, 'createSocket');
  const version = await resolveSocketVersion();
  logger.info({ sessionId, version, browser: browserConfigForSession(sessionId, meta) }, 'createSocket config');

  const { state, saveCreds } = await useMultiFileAuthState(authPath);

  const sock = makeWASocket({
    version,
    auth: state,
    logger: pino({ level: 'silent' }),
    printQRInTerminal: false,
    browser: browserConfigForSession(sessionId, meta),
    markOnlineOnConnect: false,
    syncFullHistory: false,
    emitOwnEvents: true,
    connectTimeoutMs: 60_000,
    defaultQueryTimeoutMs: 60_000,
    keepAliveIntervalMs: Number(process.env.BAILEYS_KEEP_ALIVE_MS ?? 25_000),
  });

  meta.sock = sock;
  meta.saveCreds = saveCreds;
  attachMessageReceiptListener(sock, sessionId);

  sock.ev.on('creds.update', () => {
    void (async () => {
      try {
        await flushSessionCreds(sessionId, meta);
      } catch (err) {
        logger.error({ sessionId, authPath: sessionPath(sessionId), err }, 'creds.update save failed');
      }

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
        meta.pairingConfirmedByPhone = true;
        if (!meta.pairingAcceptedAt) {
          meta.pairingAcceptedAt = Date.now();
        }
        logger.info(
          { sessionId, waId: progress.waId },
          'creds.update: pair-success from phone — waiting for user to tap Link device (do not reconnect yet)'
        );
      }
    })();
  });

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr, isNewLogin } = update;
    const statusCode = (lastDisconnect?.error as Boom | undefined)?.output?.statusCode;

    if (isNewLogin && (meta.status === 'pending_pairing' || meta.status === 'starting')) {
      meta.pairingConfirmedByPhone = true;
      if (!meta.pairingAcceptedAt) {
        meta.pairingAcceptedAt = Date.now();
      }
      logger.info(
        { sessionId, status: meta.status },
        'isNewLogin — phone confirmed pairing (pair-success); expect restartRequired close'
      );
    } else if (isNewLogin && meta.status === 'pending_qr') {
      logger.info({ sessionId, status: meta.status }, 'isNewLogin — will reconnect on restartRequired close');
    }

    if (qr) {
      if (isPhonePairingFlow(meta)) {
        logger.info({ sessionId, status: meta.status }, 'QR event ignored during phone pairing flow');
      } else {
        meta.qr = qr;
        meta.qrGeneratedAt = Date.now();
        const credsRegistered = Boolean(sock.authState.creds.registered);
        if (!credsRegistered && meta.status !== 'pending_pairing') {
          meta.status = 'pending_qr';
        } else if (credsRegistered) {
          logger.info({ sessionId }, 'QR event ignored — saved credentials still registered');
        }
        logger.info({ sessionId, status: meta.status, credsRegistered }, 'QR event');
        if (meta.status === 'pending_qr' && !credsRegistered) {
          startQrKeepalive(sessionId);
        }
      }
    }

    if (connection === 'open') {
      meta.status = 'connected';
      meta.qr = undefined;
      meta.pairingCode = undefined;
      meta.connectedAt = Date.now();
      meta.reconnectFailures = 0;
      loggedOutReconnectAttempts.delete(sessionId);
      stopPairingKeepalive(sessionId);
      stopQrKeepalive(sessionId);
      startConnectedHeartbeat(sessionId);
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

      logger.warn(
        {
          sessionId,
          statusCode,
          loggedOut,
          restartRequired,
          status: meta.status,
        },
        'connection closed'
      );

      meta.sock = undefined;
      stopConnectedHeartbeat(sessionId);

      void (async () => {
        const progress = await getPairingProgress(sessionId);
        const registered = progress.registered;
        const wasPairing = isPhonePairingFlow(meta) || meta.status === 'pending_pairing';
        const wasQrLinking =
          !registered &&
          !wasPairing &&
          (meta.status === 'pending_qr' || (meta.status === 'starting' && !meta.linkPhone));

        if (restartRequired && wasQrLinking) {
          stopPairingKeepalive(sessionId);
          logger.info({ sessionId, wasQrLinking }, 'restartRequired during QR — refreshing for scan');
          await reconnectForQrLinking(sessionId, meta);
          return;
        }

        if (restartRequired && (wasPairing || (registered && !loggedOut))) {
          stopPairingKeepalive(sessionId);
          const awaitingCode = Boolean(meta.pairingCode) && !progress.pairingAccepted;

          if (wasPairing && awaitingCode) {
            logger.info(
              { sessionId, wasPairing, registered },
              'restartRequired while awaiting pairing code — reconnecting with saved creds'
            );
            await reconnectPairingAwaitingCode(sessionId, meta);
            startPairingKeepalive(sessionId, meta);
            return;
          }

          logger.info(
            { sessionId, wasPairing, wasQrLinking, registered },
            'restartRequired — reconnecting to complete or restore session'
          );
          await reconnectAfterRestart(sessionId, meta);
          return;
        }

        if (loggedOut && registered) {
          const maxAttempts = isSystemSession(sessionId)
            ? SYSTEM_LOGGED_OUT_RECONNECT_ATTEMPTS
            : LOGGED_OUT_RECONNECT_ATTEMPTS;
          const attempts = (loggedOutReconnectAttempts.get(sessionId) ?? 0) + 1;
          loggedOutReconnectAttempts.set(sessionId, attempts);

          if (attempts <= maxAttempts) {
            logger.warn(
              { sessionId, statusCode, attempts, max: maxAttempts },
              'loggedOut on registered session — retrying reconnect before wiping creds'
            );
            meta.status = 'starting';
            await reconnectAfterRestart(sessionId, meta);
            const live = sessions.get(sessionId);
            if (live?.status === 'connected') {
              loggedOutReconnectAttempts.delete(sessionId);
            }
            return;
          }

          logger.warn(
            { sessionId, statusCode, attempts },
            'logged out from WhatsApp phone — wiping session credentials'
          );
          wipeSessionAuth(sessionId, 'logged_out');
          meta.status = 'disconnected';
          meta.pairingCode = undefined;
          meta.qr = undefined;
          meta.connectedAt = undefined;
          meta.reconnectFailures = 0;
          return;
        }

        if (loggedOut && !registered) {
          if (wasPairing && isSystemSession(sessionId)) {
            logger.warn(
              { sessionId, statusCode },
              'system session: logged out during pairing state — wiping (use QR in admin only)'
            );
            wipeSessionAuth(sessionId, 'incomplete_link');
          }
          meta.status = 'disconnected';
          meta.pairingCode = undefined;
          meta.qr = undefined;
          meta.connectedAt = undefined;
          meta.reconnectFailures = 0;
          return;
        }

        loggedOutReconnectAttempts.delete(sessionId);

        if (wasQrLinking) {
          logger.info(
            { sessionId, statusCode, loggedOut, restartRequired },
            'QR link interrupted — refreshing socket (scan window stays open)'
          );
          await reconnectForQrLinking(sessionId, meta);
          return;
        }

        if (wasPairing) {
          const awaitingCode = Boolean(meta.pairingCode) && !progress.pairingAccepted;

          if (awaitingCode) {
            if (statusCode === WA_CLIENT_TOO_OLD_STATUS) {
              invalidateCachedWaVersion('405 while awaiting pairing code');
              await resolveSocketVersion(true);
            }

            logger.info(
              {
                sessionId,
                statusCode,
                code: meta.pairingCode ? formatPairingCodeDisplay(meta.pairingCode) : null,
              },
              'pairing socket closed while awaiting code — reconnecting with saved creds'
            );
            await reconnectPairingAwaitingCode(sessionId, meta);
            startPairingKeepalive(sessionId, meta);
            return;
          }

          void maintainPairingSocketAlive(sessionId, meta);

          setTimeout(() => {
            void (async () => {
              if (meta.status === 'connected') {
                return;
              }
              const latest = await getPairingProgress(sessionId);
              if (latest.registered && !meta.sock) {
                logger.info({ sessionId, statusCode }, 'pairing close with registered creds — reconnecting');
                stopPairingKeepalive(sessionId);
                await reconnectAfterRestart(sessionId, meta);
              } else if (latest.pairingAccepted) {
                logger.info(
                  { sessionId, statusCode, waId: latest.waId },
                  'pairing accepted — keeping session open for Link device confirmation'
                );
              } else if (!meta.pairingCode) {
                void maintainPairingSocketAlive(sessionId, meta);
              }
            })();
          }, 1500);

          return;
        }

        meta.pairingCode = undefined;

        if (registered) {
          meta.status = 'starting';
          logger.info({ sessionId, statusCode }, 'registered session socket closed — reconnecting');
          await reconnectAfterRestart(sessionId, meta);
          return;
        }

        meta.status = 'disconnected';

        if (meta.linkPhone) {
          setTimeout(() => {
            void startSessionWithPairing(sessionId, meta.linkPhone!, true);
          }, 5000);
        }
      })();
    }
  });

  const { registerInboundHandler } = await import('./events.js');
  registerInboundHandler(sock, sessionId);

  return sock;
}

function isPairingSocketError(message: string): boolean {
  const normalized = message.toLowerCase();
  return (
    normalized.includes('connection closed') ||
    normalized.includes('connection lost') ||
    normalized.includes('socket closed') ||
    normalized.includes('not connected') ||
    normalized.includes('socket not ready')
  );
}

async function requestPairingCodeWithRetry(
  meta: SessionMeta,
  digits: string,
  sessionId: string
): Promise<string> {
  let lastError: unknown;

  for (let attempt = 1; attempt <= PAIRING_CODE_MAX_ATTEMPTS; attempt++) {
    if (!meta.sock) {
      logger.info({ sessionId, attempt }, 'creating fresh socket for requestPairingCode');
      await createSocket(sessionId, meta);
    }

    const sock = meta.sock;
    if (!sock) {
      lastError = new Error('Failed to create WhatsApp socket for pairing');
      await sleep(Math.min(attempt * 1000, 3000));
      continue;
    }

    try {
      await waitUntilReadyForPairing(sock, sessionId);
      logger.info({ sessionId, delayMs: PAIRING_READY_DELAY_MS, attempt }, 'waiting before requestPairingCode');
      await sleep(PAIRING_READY_DELAY_MS);

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

      if (isPairingSocketError(message) || !meta.sock) {
        endSocket(meta);
      }

      const status405 = err instanceof Boom && err.output?.statusCode === WA_CLIENT_TOO_OLD_STATUS;
      if (status405 || message.includes('405')) {
        invalidateCachedWaVersion('405 during requestPairingCode');
        await resolveSocketVersion(true);
      }

      await sleep(Math.min(attempt * 1000, 3000));
    }
  }

  const message = lastError instanceof Error ? lastError.message : 'requestPairingCode failed';
  throw new Error(message);
}

async function persistPairingCredsOrThrow(sessionId: string, meta: SessionMeta, code: string): Promise<void> {
  const authPath = sessionPath(sessionId);
  const credsFile = path.join(authPath, 'creds.json');
  const expectedRaw = formatPairingCodeRaw(code);

  fs.mkdirSync(authPath, { recursive: true });

  for (let attempt = 1; attempt <= 3; attempt++) {
    await flushSessionCreds(sessionId, meta);

    if (fs.existsSync(credsFile)) {
      const progress = await getPairingProgress(sessionId);
      const diskRaw = progress.pairingCodeOnDisk ? formatPairingCodeRaw(progress.pairingCodeOnDisk) : null;
      if (diskRaw === expectedRaw) {
        logger.info({ sessionId, authPath, attempt }, 'pairing creds.json persisted');
        return;
      }
    }

    if (attempt < 3) {
      await sleep(150);
    }
  }

  logger.error(
    { sessionId, authPath, credsExists: fs.existsSync(credsFile) },
    'pairing creds.json missing or code mismatch after issue — phone linking will fail'
  );
  throw new Error(
    `Pairing credentials were not saved (${authPath}). Check SESSIONS_DIR permissions and redeploy gateway.`
  );
}

async function runPairingFlow(sessionId: string, digits: string, fresh: boolean): Promise<SessionMeta> {
  if (fresh) {
    wipeSessionAuth(sessionId, 'incomplete_link');
  } else {
    const existing = sessions.get(sessionId);
    if (existing?.sock) {
      endSocket(existing);
    }
  }

  const meta: SessionMeta = {
    sessionId,
    status: 'pending_pairing',
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
    wipeSessionAuth(sessionId, 'incomplete_link');
    return runPairingFlow(sessionId, digits, true);
  }

  const code = await requestPairingCodeWithRetry(meta, digits, sessionId);
  await persistPairingCredsOrThrow(sessionId, meta, code);
  meta.pairingCode = code;
  meta.pairingRequestedAt = Date.now();
  startPairingKeepalive(sessionId, meta);

  if (!meta.sock) {
    await reconnectPairingAwaitingCode(sessionId, meta);
  }

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
    wipeSessionAuth(sessionId, 'pairing_mismatch');
    return runPairingFlow(sessionId, digits, true);
  }

  logger.info(
    {
      sessionId,
      status: meta.status,
      display: formatPairingCodeDisplay(code),
      authPath: sessionPath(sessionId),
      registeredOnDisk: progress.registered,
      diskCode: progress.pairingCodeOnDisk,
    },
    'pairing code ready — open WhatsApp immediately and enter code'
  );

  return meta;
}

export async function startSession(sessionId: string): Promise<SessionMeta> {
  clearSessionAbort(sessionId);
  await ensureQrLinkingSession(sessionId);

  const reconnecting = reconnectPromises.get(sessionId);
  if (reconnecting) {
    await reconnecting;
  }

  const existing = sessions.get(sessionId);
  if (isSessionLiveConnected(existing)) {
    return existing!;
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
  if (isSystemSession(sessionId)) {
    throw new Error(
      `Session "${sessionId}" is the platform OTP number — use QR linking in admin (never pairing code).`
    );
  }

  clearSessionAbort(sessionId);

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
  if (isSessionLiveConnected(existing)) {
    return existing!;
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
  if (isSessionLiveConnected(meta)) {
    return;
  }

  if (!sessionAuthExists(sessionId)) {
    return;
  }

  const progress = await getPairingProgress(sessionId);

  if (isSystemSession(sessionId)) {
    if (progress.registered) {
      return;
    }

    if (
      isLinkingInProgress(sessionId) &&
      (meta?.status === 'pending_qr' || meta?.status === 'starting')
    ) {
      logger.info({ sessionId, status: meta?.status }, 'system session: QR scan in progress — not wiping');
      return;
    }

    const hasPairingCode = Boolean(progress.pairingCodeOnDisk);
    const midQrScan =
      progress.pairingAccepted && !hasPairingCode && isLinkingInProgress(sessionId);

    if (midQrScan) {
      return;
    }

    if (hasPairingCode || progress.pairingAccepted || !isLinkingInProgress(sessionId)) {
      logger.warn(
        { sessionId, progress, hasPairingCode },
        'system session: removing incomplete auth (OTP number must link via QR only)'
      );
      wipeSessionAuth(sessionId, 'incomplete_link');
    }
    return;
  }

  if (isLinkingInProgress(sessionId) && progress.pairingAccepted && !progress.registered) {
    logger.info({ sessionId, status: meta?.status }, 'QR/pairing in progress — not wiping session');
    return;
  }

  const blocksQr =
    progress.registered ||
    progress.pairingAccepted ||
    Boolean(progress.pairingCodeOnDisk);

  if (blocksQr) {
    logger.info({ sessionId, progress }, 'wiping stale auth before QR linking');
    wipeSessionAuth(sessionId, 'incomplete_link');
  }
}

export async function deleteSession(sessionId: string, force = true): Promise<void> {
  if (!force && isLinkingInProgress(sessionId)) {
    logger.warn({ sessionId }, 'deleteSession skipped — link in progress (use force=true)');
    return;
  }

  abortSession(sessionId);
  stopPairingKeepalive(sessionId);
  stopConnectedHeartbeat(sessionId);
  stopQrKeepalive(sessionId);
  loggedOutReconnectAttempts.delete(sessionId);
  finalizingSessions.delete(sessionId);

  const reconnecting = reconnectPromises.get(sessionId);
  if (reconnecting) {
    try {
      await Promise.race([reconnecting, sleep(8000)]);
    } catch {
      // reconnect aborted by delete
    }
  }
  reconnectPromises.delete(sessionId);

  const inFlight = startPromises.get(sessionId);
  if (inFlight) {
    try {
      await Promise.race([inFlight, sleep(8000)]);
    } catch {
      // pairing flow aborted by delete
    }
  }
  startPromises.delete(sessionId);

  const meta = sessions.get(sessionId);
  const sock = meta?.sock;
  if (sock) {
    try {
      await Promise.race([sock.logout(), sleep(8000)]);
      logger.info({ sessionId }, 'WhatsApp logout sent — device should unlink from Linked devices');
    } catch (err) {
      logger.warn({ sessionId, err }, 'WhatsApp logout failed — wiping local session anyway');
    }
  }

  if (meta) {
    endSocket(meta);
    meta.status = 'disconnected';
    meta.pairingCode = undefined;
    meta.phone = undefined;
    meta.linkPhone = undefined;
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
