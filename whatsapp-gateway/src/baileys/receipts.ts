import axios from 'axios';
import type { WASocket } from '@whiskeysockets/baileys';
import pino from 'pino';

const logger = pino({ level: process.env.LOG_LEVEL ?? 'info' });

/** Baileys WAMessageStatus numeric values */
const STATUS_SERVER_ACK = 2;
const STATUS_DELIVERY_ACK = 3;
const STATUS_READ = 4;
const STATUS_PLAYED = 5;

type OutboundMeta = {
  referenceId: string;
  to: string;
  sessionId: string;
};

const outboundByKey = new Map<string, OutboundMeta>();

function mapKey(sessionId: string, messageId: string): string {
  return `${sessionId}:${messageId}`;
}

export function registerOutboundMessage(
  sessionId: string,
  messageId: string | undefined,
  referenceId: string | undefined,
  to: string
): void {
  if (!messageId || !referenceId) {
    return;
  }

  outboundByKey.set(mapKey(sessionId, messageId), {
    referenceId,
    to,
    sessionId,
  });
}

function statusLabel(code: number): 'delivered' | 'read' | null {
  if (code === STATUS_DELIVERY_ACK) {
    return 'delivered';
  }
  if (code === STATUS_READ || code === STATUS_PLAYED) {
    return 'read';
  }

  return null;
}

function webhookUrl(): string | null {
  const explicit = process.env.LARAVEL_RECEIPT_WEBHOOK_URL?.trim();
  if (explicit) {
    return explicit;
  }

  const appUrl = process.env.LARAVEL_APP_URL?.trim();
  if (!appUrl) {
    return null;
  }

  return `${appUrl.replace(/\/$/, '')}/api/v1/webhooks/baileys-message-status`;
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
}): Promise<void> {
  const url = webhookUrl();
  const secret = webhookSecret();

  if (!url || !secret) {
    logger.debug(
      { status: payload.status, referenceId: payload.referenceId },
      'receipt webhook skipped — set LARAVEL_RECEIPT_WEBHOOK_URL and BAILEYS_GATEWAY_SECRET'
    );
    return;
  }

  try {
    await axios.post(url, payload, {
      headers: {
        Authorization: `Bearer ${secret}`,
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      timeout: 10_000,
    });
  } catch (err) {
    logger.warn({ err, url, referenceId: payload.referenceId, status: payload.status }, 'receipt webhook failed');
  }
}

export function attachMessageReceiptListener(sock: WASocket, sessionId: string): void {
  sock.ev.on('messages.update', (updates) => {
    for (const { key, update } of updates) {
      const status = update.status;

      if (!key?.fromMe || !key.id || status === undefined || status === null) {
        continue;
      }

      const label = statusLabel(Number(status));
      if (!label) {
        continue;
      }

      const meta = outboundByKey.get(mapKey(sessionId, key.id));
      if (!meta) {
        continue;
      }

      void postReceiptToLaravel({
        sessionId,
        messageId: key.id,
        referenceId: meta.referenceId,
        to: meta.to,
        status: label,
      });
    }
  });
}
