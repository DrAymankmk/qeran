<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Services\External\BaileysGateway;
use Illuminate\Support\Facades\Cache;

class WhatsAppSystemSessionService
{
    public static function sessionId(): string
    {
        return BaileysGateway::systemSessionId();
    }

    public static function disconnectCacheKey(?string $sessionId = null): string
    {
        return UserWhatsAppSessionService::disconnectCacheKey($sessionId ?? self::sessionId());
    }

    public static function adminRequestedDisconnect(?string $sessionId = null): bool
    {
        return Cache::has(self::disconnectCacheKey($sessionId));
    }

    public static function markAdminDisconnect(?string $sessionId = null): void
    {
        $sessionId = $sessionId ?? self::sessionId();
        Cache::put(self::disconnectCacheKey($sessionId), 1, now()->addDays(30));
        Cache::forget('whatsapp_status_reconnect:'.$sessionId);
    }

    public static function clearAdminDisconnect(?string $sessionId = null): void
    {
        $sessionId = $sessionId ?? self::sessionId();
        Cache::forget(self::disconnectCacheKey($sessionId));

        WhatsappSession::query()
            ->where('session_id', $sessionId)
            ->update(['disconnected_at' => null]);
    }

    public static function record(): WhatsappSession
    {
        return WhatsappSession::query()->firstOrCreate(
            ['session_id' => self::sessionId()],
            ['user_id' => null, 'status' => 'disconnected']
        );
    }

    /**
     * @param  array<string, mixed>  $gatewayData
     */
    public static function syncFromGateway(array $gatewayData): WhatsappSession
    {
        $sessionId = self::sessionId();
        $status = (string) ($gatewayData['status'] ?? 'disconnected');
        $socketAlive = (bool) ($gatewayData['socketAlive'] ?? false);
        $registered = (bool) ($gatewayData['registeredOnDisk'] ?? false);
        $reconnecting = (bool) ($gatewayData['reconnecting'] ?? false);
        $phone = $gatewayData['phone'] ?? null;
        $isLive = $status === 'connected' && $socketAlive && $registered;
        $isReconnecting = $registered && ($reconnecting || $status === 'reconnecting' || (! $socketAlive && ! in_array($status, ['pending_qr', 'pending_pairing'], true)));

        $record = self::record();
        $wasConnected = $record->status === 'connected' && $record->connected_at !== null;

        $updates = [
            'status' => $isLive ? 'connected' : ($isReconnecting ? 'reconnecting' : ($status === 'pending_qr' ? 'pending_qr' : 'disconnected')),
            'phone' => $isLive ? $phone : ($record->phone && $registered ? $record->phone : null),
            'last_seen_at' => now(),
        ];

        if ($isLive && ! $wasConnected) {
            $updates['connected_at'] = now();
            $updates['disconnected_at'] = null;
        } elseif ($isLive) {
            $updates['disconnected_at'] = null;
        } elseif ($isReconnecting) {
            $updates['disconnected_at'] = null;
        } elseif ($wasConnected && ! $isLive) {
            $updates['disconnected_at'] = now();
        }

        $record->fill($updates);
        $record->save();

        return $record->fresh();
    }

    public static function markDisconnected(): WhatsappSession
    {
        $record = self::record();

        $record->update([
            'status' => 'disconnected',
            'phone' => null,
            'connected_at' => null,
            'disconnected_at' => now(),
            'last_seen_at' => now(),
        ]);

        return $record->fresh();
    }

    /**
     * @return array<string, mixed>
     */
    public static function sessionMeta(?WhatsappSession $record = null): array
    {
        $record ??= self::record();

        $connectedAt = $record->connected_at;
        $disconnectedAt = $record->disconnected_at;
        $now = now();

        $uptimeSeconds = null;
        if ($record->status === 'connected' && $connectedAt) {
            $uptimeSeconds = (int) $connectedAt->diffInSeconds($now);
        }

        $lastSessionSeconds = null;
        if ($connectedAt && $disconnectedAt && $disconnectedAt->greaterThan($connectedAt)) {
            $lastSessionSeconds = (int) $connectedAt->diffInSeconds($disconnectedAt);
        }

        return [
            'connected_at' => $connectedAt?->toIso8601String(),
            'disconnected_at' => $disconnectedAt?->toIso8601String(),
            'last_seen_at' => $record->last_seen_at?->toIso8601String(),
            'uptime_seconds' => $uptimeSeconds,
            'last_session_seconds' => $lastSessionSeconds,
            'admin_disconnect_locked' => self::adminRequestedDisconnect(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function maybeReconnect(string $sessionId, array $data): array
    {
        if (self::adminRequestedDisconnect($sessionId)) {
            return $data;
        }

        $connectionStatus = $data['status'] ?? 'disconnected';
        $registeredOnDisk = (bool) ($data['registeredOnDisk'] ?? false);
        $socketAlive = (bool) ($data['socketAlive'] ?? false);

        if (
            $connectionStatus === 'connected'
            || $socketAlive
            || ! $registeredOnDisk
            || in_array($connectionStatus, ['pending_qr', 'pending_pairing', 'starting', 'reconnecting'], true)
        ) {
            return $data;
        }

        $reconnectKey = 'whatsapp_status_reconnect:'.$sessionId;
        if (Cache::has($reconnectKey)) {
            return $data;
        }

        Cache::put($reconnectKey, 1, now()->addSeconds(8));

        BaileysGateway::startSession($sessionId, 45);
        $retry = BaileysGateway::getStatus($sessionId, true, 30);

        return $retry['ok'] ? ($retry['data'] ?? $data) : $data;
    }
}
