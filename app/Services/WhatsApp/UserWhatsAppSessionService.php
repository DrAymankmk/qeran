<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Services\External\BaileysGateway;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserWhatsAppSessionService
{
    public static function disconnectUser(int $userId): bool
    {
        if (! BaileysGateway::isConfigured()) {
            return false;
        }

        $sessionId = BaileysGateway::sessionIdForUser($userId);

        Cache::put(self::disconnectCacheKey($sessionId), 1, now()->addDays(30));
        Cache::forget('whatsapp_status_reconnect:'.$sessionId);
        Cache::forget('whatsapp_finalize_pairing:'.$sessionId);

        $deleteResult = BaileysGateway::deleteSession($sessionId, 35);
        if (! $deleteResult['ok']) {
            Log::warning('Admin WhatsApp client disconnect: gateway delete failed', [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'error' => $deleteResult['error'] ?? null,
            ]);
        }

        WhatsappSession::query()->where('user_id', $userId)->update([
            'status' => 'disconnected',
            'phone' => null,
            'connected_at' => null,
            'disconnected_at' => now(),
            'last_seen_at' => now(),
        ]);

        Log::info('Admin disconnected client WhatsApp session', [
            'user_id' => $userId,
            'session_id' => $sessionId,
        ]);

        return true;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function liveGatewayStatus(string $sessionId): ?array
    {
        if (! BaileysGateway::isConfigured()) {
            return null;
        }

        $result = BaileysGateway::getStatus($sessionId, true, 10);

        return $result['ok'] ? ($result['data'] ?? null) : null;
    }

    /**
     * @param  array<string, mixed>|null  $live
     * @return array{linked: bool, status: string, socket_alive: bool, label: string}
     */
    public static function interpretLiveStatus(?array $live, ?string $dbStatus = null): array
    {
        if (! $live) {
            return [
                'linked' => false,
                'status' => $dbStatus ?? 'unknown',
                'socket_alive' => false,
                'label' => 'unavailable',
            ];
        }

        $status = (string) ($live['status'] ?? 'disconnected');
        $socketAlive = (bool) ($live['socketAlive'] ?? false);
        $registered = (bool) ($live['registeredOnDisk'] ?? false);
        $linked = (bool) ($live['linkedOnWhatsApp'] ?? false);

        if (! $linked && $status === 'connected' && $socketAlive && $registered) {
            $linked = true;
        }

        return [
            'linked' => $linked,
            'status' => $status,
            'socket_alive' => $socketAlive,
            'label' => $linked ? 'linked' : $status,
        ];
    }

    public static function disconnectCacheKey(string $sessionId): string
    {
        return 'whatsapp_session_disconnected:'.$sessionId;
    }

    public static function formatPhoneDisplay(?string $countryCode, ?string $phone, ?string $sessionPhone = null): string
    {
        if ($sessionPhone) {
            return PhoneNumber::formatForWhatsAppDisplay(
                PhoneNumber::e164ForWhatsAppPairing($countryCode, $sessionPhone)
            );
        }

        if ($phone) {
            return PhoneNumber::formatForWhatsAppDisplay(
                PhoneNumber::e164ForWhatsAppPairing($countryCode, $phone)
            );
        }

        return '—';
    }
}
