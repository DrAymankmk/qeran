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

export function receiptWebhookConfig(): {
  url: string | null;
  hasSecret: boolean;
  hostHeader: string | null;
} {
  const explicit = process.env.LARAVEL_RECEIPT_WEBHOOK_URL?.trim();
  if (explicit) {
    return {
      url: explicit,
      hasSecret: Boolean(webhookSecret()),
      hostHeader: process.env.LARAVEL_WEBHOOK_HOST?.trim() || null,
    };
  }

  const internal = process.env.LARAVEL_RECEIPT_INTERNAL_URL?.trim();
  if (internal) {
    return {
      url: internal,
      hasSecret: Boolean(webhookSecret()),
      hostHeader: process.env.LARAVEL_WEBHOOK_HOST?.trim() || null,
    };
  }

  const appUrl = process.env.LARAVEL_APP_URL?.trim();
  if (!appUrl) {
    return { url: null, hasSecret: Boolean(webhookSecret()), hostHeader: null };
  }

  return {
    url: `${appUrl.replace(/\/$/, '')}/api/v1/webhooks/baileys-message-status`,
    hasSecret: Boolean(webhookSecret()),
    hostHeader: process.env.LARAVEL_WEBHOOK_HOST?.trim() || null,
  };
}

function webhookUrl(): string | null {
  return receiptWebhookConfig().url;
}

function webhookSecret(): string | null {
  const secret = process.env.BAILEYS_GATEWAY_SECRET?.trim();
  return secret || null;
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
  const headers: Record<string, string> = {
    Authorization: `Bearer ${secret}`,
    'Content-Type': 'application/json',
    Accept: 'application/json',
  };
  if (hostHeader) {
    headers.Host = hostHeader;
  }

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
  const { url, hasSecret } = receiptWebhookConfig();
  if (!url || !hasSecret) {
    logger.warn(
      {
        hasUrl: Boolean(url),
        hasSecret,
        hint: 'Set LARAVEL_APP_URL (or LARAVEL_RECEIPT_INTERNAL_URL) and BAILEYS_GATEWAY_SECRET on the gateway process',
      },
      'receipt: webhooks DISABLED — delivered_at/read_at will stay null in Laravel'
    );
    return;
  }

  let host = url;
  try {
    host = new URL(url).host;
  } catch {
    /* keep raw */
  }

  logger.info(
    { webhookHost: host, internal: Boolean(process.env.LARAVEL_RECEIPT_INTERNAL_URL?.trim()) },
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
