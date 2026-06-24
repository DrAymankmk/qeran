<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Models\WhatsappSessionLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class WhatsAppSystemSessionLogService
{
    /**
     * @param  array<string, mixed>  $context
     */
    public static function record(
        string $event,
        string $message,
        array $context = [],
        string $level = WhatsappSessionLog::LEVEL_INFO,
        ?int $adminId = null,
        ?int $dedupeSeconds = null
    ): ?WhatsappSessionLog {
        $sessionId = WhatsAppSystemSessionService::sessionId();

        if ($dedupeSeconds !== null && $dedupeSeconds > 0) {
            $dedupeKey = self::dedupeCacheKey($sessionId, $event, $context);
            if (Cache::has($dedupeKey)) {
                return null;
            }
            Cache::put($dedupeKey, 1, now()->addSeconds($dedupeSeconds));
        }

        return WhatsappSessionLog::query()->create([
            'session_id' => $sessionId,
            'event' => $event,
            'level' => $level,
            'message' => $message,
            'context' => self::sanitizeContext($context),
            'admin_id' => $adminId ?? self::currentAdminId(),
        ]);
    }

    /**
     * Log connect/disconnect/reconnect transitions detected during gateway sync.
     *
     * @param  array<string, mixed>  $gatewayData
     */
    public static function logGatewayTransition(
        WhatsappSession $before,
        WhatsappSession $after,
        array $gatewayData
    ): void {
        $wasLive = $before->status === 'connected' && $before->connected_at !== null;
        $isLive = $after->status === 'connected' && $after->connected_at !== null;
        $context = self::gatewaySnapshot($gatewayData);

        if (! $wasLive && $isLive) {
            Cache::forget('whatsapp_socket_lost_at:'.WhatsAppSystemSessionService::sessionId());

            self::record(
                WhatsappSessionLog::EVENT_CONNECTED,
                __('admin.whatsapp-log-connected', ['phone' => $after->phone ?? '—']),
                array_merge($context, ['phone' => $after->phone]),
                WhatsappSessionLog::LEVEL_SUCCESS,
                null,
                30
            );

            return;
        }

        if ($wasLive && ! $isLive) {
            $reason = self::disconnectReason($gatewayData, $after);
            $disconnectAt = now();
            $displayTz = (string) config('app.display_timezone', 'Asia/Riyadh');
            $stillLinked = (bool) ($gatewayData['registeredOnDisk'] ?? false);
            $durationSeconds = $before->connected_at
                ? (int) $before->connected_at->diffInSeconds($disconnectAt)
                : null;

            $context = array_merge($context, [
                'reason' => $reason,
                'previous_phone' => $before->phone,
                'session_duration_seconds' => $durationSeconds,
                'session_duration_human' => self::formatDuration($durationSeconds),
                'connected_at' => $before->connected_at?->toIso8601String(),
                'connected_at_display' => $before->connected_at?->timezone($displayTz)->format('Y-m-d H:i:s'),
                'disconnected_at' => $disconnectAt->toIso8601String(),
                'disconnected_at_display' => $disconnectAt->timezone($displayTz)->format('Y-m-d H:i:s'),
                'still_linked_on_phone' => $stillLinked,
                'dashboard_status' => $after->status,
            ]);

            $event = $stillLinked ? WhatsappSessionLog::EVENT_SOCKET_LOST : WhatsappSessionLog::EVENT_DISCONNECTED;
            $message = $stillLinked
                ? __('admin.whatsapp-log-socket-lost', [
                    'at' => $disconnectAt->timezone($displayTz)->format('Y-m-d H:i:s'),
                    'duration' => self::formatDuration($durationSeconds),
                ])
                : __('admin.whatsapp-log-disconnected', ['reason' => $reason]);

            self::record(
                $event,
                $message,
                $context,
                WhatsappSessionLog::LEVEL_WARNING,
                null,
                60
            );

            if ($stillLinked) {
                Cache::put(
                    'whatsapp_socket_lost_at:'.WhatsAppSystemSessionService::sessionId(),
                    $disconnectAt->toIso8601String(),
                    now()->addDays(7)
                );
            }

            return;
        }

        if ($after->status === 'reconnecting' && $before->status !== 'reconnecting') {
            self::record(
                WhatsappSessionLog::EVENT_RECONNECTING,
                __('admin.whatsapp-log-reconnecting'),
                $context,
                WhatsappSessionLog::LEVEL_INFO,
                null,
                120
            );

            return;
        }

        if ($after->status === 'pending_qr' && $before->status !== 'pending_qr') {
            self::record(
                WhatsappSessionLog::EVENT_PENDING_QR,
                __('admin.whatsapp-log-pending-qr'),
                $context,
                WhatsappSessionLog::LEVEL_INFO,
                null,
                120
            );
        }
    }

    /**
     * @param  array<string, mixed>  $gatewayData
     */
    public static function disconnectReason(array $gatewayData, WhatsappSession $record): string
    {
        if (WhatsAppSystemSessionService::adminRequestedDisconnect()) {
            return __('admin.whatsapp-log-reason-admin-disconnect');
        }

        $status = (string) ($gatewayData['status'] ?? $record->status);
        $socketAlive = (bool) ($gatewayData['socketAlive'] ?? false);
        $registered = (bool) ($gatewayData['registeredOnDisk'] ?? false);
        $reconnecting = (bool) ($gatewayData['reconnecting'] ?? false);

        if (! $registered) {
            return __('admin.whatsapp-log-reason-unregistered');
        }

        if ($reconnecting || $status === 'reconnecting') {
            return __('admin.whatsapp-log-reason-socket-down-reconnecting');
        }

        if ($status === 'connected' && ! $socketAlive) {
            return __('admin.whatsapp-log-reason-socket-closed-still-linked');
        }

        if ($status === 'disconnected') {
            return __('admin.whatsapp-log-reason-gateway-disconnected');
        }

        return __('admin.whatsapp-log-reason-unknown', ['status' => $status]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function gatewaySnapshot(array $data): array
    {
        return array_filter([
            'status' => $data['status'] ?? null,
            'socket_alive' => $data['socketAlive'] ?? null,
            'registered_on_disk' => $data['registeredOnDisk'] ?? null,
            'reconnecting' => $data['reconnecting'] ?? null,
            'linked_on_whatsapp' => $data['linkedOnWhatsApp'] ?? null,
            'phone' => $data['phone'] ?? null,
            'admin_disconnect' => $data['admin_disconnect'] ?? null,
        ], fn ($value) => $value !== null);
    }

    public static function recent(int $limit = 50): Collection
    {
        return WhatsappSessionLog::query()
            ->where('session_id', WhatsAppSystemSessionService::sessionId())
            ->with('admin:id,name')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public static function paginate(int $perPage = 25, int $page = 1): LengthAwarePaginator
    {
        return WhatsappSessionLog::query()
            ->where('session_id', WhatsAppSystemSessionService::sessionId())
            ->with('admin:id,name')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', max(1, $page));
    }

    public static function clearAll(): int
    {
        return WhatsappSessionLog::query()
            ->where('session_id', WhatsAppSystemSessionService::sessionId())
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected static function dedupeCacheKey(string $sessionId, string $event, array $context): string
    {
        $fingerprint = md5(json_encode([
            'event' => $event,
            'status' => $context['status'] ?? null,
            'reason' => $context['reason'] ?? null,
            'error' => $context['error'] ?? null,
            'gateway_error' => $context['gateway_error'] ?? null,
        ]));

        return 'whatsapp_session_log_dedupe:'.$sessionId.':'.$event.':'.$fingerprint;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected static function sanitizeContext(array $context): array
    {
        foreach (['to', 'phone', 'phone_masked', 'previous_phone'] as $key) {
            if (! isset($context[$key]) || ! is_string($context[$key])) {
                continue;
            }
            $context[$key] = self::maskPhone($context[$key]);
        }

        return $context;
    }

    protected static function maskPhone(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if (strlen($digits) <= 4) {
            return '****';
        }

        return str_repeat('*', strlen($digits) - 4).substr($digits, -4);
    }

    protected static function currentAdminId(): ?int
    {
        $admin = auth('admin')->user();

        return $admin?->id;
    }

    protected static function formatDuration(?int $seconds): string
    {
        if ($seconds === null || $seconds < 0) {
            return '—';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }

        return sprintf('%ds', $secs);
    }

    public static function formatDurationPublic(?int $seconds): string
    {
        return self::formatDuration($seconds);
    }
}
