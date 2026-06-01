import axios from 'axios';
import { WAMessageStatus, type WASocket } from '@whiskeysockets/baileys';
import type { proto } from '@whiskeysockets/baileys';
import pino from 'pino';

const logger = pino({ level: process.env.LOG_LEVEL ?? 'info' });

const RECEIPT_DEBUG = process.env.RECEIPT_DEBUG === '1' || process.env.RECEIPT_DEBUG === 'true';

/** Last known ack per outbound message (for GET /receipts/debug). */
const lastAckByKey = new Map<string, string>();

type OutboundMeta = {
  referenceId: string;
  to: string;
  sessionId: string;
  messageId: string;
};

const outboundByMessageKey = new Map<string, OutboundMeta>();

function mapKey(sessionId: string, messageId: string): string {
  return `${sessionId}:${messageId}`;
}

function debugLog(msg: string, data?: Record<string, unknown>): void {
  if (RECEIPT_DEBUG) {
    logger.info(data ?? {}, msg);
  }
}

export function registerOutboundMessage(
  sessionId: string,
  messageId: string | undefined,
  referenceId: string | undefined,
  to: string
): void {
  if (!messageId) {
    logger.warn({ sessionId, referenceId, to }, 'receipt: send returned no message id — cannot track delivery/read');
    return;
  }

  if (!referenceId) {
    logger.warn(
      { sessionId, messageId, to },
      'receipt: no referenceId on send — webhook will match Laravel row by whatsapp_message_id only'
    );
  }

  const meta: OutboundMeta = {
    referenceId: referenceId ?? '',
    to,
    sessionId,
    messageId,
  };

  outboundByMessageKey.set(mapKey(sessionId, normalizeMessageId(messageId)), meta);

  logger.info(
    { sessionId, messageId, referenceId: referenceId ?? null, toSuffix: to.slice(-4) },
    'receipt: outbound message registered'
  );
}

function statusLabelFromCode(code: number): 'delivered' | 'read' | null {
  if (code === WAMessageStatus.DELIVERY_ACK) {
    return 'delivered';
  }
  if (code === WAMessageStatus.READ || code === WAMessageStatus.PLAYED) {
    return 'read';
  }

  return null;
}

const STATUS_BY_NAME: Record<string, number> = {
  ERROR: WAMessageStatus.ERROR,
  PENDING: WAMessageStatus.PENDING,
  SERVER_ACK: WAMessageStatus.SERVER_ACK,
  DELIVERY_ACK: WAMessageStatus.DELIVERY_ACK,
  READ: WAMessageStatus.READ,
  PLAYED: WAMessageStatus.PLAYED,
};

function parseStatusCode(status: unknown): number | null {
  if (typeof status === 'number' && !Number.isNaN(status)) {
    return status;
  }
  if (typeof status === 'string') {
    const named = STATUS_BY_NAME[status] ?? STATUS_BY_NAME[status.toUpperCase()];
    if (typeof named === 'number') {
      return named;
    }
    const parsed = Number(status);
    return Number.isNaN(parsed) ? null : parsed;
  }
  if (status !== null && typeof status === 'object') {
    const obj = status as Record<string, unknown>;
    if (typeof obj.low === 'number') {
      return obj.low;
    }
    if (typeof obj.value === 'number') {
      return obj.value;
    }
  }

  return null;
}

function normalizeMessageId(id: string): string {
  return id.trim();
}

/** Host header must be hostname only (not https://domain). */
export function normalizeWebhookHost(raw: string | undefined | null): string | null {
  if (!raw) {
    return null;
  }

  let host = raw.trim();
  if (!host) {
    return null;
  }

  host = host.replace(/^https?:\/\//i, '');
  host = host.split('/')[0] ?? '';
  if (host.includes(':')) {
    host = host.split(':')[0] ?? host;
  }

  return host || null;
}

function hostFromUrl(url: string): string | null {
  try {
    return new URL(url).hostname;
  } catch {
    return null;
  }
}

function loopbackReceiptUrl(): string {
  const port = process.env.LARAVEL_RECEIPT_LOOPBACK_PORT?.trim() || '80';
  return `http://127.0.0.1:${port}/api/v1/webhooks/baileys-message-status`;
}

export type ReceiptWebhookMode = 'explicit' | 'internal' | 'loopback-auto' | 'public' | 'none';

export function receiptWebhookConfig(): {
  url: string | null;
  hasSecret: boolean;
  hostHeader: string | null;
  mode: ReceiptWebhookMode;
} {
  const hasSecret = Boolean(webhookSecret());

  const explicit = process.env.LARAVEL_RECEIPT_WEBHOOK_URL?.trim();
  if (explicit) {
    return {
      url: explicit,
      hasSecret,
      hostHeader:
        normalizeWebhookHost(process.env.LARAVEL_WEBHOOK_HOST) ?? hostFromUrl(explicit),
      mode: 'explicit',
    };
  }

  const internal = process.env.LARAVEL_RECEIPT_INTERNAL_URL?.trim();
  if (internal) {
    return {
      url: internal,
      hasSecret,
      hostHeader:
        normalizeWebhookHost(process.env.LARAVEL_WEBHOOK_HOST) ??
        hostFromUrl(process.env.LARAVEL_APP_URL ?? '') ??
        hostFromUrl(internal),
      mode: 'internal',
    };
  }

  const appUrl = process.env.LARAVEL_APP_URL?.trim();
  const preferPublic = process.env.LARAVEL_RECEIPT_USE_PUBLIC_URL === 'true';

  if (appUrl && !preferPublic) {
    const host = normalizeWebhookHost(process.env.LARAVEL_WEBHOOK_HOST) ?? hostFromUrl(appUrl);
    if (host) {
      return {
        url: loopbackReceiptUrl(),
        hasSecret,
        hostHeader: host,
        mode: 'loopback-auto',
      };
    }
  }

  if (appUrl) {
    return {
      url: `${appUrl.replace(/\/$/, '')}/api/v1/webhooks/baileys-message-status`,
      hasSecret,
      hostHeader: normalizeWebhookHost(process.env.LARAVEL_WEBHOOK_HOST),
      mode: 'public',
    };
  }

  return { url: null, hasSecret, hostHeader: null, mode: 'none' };
}

function webhookUrl(): string | null {
  return receiptWebhookConfig().url;
}

function webhookSecret(): string | null {
  const secret = process.env.BAILEYS_GATEWAY_SECRET?.trim();
  return secret || null;
}

function buildWebhookHeaders(secret: string, hostHeader: string | null): Record<string, string> {
  const headers: Record<string, string> = {
    Authorization: `Bearer ${secret}`,
    'Content-Type': 'application/json',
    Accept: 'application/json',
  };
  if (hostHeader) {
    headers.Host = hostHeader;
  }
  return headers;
}

/** Call from GET /health/receipt-probe to verify gateway → Laravel connectivity. */
export async function probeReceiptWebhook(): Promise<{
  ok: boolean;
  httpStatus: number | null;
  error: string | null;
  config: ReturnType<typeof receiptWebhookConfig>;
  laravelBody: unknown;
}> {
  const config = receiptWebhookConfig();
  const secret = webhookSecret();

  if (!config.url || !secret) {
    return {
      ok: false,
      httpStatus: null,
      error: 'Receipt webhook not configured (need LARAVEL_APP_URL + BAILEYS_GATEWAY_SECRET on gateway .env)',
      config,
      laravelBody: null,
    };
  }

  const headers = buildWebhookHeaders(secret, config.hostHeader);
  headers['X-Baileys-Receipt-Probe'] = '1';

  try {
    const response = await axios.post(
      config.url,
      {},
      { headers, timeout: 10_000, validateStatus: () => true }
    );

    const ok = response.status >= 200 && response.status < 300;
    return {
      ok,
      httpStatus: response.status,
      error: ok ? null : `Laravel returned HTTP ${response.status}`,
      config,
      laravelBody: response.data,
    };
  } catch (err) {
    const message = err instanceof Error ? err.message : String(err);
    return {
      ok: false,
      httpStatus: null,
      error: message,
      config,
      laravelBody: null,
    };
  }
}

async function postReceiptToLaravel(payload: {
  sessionId: string;
  messageId: string;
  referenceId: string;
  to: string;
  status: 'delivered' | 'read';
  source: 'messages.update' | 'message-receipt.update';
}): Promise<void> {
  const url = webhookUrl();
  const secret = webhookSecret();

  if (!url || !secret) {
    logger.warn(
      {
        status: payload.status,
        messageId: payload.messageId,
        referenceId: payload.referenceId || null,
        hasUrl: Boolean(url),
        hasSecret: Boolean(secret),
      },
      'receipt: webhook skipped — set LARAVEL_APP_URL (or LARAVEL_RECEIPT_WEBHOOK_URL) and BAILEYS_GATEWAY_SECRET on gateway'
    );
    return;
  }

  const { hostHeader } = receiptWebhookConfig();
  const headers = buildWebhookHeaders(secret, hostHeader);

  try {
    const response = await axios.post(
      url,
      {
        sessionId: payload.sessionId,
        messageId: normalizeMessageId(payload.messageId),
        referenceId: payload.referenceId || undefined,
        to: payload.to,
        status: payload.status,
        source: payload.source,
      },
      {
        headers,
        timeout: 10_000,
        validateStatus: () => true,
      }
    );

    if (response.status >= 200 && response.status < 300) {
      logger.info(
        {
          status: payload.status,
          messageId: payload.messageId,
          referenceId: payload.referenceId || null,
          source: payload.source,
          laravel: response.data,
        },
        'receipt: webhook OK'
      );
      return;
    }

    logger.warn(
      {
        status: payload.status,
        messageId: payload.messageId,
        httpStatus: response.status,
        body: response.data,
      },
      'receipt: webhook rejected'
    );
  } catch (err) {
    logger.error(
      { err, url, messageId: payload.messageId, referenceId: payload.referenceId || null, status: payload.status },
      'receipt: webhook request failed'
    );
  }
}

function resolveMeta(sessionId: string, messageId: string): OutboundMeta | undefined {
  return outboundByMessageKey.get(mapKey(sessionId, messageId));
}

function emitReceipt(
  sessionId: string,
  messageId: string,
  label: 'delivered' | 'read',
  source: 'messages.update' | 'message-receipt.update'
): void {
  const normalizedId = normalizeMessageId(messageId);
  const meta = resolveMeta(sessionId, normalizedId) ?? resolveMeta(sessionId, messageId);

  lastAckByKey.set(mapKey(sessionId, normalizedId), label);

  if (!meta) {
    logger.info(
      { sessionId, messageId: normalizedId, label, source },
      'receipt: WhatsApp ack received — posting webhook (match by whatsapp_message_id in Laravel)'
    );
  } else {
    logger.info(
      { sessionId, messageId: normalizedId, label, source, referenceId: meta.referenceId || null },
      'receipt: WhatsApp ack received — posting webhook'
    );
  }

  void postReceiptToLaravel({
    sessionId,
    messageId: normalizedId,
    referenceId: meta?.referenceId ?? '',
    to: meta?.to ?? '',
    status: label,
    source,
  });
}

function handleMessageUpdate(sessionId: string, updates: { key: proto.IMessageKey; update: Partial<proto.IWebMessageInfo> }[]): void {
  for (const { key, update } of updates) {
    if (!key?.fromMe || !key.id) {
      continue;
    }

    const statusCode = parseStatusCode(update.status);
    if (statusCode === null) {
      continue;
    }

    debugLog('receipt: messages.update', {
      sessionId,
      messageId: key.id,
      statusCode,
      statusName: WAMessageStatus[statusCode],
    });

    const label = statusLabelFromCode(statusCode);
    if (!label) {
      if (statusCode === WAMessageStatus.SERVER_ACK) {
        debugLog('receipt: server ack only (not delivered yet)', { messageId: key.id });
      }
      continue;
    }

    emitReceipt(sessionId, key.id, label, 'messages.update');
  }
}

function receiptImpliesRead(receipt: proto.IUserReceipt): boolean {
  const readTs = Number(receipt.readTimestamp ?? 0);
  const playedTs = Number(receipt.playedTimestamp ?? 0);
  return readTs > 0 || playedTs > 0;
}

function receiptImpliesDelivered(receipt: proto.IUserReceipt): boolean {
  const receiptTs = Number(receipt.receiptTimestamp ?? 0);
  return receiptTs > 0;
}

type ReceiptRow = {
  key?: proto.IMessageKey;
  receipt?: proto.IUserReceipt | proto.IUserReceipt[];
  userReceipt?: proto.IUserReceipt | proto.IUserReceipt[];
};

function handleMessageReceiptUpdate(sessionId: string, updates: ReceiptRow[]): void {
  for (const row of updates) {
    const key = row.key;
    if (!key?.fromMe || !key.id) {
      continue;
    }

    const raw = row.receipt ?? row.userReceipt;
    const receipts = raw === undefined ? [] : Array.isArray(raw) ? raw : [raw];

    for (const userReceipt of receipts) {
      debugLog('receipt: message-receipt.update', {
        sessionId,
        messageId: key.id,
        userJid: userReceipt.userJid,
        readTimestamp: userReceipt.readTimestamp,
        receiptTimestamp: userReceipt.receiptTimestamp,
      });

      if (receiptImpliesRead(userReceipt)) {
        emitReceipt(sessionId, key.id, 'read', 'message-receipt.update');
        continue;
      }

      if (receiptImpliesDelivered(userReceipt)) {
        emitReceipt(sessionId, key.id, 'delivered', 'message-receipt.update');
      }
    }
  }
}

export function getLastReceiptAck(sessionId: string, messageId: string): string | null {
  return lastAckByKey.get(mapKey(sessionId, normalizeMessageId(messageId))) ?? null;
}

export function logReceiptStartupConfig(): void {
  const { url, hasSecret, hostHeader, mode } = receiptWebhookConfig();
  if (!url || !hasSecret) {
    logger.warn(
      {
        hasUrl: Boolean(url),
        hasSecret,
        mode,
        hint:
          'Put LARAVEL_APP_URL + BAILEYS_GATEWAY_SECRET in whatsapp-gateway/.env (not only Laravel). Secret must be non-empty and match Laravel.',
      },
      'receipt: webhooks DISABLED — delivered_at/read_at will stay null in Laravel'
    );
    return;
  }

  logger.info(
    {
      mode,
      postUrl: url,
      hostHeader: hostHeader ?? '(default)',
      note:
        mode === 'loopback-auto'
          ? 'Using 127.0.0.1 + Host header — receipt env vars on Laravel .env are ignored'
          : undefined,
    },
    'receipt: webhooks enabled'
  );
}

export function attachMessageReceiptListener(sock: WASocket, sessionId: string): void {
  sock.ev.on('messages.update', (updates) => {
    handleMessageUpdate(sessionId, updates);
  });

  sock.ev.on('message-receipt.update', (updates: ReceiptRow[]) => {
    handleMessageReceiptUpdate(sessionId, updates);
  });

  logger.info({ sessionId }, 'receipt: listeners attached (messages.update + message-receipt.update)');
}

logReceiptStartupConfig();
