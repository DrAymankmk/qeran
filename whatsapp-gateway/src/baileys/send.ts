import { getSessionMeta, startSession } from './manager.js';
import { registerOutboundMessage } from './receipts.js';

type SendOptions = {
  text?: string;
  mediaUrl?: string;
  mediaBase64?: string;
  mediaType?: 'image' | 'video';
  referenceId?: string;
};

async function connectedSocket(sessionId: string) {
  let meta = getSessionMeta(sessionId);

  if (!meta?.sock || meta.status !== 'connected') {
    await startSession(sessionId);
    meta = getSessionMeta(sessionId);
  }

  if (!meta) {
    throw new Error(`Session "${sessionId}" is not connected. Call POST /sessions and scan QR.`);
  }

  const sock = meta.sock;
  if (!sock || meta.status !== 'connected') {
    if (meta.status === 'pending_qr' || meta.qr) {
      throw new Error(`Session "${sessionId}" needs QR scan. GET /sessions/${sessionId}/qr`);
    }
    throw new Error(`Session "${sessionId}" is not connected. Call POST /sessions and scan QR.`);
  }

  return sock;
}

export async function sendMessage(
  sessionId: string,
  to: string,
  options: SendOptions
): Promise<{ idMessage?: string; sent: boolean }> {
  const sock = await connectedSocket(sessionId);
  const digits = to.replace(/\D/g, '');
  if (!digits) {
    throw new Error('Invalid phone number');
  }

  const jid = `${digits}@s.whatsapp.net`;
  const caption = (options.text ?? '').trim();
  const mediaUrl = (options.mediaUrl ?? '').trim();
  const mediaBase64 = (options.mediaBase64 ?? '').replace(/^data:[^;]+;base64,/, '').trim();
  const mediaType = options.mediaType === 'video' ? 'video' : 'image';

  let result;
  if (mediaBase64) {
    const buffer = Buffer.from(mediaBase64, 'base64');
    result =
      mediaType === 'video'
        ? await sock.sendMessage(jid, {
            video: buffer,
            caption: caption || undefined,
          })
        : await sock.sendMessage(jid, {
            image: buffer,
            caption: caption || undefined,
            mimetype: 'image/png',
          });
  } else if (mediaUrl) {
    result =
      mediaType === 'video'
        ? await sock.sendMessage(jid, {
            video: { url: mediaUrl },
            caption: caption || undefined,
          })
        : await sock.sendMessage(jid, {
            image: { url: mediaUrl },
            caption: caption || undefined,
          });
  } else {
    if (!caption) {
      throw new Error('message is required when no media is attached');
    }
    result = await sock.sendMessage(jid, { text: caption });
  }

  const idMessage = result?.key?.id ?? undefined;
  registerOutboundMessage(sessionId, idMessage, options.referenceId, digits);

  return {
    idMessage,
    sent: true,
  };
}

export async function sendText(
  sessionId: string,
  to: string,
  message: string,
  referenceId?: string
): Promise<{ idMessage?: string; sent: boolean }> {
  return sendMessage(sessionId, to, { text: message, referenceId });
}
