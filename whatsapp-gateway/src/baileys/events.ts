import type { WASocket } from '@whiskeysockets/baileys';
import axios from 'axios';
import pino from 'pino';

const logger = pino({ level: process.env.LOG_LEVEL ?? 'info' });

const registered = new WeakSet<WASocket>();

export function registerInboundHandler(sock: WASocket, sessionId: string): void {
  if (registered.has(sock)) {
    return;
  }
  registered.add(sock);

  sock.ev.on('messages.upsert', async ({ messages, type }) => {
    if (type !== 'notify') {
      return;
    }

    const webhookUrl = process.env.LARAVEL_WEBHOOK_URL;
    if (!webhookUrl) {
      return;
    }

    for (const msg of messages) {
      if (msg.key.fromMe) {
        continue;
      }

      const from = msg.key.remoteJid?.replace('@s.whatsapp.net', '').replace(/\D/g, '') ?? '';
      const body =
        msg.message?.conversation ??
        msg.message?.extendedTextMessage?.text ??
        '';

      if (!from || !body) {
        continue;
      }

      try {
        const secret = process.env.LARAVEL_WEBHOOK_SECRET ?? process.env.BAILEYS_GATEWAY_SECRET;
        await axios.post(
          webhookUrl,
          {
            type: 'incoming',
            sessionId,
            from,
            body,
          },
          {
            headers: secret ? { Authorization: `Bearer ${secret}` } : {},
            timeout: 15000,
          }
        );
      } catch (err) {
        logger.error({ sessionId, from, err }, 'Failed to forward message to Laravel');
      }
    }
  });
}
