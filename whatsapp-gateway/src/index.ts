import 'dotenv/config';
import express, { type NextFunction, type Request, type Response } from 'express';
import QRCode from 'qrcode';
import pino from 'pino';
import {
  clearStaleSessionMeta,
  cleanupStaleUnregisteredSession,
  deleteSession,
  disconnectedStatusPayload,
  getPairingCode,
  getPairingCodeAgeSeconds,
  getPairingProgress,
  getQr,
  ensurePairingFinalized,
  ensureQrLinkingSession,
  ensureSessionMeta,
  formatPairingCodeDisplay,
  getSessionMeta,
  type SessionStatus,
  isAuthRegistered,
  isLinkedOnWhatsApp,
  isPairingSocketAlive,
  isSessionAborted,
  isLinkingInProgress,
  isSessionReconnecting,
  isSessionStartInFlight,
  isSystemSession,
  normalizePhoneDigits,
  restorePersistedSessions,
  sessionAuthExists,
  startConnectedSessionWatchdog,
  startSession,
  startSessionWithPairing,
  waitForConnected,
  waitForPairingOrConnected,
  waitForQrOrConnected,
} from './baileys/manager.js';
import { sendText } from './baileys/send.js';
import { probeReceiptWebhook, receiptWebhookConfig } from './baileys/receipts.js';

const logger = pino({ level: process.env.LOG_LEVEL ?? 'info' });
const app = express();
app.use(express.json({ limit: '2mb' }));

const PORT = Number(process.env.PORT ?? 3000);
const HOST = process.env.HOST ?? '127.0.0.1';
const SECRET = process.env.BAILEYS_GATEWAY_SECRET ?? '';
const QR_SETUP_PAGE_ENABLED = process.env.ENABLE_QR_SETUP_PAGE !== 'false';

function readToken(req: Request): string {
  const queryToken = req.query.token;
  if (typeof queryToken === 'string' && queryToken.length > 0) {
    return queryToken;
  }

  const header = req.headers.authorization ?? '';
  return header.startsWith('Bearer ') ? header.slice(7) : header;
}

function auth(req: Request, res: Response, next: NextFunction): void {
  if (!SECRET) {
    logger.warn('BAILEYS_GATEWAY_SECRET is not set — rejecting all requests');
    res.status(500).json({ error: 'Gateway secret not configured' });
    return;
  }

  if (readToken(req) !== SECRET) {
    res.status(401).json({ error: 'Unauthorized' });
    return;
  }

  next();
}

function parseWaitMs(raw: unknown, fallback: number): number {
  const n = Number(raw);
  if (!Number.isFinite(n) || n < 0) {
    return fallback;
  }

  return Math.min(60_000, Math.floor(n));
}

async function fetchQrPayload(sessionId: string, waitMs = 60_000) {
  await startSession(sessionId);
  const meta = await waitForQrOrConnected(sessionId, waitMs);

  if (meta.status === 'connected') {
    return { kind: 'connected' as const, phone: meta.phone ?? null };
  }

  const qr = meta.qr ?? getQr(sessionId);
  if (!qr) {
    throw new Error('QR not generated. Check pm2 logs or delete session and retry.');
  }

  const qrImage = await QRCode.toDataURL(qr);
  return { kind: 'qr' as const, qr, qrImage };
}

async function respondWithQrSnapshot(
  sessionId: string,
  waitMs: number,
  res: Response
): Promise<void> {
  const snap = getSessionMeta(sessionId);
  if (snap?.status === 'connected') {
    res.json({
      status: 'connected',
      qr: null,
      qrImage: null,
      phone: snap.phone ?? null,
      ready: true,
    });
    return;
  }

  const immediateQr = getQr(sessionId);
  if (immediateQr) {
    const qrImage = await QRCode.toDataURL(immediateQr);
    res.json({ status: 'pending_qr', qr: immediateQr, qrImage, ready: true });
    return;
  }

  if (waitMs <= 0) {
    res.json({ status: 'generating', qr: null, qrImage: null, ready: false });
    return;
  }

  await ensureQrLinkingSession(sessionId);
  await startSession(sessionId);
  const meta = await waitForQrOrConnected(sessionId, waitMs);

  if (meta.status === 'connected') {
    res.json({
      status: 'connected',
      qr: null,
      qrImage: null,
      phone: meta.phone ?? null,
      ready: true,
    });
    return;
  }

  const qr = meta.qr ?? getQr(sessionId);
  if (!qr) {
    res.json({ status: 'generating', qr: null, qrImage: null, ready: false });
    return;
  }

  const qrImage = await QRCode.toDataURL(qr);
  res.json({ status: 'pending_qr', qr, qrImage, ready: true });
}

app.get('/health', (_req, res) => {
  const receipt = receiptWebhookConfig();
  res.json({
    ok: true,
    version: '1.4.0',
    qrSetupPage: QR_SETUP_PAGE_ENABLED,
    secretConfigured: Boolean(SECRET),
    receiptWebhooks: {
      configured: Boolean(receipt.url && receipt.hasSecret),
      hasUrl: Boolean(receipt.url),
      hasSecret: receipt.hasSecret,
      mode: receipt.mode,
      postUrl: receipt.url,
      hostHeader: receipt.hostHeader,
    },
    features: {
      pairingCode: true,
      qrPage: QR_SETUP_PAGE_ENABLED,
      deliveryReadReceipts: Boolean(receipt.url && receipt.hasSecret),
    },
  });
});

app.get('/health/receipt-probe', auth, async (_req, res) => {
  const result = await probeReceiptWebhook();
  res.status(result.ok ? 200 : 503).json(result);
});

/** Browser-friendly QR page for initial setup (token in query string). */
app.get('/sessions/:id/qr/page', async (req, res) => {
  if (!QR_SETUP_PAGE_ENABLED) {
    res.status(404).send('QR setup page is disabled');
    return;
  }

  if (!SECRET || readToken(req) !== SECRET) {
    res.status(401).send('Unauthorized — add ?token=YOUR_BAILEYS_GATEWAY_SECRET');
    return;
  }

  const sessionId = req.params.id;

  try {
    const payload = await fetchQrPayload(sessionId);

    if (payload.kind === 'connected') {
      res.type('html').send(`<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:2rem">
        <h1>Already connected</h1><p>Session <b>${sessionId}</b></p><p>Phone: ${payload.phone ?? '—'}</p>
        </body></html>`);
      return;
    }

    res.type('html').send(`<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta http-equiv="refresh" content="45">
<title>WhatsApp QR — ${sessionId}</title></head>
<body style="font-family:sans-serif;text-align:center;padding:2rem">
<h1>Scan with WhatsApp</h1>
<p>Session: <b>${sessionId}</b></p>
<p>WhatsApp → <b>Linked devices</b> → <b>Link a device</b></p>
<img src="${payload.qrImage}" width="360" alt="QR code"/>
<p><small>Page refreshes every 45s while QR is valid. Do not share this URL.</small></p>
</body></html>`);
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Failed to load QR';
    logger.error({ sessionId, err }, 'qr page failed');
    res.status(503).type('html').send(`<html><body><h1>Error</h1><p>${message}</p></body></html>`);
  }
});

app.use(auth);

app.post('/sessions', async (req, res) => {
  const sessionId = String(req.body?.sessionId ?? '').trim();
  const phone = String(req.body?.phone ?? '').trim();
  const linkMethod = String(req.body?.linkMethod ?? '').trim().toLowerCase();

  if (!sessionId) {
    res.status(400).json({ error: 'sessionId is required' });
    return;
  }

  try {
    const usePairing = linkMethod === 'pairing' || (phone.length > 0 && linkMethod !== 'qr');

    if (!usePairing) {
      await ensureQrLinkingSession(sessionId);
    }

    const meta = usePairing
      ? await startSessionWithPairing(sessionId, phone)
      : await startSession(sessionId);

    res.json({
      sessionId: meta.sessionId,
      status: meta.status,
      phone: meta.phone ?? meta.linkPhone ?? null,
      pairingCode: meta.pairingCode ? formatPairingCodeDisplay(meta.pairingCode) : null,
      linkPhone: meta.linkPhone ?? null,
      linkMethod: usePairing ? 'pairing' : 'qr',
    });
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Failed to start session';
    logger.error({ sessionId, err }, 'start session failed');
    res.status(503).json({ error: message });
  }
});

app.get('/sessions/:id/status', async (req, res) => {
  const sessionId = req.params.id;
  const quick = req.query.quick === '1' || req.query.quick === 'true';

  if (isSessionAborted(sessionId)) {
    res.json(disconnectedStatusPayload(sessionId));
    return;
  }

  if (!sessionAuthExists(sessionId)) {
    const staleMeta = getSessionMeta(sessionId);
    if (staleMeta && !isLinkingInProgress(sessionId)) {
      clearStaleSessionMeta(sessionId);
    }
    if (staleMeta && isLinkingInProgress(sessionId)) {
      res.json({
        sessionId,
        status: staleMeta.status,
        phone: staleMeta.phone ?? null,
        registeredOnDisk: false,
        pairingAccepted: false,
        pairingProgress: 'awaiting_code',
        linkedOnWhatsApp: false,
        socketAlive: isPairingSocketAlive(sessionId),
        linking: true,
      });
      return;
    }
    res.json(disconnectedStatusPayload(sessionId));
    return;
  }

  if (await cleanupStaleUnregisteredSession(sessionId)) {
    res.json(disconnectedStatusPayload(sessionId));
    return;
  }

  let meta = getSessionMeta(sessionId);
  if (!meta) {
    meta = ensureSessionMeta(sessionId);
  }

  if (quick) {
    const authOnDisk = sessionAuthExists(sessionId);
    const progress = authOnDisk
      ? await getPairingProgress(sessionId)
      : {
          registered: false,
          pairingAccepted: false,
          waId: null as string | null,
          pairingCodeOnDisk: null as string | null,
        };

    const inPairingFlow =
      meta.status === 'pending_pairing' && !progress.registered && !progress.pairingAccepted;

    const socketAlive = isPairingSocketAlive(sessionId);

    if (
      !isSessionAborted(sessionId) &&
      progress.registered &&
      !socketAlive &&
      !inPairingFlow
    ) {
      if (!isSessionStartInFlight(sessionId)) {
        void startSession(sessionId).catch((err) => {
          logger.warn({ sessionId, err }, 'background reconnect on status poll failed');
        });
      }
    }

    if (
      !isSessionAborted(sessionId) &&
      !isSystemSession(sessionId) &&
      progress.pairingAccepted &&
      !progress.registered &&
      !meta.sock
    ) {
      void ensurePairingFinalized(sessionId).catch((err) => {
        logger.warn({ sessionId, err }, 'background finalize after pairing accepted failed');
      });
    }

    if (
      isSystemSession(sessionId) &&
      !isSessionAborted(sessionId) &&
      progress.pairingAccepted &&
      !progress.registered &&
      !meta.sock &&
      !isSessionStartInFlight(sessionId)
    ) {
      void startSession(sessionId).catch((err) => {
        logger.warn({ sessionId, err }, 'system session: reconnect after QR scan step failed');
      });
    }

    const reconnecting =
      progress.registered &&
      !socketAlive &&
      !inPairingFlow &&
      (isSessionReconnecting(sessionId) || isSessionStartInFlight(sessionId) || meta.status === 'starting');

    let reportStatus: SessionStatus = meta.status;
    if (socketAlive && (meta.status === 'connected' || (progress.registered && !inPairingFlow))) {
      reportStatus = 'connected';
    } else if (progress.registered && !inPairingFlow && !socketAlive) {
      // Creds still valid (phone may show linked device) — socket drop is reconnectable, not a full logout.
      reportStatus = 'reconnecting';
    } else if (reconnecting) {
      reportStatus = 'reconnecting';
    } else if (meta.status === 'connected' && !socketAlive) {
      reportStatus = 'disconnected';
    }

    const liveConnected = reportStatus === 'connected' && socketAlive;
    const registeredOnDisk = progress.registered;
    const pairingAccepted = liveConnected ? false : progress.pairingAccepted;
    const pairingProgress = liveConnected
      ? 'registered'
      : progress.registered
        ? 'registered'
        : progress.pairingAccepted
          ? 'code_accepted'
          : 'awaiting_code';
    const pairingCodeAgeSeconds =
      meta.status === 'pending_pairing' && meta.pairingCode
        ? getPairingCodeAgeSeconds(sessionId)
        : null;

    const linkedOnWhatsApp = isLinkedOnWhatsApp(reportStatus, socketAlive, progress.registered);

    res.json({
      sessionId: meta.sessionId,
      status: reportStatus,
      phone: meta.phone ?? progress.waId?.split('@')[0]?.split(':')[0] ?? null,
      pairingCode: meta.pairingCode ? formatPairingCodeDisplay(meta.pairingCode) : null,
      registeredOnDisk,
      pairingAccepted,
      pairingProgress,
      linkedOnWhatsApp,
      waId: progress.waId,
      socketAlive,
      reconnecting: reconnecting || (registeredOnDisk && !socketAlive && !inPairingFlow && reportStatus !== 'connected'),
      unlinked: !registeredOnDisk,
      pairingCodeAgeSeconds,
      connectedAt: meta.connectedAt ? new Date(meta.connectedAt).toISOString() : null,
      socketUptimeSeconds:
        meta.connectedAt && socketAlive
          ? Math.max(0, Math.floor((Date.now() - meta.connectedAt) / 1000))
          : null,
      quick: true,
    });
    return;
  }

  if (!getSessionMeta(sessionId) && sessionAuthExists(sessionId)) {
    ensureSessionMeta(sessionId);
  }

  meta = getSessionMeta(sessionId) ?? ensureSessionMeta(sessionId);
  let progress = await getPairingProgress(sessionId);

  const awaitingFreshCode =
    meta.status === 'pending_pairing' && Boolean(meta.pairingCode) && !progress.pairingAccepted;

  const awaitingLinkConfirmation =
    meta.status === 'pending_pairing' &&
    progress.pairingAccepted &&
    !progress.registered &&
    isPairingSocketAlive(sessionId);

  const needsFinalize =
    !awaitingFreshCode &&
    !awaitingLinkConfirmation &&
    (progress.pairingAccepted || progress.registered) &&
    meta.status !== 'connected';

  if (needsFinalize) {
    meta = (await ensurePairingFinalized(sessionId)) ?? meta;
    if (meta.status !== 'connected') {
      await waitForConnected(sessionId, progress.pairingAccepted ? 30_000 : 10_000);
      meta = getSessionMeta(sessionId) ?? meta;
    }
    progress = await getPairingProgress(sessionId);
  } else if (meta.status === 'pending_pairing' || meta.status === 'starting') {
    meta = (await ensurePairingFinalized(sessionId)) ?? meta;
    if (meta.status === 'pending_pairing' || meta.status === 'starting') {
      await waitForConnected(sessionId, 10_000);
      meta = getSessionMeta(sessionId) ?? meta;
    }
    progress = await getPairingProgress(sessionId);
  }

  const fullSocketAlive = isPairingSocketAlive(sessionId);
  const linkedOnWhatsApp = isLinkedOnWhatsApp(meta.status, fullSocketAlive, progress.registered);
  const fullReconnecting =
    progress.registered &&
    !fullSocketAlive &&
    meta.status !== 'pending_pairing' &&
    meta.status !== 'pending_qr' &&
    (isSessionReconnecting(sessionId) || meta.status === 'starting');

  res.json({
    sessionId: meta.sessionId,
    status: fullReconnecting ? 'reconnecting' : meta.status,
    phone: meta.phone ?? progress.waId?.split('@')[0]?.split(':')[0] ?? null,
    pairingCode: meta.pairingCode ? formatPairingCodeDisplay(meta.pairingCode) : null,
    registeredOnDisk: progress.registered,
    pairingAccepted: progress.pairingAccepted,
    pairingProgress: progress.registered
      ? 'registered'
      : progress.pairingAccepted
        ? 'code_accepted'
        : 'awaiting_code',
    linkedOnWhatsApp,
    waId: progress.waId,
    socketAlive: fullSocketAlive,
    reconnecting: fullReconnecting || (progress.registered && !fullSocketAlive && meta.status !== 'pending_pairing'),
    unlinked: !progress.registered,
    pairingCodeAgeSeconds: getPairingCodeAgeSeconds(sessionId),
  });
});

app.post('/sessions/:id/finalize', async (req, res) => {
  const sessionId = req.params.id;
  const quick = req.query.quick === '1' || req.query.quick === 'true';
  const waitMs = quick ? 18_000 : 45_000;

  try {
    await ensurePairingFinalized(sessionId);
    const connected = await waitForConnected(sessionId, waitMs);
    const meta = getSessionMeta(sessionId);
    const progress = await getPairingProgress(sessionId);

    logger.info(
      { sessionId, connected, status: meta?.status, progress },
      'finalize pairing result'
    );

    res.json({
      sessionId,
      status: meta?.status ?? 'disconnected',
      phone: meta?.phone ?? null,
      connected: connected || meta?.status === 'connected',
      registeredOnDisk: progress.registered,
      pairingAccepted: progress.pairingAccepted,
      pairingProgress: progress.registered
        ? 'registered'
        : progress.pairingAccepted
          ? 'code_accepted'
          : 'awaiting_code',
    });
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Finalize failed';
    logger.error({ sessionId, err }, 'finalize pairing failed');
    res.status(503).json({ error: message, connected: false });
  }
});

app.get('/sessions/:id/pairing-code', async (req, res) => {
  const sessionId = req.params.id;
  const phone = String(req.query.phone ?? '').trim();

  try {
    if (phone) {
      await startSessionWithPairing(sessionId, phone);
    }

    const meta = await waitForPairingOrConnected(sessionId, 90_000);

    if (meta.status === 'connected') {
      res.json({
        status: 'connected',
        pairingCode: null,
        phone: meta.phone ?? null,
      });
      return;
    }

    const code = meta.pairingCode ?? getPairingCode(sessionId);
    if (!code) {
      res.status(503).json({
        error: 'Pairing code not ready. POST /sessions with phone and linkMethod pairing.',
      });
      return;
    }

    res.json({
      status: 'pending_pairing',
      pairingCode: code,
      phone: meta.linkPhone ?? normalizePhoneDigits(phone) ?? null,
    });
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Failed to get pairing code';
    logger.error({ sessionId, err }, 'pairing-code endpoint failed');
    res.status(503).json({ error: message });
  }
});

app.get('/sessions/:id/qr', async (req, res) => {
  const sessionId = req.params.id;
  const waitMs = parseWaitMs(req.query.waitMs, 8_000);

  try {
    await respondWithQrSnapshot(sessionId, waitMs, res);
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Failed to get QR';
    logger.error({ sessionId, err }, 'qr endpoint failed');
    res.status(503).json({ error: message });
  }
});

app.post('/send', async (req, res) => {
  const sessionId = String(req.body?.sessionId ?? '').trim();
  const to = String(req.body?.to ?? '').trim();
  const message = String(req.body?.message ?? '').trim();

  if (!sessionId || !to || !message) {
    res.status(400).json({ error: 'sessionId, to, and message are required' });
    return;
  }

  try {
    const referenceId = String(req.body?.referenceId ?? '').trim() || undefined;
    const result = await sendText(sessionId, to, message, referenceId);
    res.json({
      sent: result.sent,
      idMessage: result.idMessage ?? null,
      referenceId: req.body?.referenceId ?? null,
    });
  } catch (err) {
    const messageText = err instanceof Error ? err.message : 'Send failed';
    logger.error({ sessionId, to, err }, 'send failed');
    res.status(503).json({ sent: false, error: messageText });
  }
});

app.delete('/sessions/:id', async (req, res) => {
  const sessionId = req.params.id;
  await deleteSession(sessionId);
  res.json({
    deleted: true,
    sessionId,
    ...disconnectedStatusPayload(sessionId),
  });
});

app.listen(PORT, HOST, () => {
  logger.info(`WhatsApp gateway listening on http://${HOST}:${PORT}`);
  void restorePersistedSessions().catch((err) => {
    logger.error({ err }, 'failed to restore persisted WhatsApp sessions on startup');
  });
  startConnectedSessionWatchdog();
});
