import { getSessionMeta, getSocket, startSession } from './manager.js';

export async function sendText(
  sessionId: string,
  to: string,
  message: string
): Promise<{ idMessage?: string; sent: boolean }> {
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

  const digits = to.replace(/\D/g, '');
  if (!digits) {
    throw new Error('Invalid phone number');
  }

  const jid = `${digits}@s.whatsapp.net`;
  const result = await sock.sendMessage(jid, { text: message });

  return {
    idMessage: result?.key?.id ?? undefined,
    sent: true,
  };
}
