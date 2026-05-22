import 'dotenv/config';
import express, { type NextFunction, type Request, type Response } from 'express';
import QRCode from 'qrcode';
import pino from 'pino';
import {
  deleteSession,
  getPairingCode,
  getQr,
  ensurePairingFinalized,
  formatPairingCodeDisplay,
  getSessionMeta,
  isAuthRegistered,
  normalizePhoneDigits,
  startSession,
  startSessionWithPairing,
  waitForConnected,
  waitForPairingOrConnected,
  waitForQrOrConnected,
} from './baileys/manager.js';
import { sendText } from './baileys/send.js';

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

async function fetchQrPayload(sessionId: string) {
  await startSession(sessionId);
  const meta = await waitForQrOrConnected(sessionId, 60_000);

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

app.get('/health', (_req, res) => {
  res.json({
    ok: true,
    version: '1.2.4',
    qrSetupPage: QR_SETUP_PAGE_ENABLED,
    secretConfigured: Boolean(SECRET),
    features: {
      pairingCode: true,
      qrPage: QR_SETUP_PAGE_ENABLED,
    },
  });
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
  let meta = getSessionMeta(sessionId);

  if (!meta) {
    res.json({ status: 'disconnected', phone: null, sessionId, registeredOnDisk: false });
    return;
  }

  if (meta.status === 'pending_pairing' || meta.status === 'starting') {
    meta = (await ensurePairingFinalized(sessionId)) ?? meta;
    if (meta.status === 'pending_pairing' || meta.status === 'starting') {
      await waitForConnected(sessionId, 10_000);
      meta = getSessionMeta(sessionId) ?? meta;
    }
  }

  const registered = await isAuthRegistered(sessionId);

  res.json({
    sessionId: meta.sessionId,
    status: meta.status,
    phone: meta.phone ?? null,
    pairingCode: meta.pairingCode ? formatPairingCodeDisplay(meta.pairingCode) : null,
    registeredOnDisk: registered,
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
    const registered = await isAuthRegistered(sessionId);

    logger.info(
      { sessionId, connected, status: meta?.status, registered },
      'finalize pairing result'
    );

    res.json({
      sessionId,
      status: meta?.status ?? 'disconnected',
      phone: meta?.phone ?? null,
      connected: connected || meta?.status === 'connected',
      registeredOnDisk: registered,
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

  try {
    const payload = await fetchQrPayload(sessionId);

    if (payload.kind === 'connected') {
      res.json({ status: 'connected', qr: null, qrImage: null, phone: payload.phone });
      return;
    }

    res.json({ status: 'pending_qr', qr: payload.qr, qrImage: payload.qrImage });
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
    const result = await sendText(sessionId, to, message);
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
  await deleteSession(req.params.id);
  res.json({ deleted: true, sessionId: req.params.id });
});

app.listen(PORT, HOST, () => {
  logger.info(`WhatsApp gateway listening on http://${HOST}:${PORT}`);
});
