<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Models\WhatsappSessionLog;
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
        $before = $record->replicate();
        $wasConnected = $record->status === 'connected' && $record->connected_at !== null;

        $updates = [
            'status' => $isLive ? 'connected' : ($isReconnecting ? 'reconnecting' : ($status === 'pending_qr' ? 'pending_qr' : 'disconnected')),
            'phone' => $isLive ? $phone : ($record->phone && $registered ? $record->phone : null),
            'last_seen_at' => now(),
        ];

        if ($isLive && ! $wasConnected) {
            $updates['connected_at'] = now();
            $updates['disconnected_at'] = null;
            Cache::forget('whatsapp_socket_lost_at:'.$sessionId);
        } elseif ($isLive) {
            $updates['disconnected_at'] = null;
            Cache::forget('whatsapp_socket_lost_at:'.$sessionId);
        } elseif ($isReconnecting) {
            $updates['disconnected_at'] = null;
        } elseif ($wasConnected && ! $isLive) {
            $updates['disconnected_at'] = now();
        }

        $record->fill($updates);
        $record->save();

        $fresh = $record->fresh();
        if ($fresh && ($before->status !== $fresh->status || $wasConnected !== $isLive)) {
            WhatsAppSystemSessionLogService::logGatewayTransition($before, $fresh, $gatewayData);
        }

        return $fresh;
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

        $socketLostRaw = Cache::get('whatsapp_socket_lost_at:'.$record->session_id);
        $socketLostAt = is_string($socketLostRaw) ? \Illuminate\Support\Carbon::parse($socketLostRaw) : null;

        return [
            'connected_at' => $connectedAt?->toIso8601String(),
            'connected_at_display' => self::formatDisplayTime($connectedAt),
            'disconnected_at' => $disconnectedAt?->toIso8601String(),
            'disconnected_at_display' => self::formatDisplayTime($disconnectedAt),
            'last_seen_at' => $record->last_seen_at?->toIso8601String(),
            'uptime_seconds' => $uptimeSeconds,
            'last_session_seconds' => $lastSessionSeconds,
            'last_session_human' => WhatsAppSystemSessionLogService::formatDurationPublic($lastSessionSeconds),
            'socket_lost_at' => $socketLostAt?->toIso8601String(),
            'socket_lost_at_display' => self::formatDisplayTime($socketLostAt),
            'admin_disconnect_locked' => self::adminRequestedDisconnect(),
        ];
    }

    protected static function formatDisplayTime(?\Illuminate\Support\Carbon $time): ?string
    {
        if ($time === null) {
            return null;
        }

        return $time->timezone(config('app.display_timezone', 'Asia/Riyadh'))->format('Y-m-d H:i:s');
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
            || in_array($connectionStatus, ['pending_qr', 'pending_pairing', 'starting'], true)
        ) {
            return $data;
        }

        // registered + socket down (reconnecting or disconnected) — attempt recovery

        $reconnectKey = 'whatsapp_status_reconnect:'.$sessionId;
        if (Cache::has($reconnectKey)) {
            return $data;
        }

        Cache::put($reconnectKey, 1, now()->addSeconds(8));

        WhatsAppSystemSessionLogService::record(
            WhatsappSessionLog::EVENT_AUTO_RECONNECT,
            __('admin.whatsapp-log-auto-reconnect'),
            WhatsAppSystemSessionLogService::gatewaySnapshot($data),
            WhatsappSessionLog::LEVEL_INFO,
            null,
            120
        );

        BaileysGateway::startSession($sessionId, 45);
        $retry = BaileysGateway::getStatus($sessionId, true, 30);

        if (! $retry['ok']) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_AUTO_RECONNECT_FAILED,
                __('admin.whatsapp-log-auto-reconnect-failed'),
                array_merge(
                    WhatsAppSystemSessionLogService::gatewaySnapshot($data),
                    ['error' => $retry['error'] ?? null]
                ),
                WhatsappSessionLog::LEVEL_ERROR,
                null,
                300
            );
        }

        return $retry['ok'] ? ($retry['data'] ?? $data) : $data;
    }
}
