<?php

namespace App\Services\External;

class BaileysWhatsApp
{
    public static function send(string $to, string $message, string $referenceId = ''): object
    {
        return self::mapSendResponse(
            BaileysGateway::send($to, $message, BaileysGateway::systemSessionId(), $referenceId)
        );
    }

    public static function sendLegacy(
        string $phone,
        string $activationCode = '',
        string $message = 'لقد تم تسجيل حسابك بنجاح كود التفعيل ',
        string $referenceId = ''
    ): object {
        $full = trim($activationCode !== '' ? $message.' '.$activationCode : $message);

        return self::send($phone, $full, $referenceId);
    }

    public static function sendFromSession(
        string $sessionId,
        string $to,
        string $message,
        string $referenceId = ''
    ): object {
        return self::mapSendResponse(
            BaileysGateway::send($to, $message, $sessionId, $referenceId)
        );
    }

    protected static function mapSendResponse(array $result): object
    {
        if ($result['ok'] && is_array($result['data'])) {
            $json = $result['data'];

            return (object) [
                'sent' => ($json['sent'] ?? true) ? 'true' : 'false',
                'id' => $json['idMessage'] ?? null,
                'status' => 'queued',
            ];
        }

        return (object) [
            'sent' => 'false',
            'error' => (object) [
                'message' => $result['error'] ?? 'Baileys send failed',
                'status' => $result['status'] ?? 0,
            ],
        ];
    }
}
