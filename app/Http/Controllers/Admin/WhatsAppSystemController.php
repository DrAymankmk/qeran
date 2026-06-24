<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSessionLog;
use App\Services\External\BaileysGateway;
use App\Services\External\BaileysWhatsApp;
use App\Services\WhatsApp\WhatsAppSystemSessionLogService;
use App\Services\WhatsApp\WhatsAppSystemSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Support\PhoneNumber;
use Illuminate\View\View;

class WhatsAppSystemController extends Controller
{
    public function index(): View
    {
        $configured = BaileysGateway::isConfigured();

        return view('admin.whatsapp-system.index', [
            'configured' => $configured,
            'gatewayUrl' => config('services.baileys.gateway_internal_url')
                ?: config('services.baileys.gateway_url'),
            'sessionId' => BaileysGateway::systemSessionId(),
            'status' => ['ok' => true, 'data' => null, 'error' => null, 'loading' => $configured],
            'qr' => ['ok' => false, 'data' => null, 'error' => null],
            'autoGenerateQr' => session('wa_auto_generate', false),
            'activityLogs' => $configured
                ? WhatsAppSystemSessionLogService::paginate(15)
                : new LengthAwarePaginator([], 0, 15),
        ]);
    }

    public function status(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-gateway-not-configured'),
            ], 503);
        }

        $sessionId = BaileysGateway::systemSessionId();

        if (WhatsAppSystemSessionService::adminRequestedDisconnect($sessionId)) {
            $record = WhatsAppSystemSessionService::record();

            return response()->json([
                'ok' => true,
                'data' => [
                    'sessionId' => $sessionId,
                    'status' => 'disconnected',
                    'phone' => null,
                    'registeredOnDisk' => false,
                    'socketAlive' => false,
                    'admin_disconnect' => true,
                ],
                'session_meta' => WhatsAppSystemSessionService::sessionMeta($record),
                'error' => null,
            ]);
        }

        $status = BaileysGateway::getStatus($sessionId, true, 15);

        if (! $status['ok']) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_GATEWAY_UNREACHABLE,
                __('admin.whatsapp-log-gateway-unreachable'),
                ['error' => $status['error'] ?? null, 'http_status' => $status['status'] ?? 0],
                WhatsappSessionLog::LEVEL_ERROR,
                null,
                300
            );

            return response()->json([
                'ok' => false,
                'data' => null,
                'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
                'error' => $status['error'],
            ]);
        }

        $data = WhatsAppSystemSessionService::maybeReconnect(
            $sessionId,
            is_array($status['data'] ?? null) ? $status['data'] : []
        );

        $data = $this->normalizeRegisteredReconnectStatus($data);
        $data = $this->normalizeLiveConnectedFields($data);
        $record = WhatsAppSystemSessionService::syncFromGateway($data);

        return response()->json([
            'ok' => true,
            'data' => array_merge($data, [
                'still_registered_on_disk' => (bool) ($data['registeredOnDisk'] ?? false),
                'recovery_hint' => WhatsAppSystemSessionService::recoveryHint($data, $record),
            ]),
            'session_meta' => WhatsAppSystemSessionService::sessionMeta($record),
            'error' => null,
        ]);
    }

    public function logs(Request $request): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json(['ok' => false, 'error' => __('admin.whatsapp-gateway-not-configured')], 503);
        }

        $perPage = max(10, min(50, (int) $request->query('per_page', 15)));
        $page = max(1, (int) $request->query('page', 1));
        $paginator = WhatsAppSystemSessionLogService::paginate($perPage, $page);

        return response()->json([
            'ok' => true,
            'data' => collect($paginator->items())
                ->map(fn (WhatsappSessionLog $log) => $this->formatLogEntry($log))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function destroyLog(WhatsappSessionLog $log): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json(['ok' => false, 'error' => __('admin.whatsapp-gateway-not-configured')], 503);
        }

        if ($log->session_id !== BaileysGateway::systemSessionId()) {
            return response()->json(['ok' => false, 'error' => __('admin.whatsapp-activity-log-not-found')], 404);
        }

        $log->delete();

        return response()->json(['ok' => true]);
    }

    public function destroyLogs(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json(['ok' => false, 'error' => __('admin.whatsapp-gateway-not-configured')], 503);
        }

        $deleted = WhatsAppSystemSessionLogService::clearAll();

        return response()->json([
            'ok' => true,
            'deleted' => $deleted,
        ]);
    }

    public function reconnect(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-gateway-not-configured'),
            ], 503);
        }

        $sessionId = BaileysGateway::systemSessionId();

        if (WhatsAppSystemSessionService::adminRequestedDisconnect($sessionId)) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-reconnect-blocked-admin-lock'),
            ], 409);
        }

        $status = BaileysGateway::getStatus($sessionId, true, 15);
        $data = is_array($status['data'] ?? null) ? $status['data'] : [];
        $data = $this->normalizeRegisteredReconnectStatus($data);

        if (($data['status'] ?? '') === 'connected' && ($data['socketAlive'] ?? false)) {
            WhatsAppSystemSessionService::syncFromGateway($data);

            return response()->json([
                'ok' => true,
                'data' => $data,
                'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
                'error' => null,
            ]);
        }

        if (! ($data['registeredOnDisk'] ?? false)) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-reconnect-needs-qr'),
            ], 422);
        }

        WhatsAppSystemSessionLogService::record(
            WhatsappSessionLog::EVENT_ADMIN_PREPARE_RECONNECT,
            __('admin.whatsapp-log-prepare-reconnect'),
            WhatsAppSystemSessionLogService::gatewaySnapshot($data),
            WhatsappSessionLog::LEVEL_INFO
        );

        $result = BaileysGateway::startSession($sessionId, 45);

        if ($result['ok']) {
            $retry = BaileysGateway::getStatus($sessionId, true, 30);
            if ($retry['ok'] && is_array($retry['data'] ?? null)) {
                $retryData = $this->normalizeRegisteredReconnectStatus($retry['data']);
                WhatsAppSystemSessionService::syncFromGateway($retryData);
                $result['data'] = $retryData;
            }
        } else {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_QR_FAILED,
                __('admin.whatsapp-log-prepare-reconnect-failed'),
                ['error' => $result['error'] ?? null],
                WhatsappSessionLog::LEVEL_ERROR
            );
        }

        return response()->json([
            'ok' => $result['ok'],
            'data' => $result['data'] ?? $data,
            'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
            'error' => $result['error'] ?? null,
        ]);
    }

    public function prepare(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-gateway-not-configured'),
            ], 503);
        }

        $sessionId = BaileysGateway::systemSessionId();
        $wasLocked = WhatsAppSystemSessionService::adminRequestedDisconnect($sessionId);
        WhatsAppSystemSessionService::clearAdminDisconnect($sessionId);

        if ($wasLocked) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_ADMIN_LOCK_CLEARED,
                __('admin.whatsapp-log-admin-lock-cleared'),
                [],
                WhatsappSessionLog::LEVEL_INFO
            );
        }

        $status = BaileysGateway::getStatus($sessionId, true, 15);
        $data = is_array($status['data'] ?? null) ? $status['data'] : [];

        if (($data['status'] ?? '') === 'connected' && ($data['socketAlive'] ?? false)) {
            WhatsAppSystemSessionService::syncFromGateway($data);

            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_ADMIN_ALREADY_CONNECTED,
                __('admin.whatsapp-log-already-connected', ['phone' => $data['phone'] ?? '—']),
                WhatsAppSystemSessionLogService::gatewaySnapshot($data),
                WhatsappSessionLog::LEVEL_INFO
            );

            return response()->json([
                'ok' => true,
                'data' => $data,
                'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
                'error' => null,
            ]);
        }

        $registeredOnDisk = (bool) ($data['registeredOnDisk'] ?? false);

        if ($registeredOnDisk) {
            Log::info('WhatsApp system: reconnecting saved session (not wiping creds)', [
                'session_id' => $sessionId,
            ]);

            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_ADMIN_PREPARE_RECONNECT,
                __('admin.whatsapp-log-prepare-reconnect'),
                WhatsAppSystemSessionLogService::gatewaySnapshot($data),
                WhatsappSessionLog::LEVEL_INFO
            );

            $result = BaileysGateway::startSession($sessionId, 45);
            if ($result['ok']) {
                $retry = BaileysGateway::getStatus($sessionId, true, 20);
                if ($retry['ok'] && is_array($retry['data'] ?? null)) {
                    WhatsAppSystemSessionService::syncFromGateway($retry['data']);
                }
            } else {
                WhatsAppSystemSessionLogService::record(
                    WhatsappSessionLog::EVENT_QR_FAILED,
                    __('admin.whatsapp-log-prepare-reconnect-failed'),
                    ['error' => $result['error'] ?? null],
                    WhatsappSessionLog::LEVEL_ERROR
                );
            }

            return response()->json([
                'ok' => $result['ok'],
                'data' => $result['data'],
                'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
                'error' => $result['error'],
            ]);
        }

        WhatsAppSystemSessionLogService::record(
            WhatsappSessionLog::EVENT_ADMIN_PREPARE_QR,
            __('admin.whatsapp-log-prepare-qr'),
            WhatsAppSystemSessionLogService::gatewaySnapshot($data),
            WhatsappSessionLog::LEVEL_INFO
        );

        $result = BaileysGateway::startSession($sessionId, 45);

        if (! $result['ok']) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_QR_FAILED,
                __('admin.whatsapp-log-prepare-qr-failed'),
                ['error' => $result['error'] ?? null],
                WhatsappSessionLog::LEVEL_ERROR
            );
        }

        return response()->json([
            'ok' => $result['ok'],
            'data' => $result['data'],
            'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
            'error' => $result['error'],
        ]);
    }

    public function qr(Request $request): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-gateway-not-configured'),
            ], 503);
        }

        $waitMs = max(0, min(60_000, (int) $request->query('waitMs', 25_000)));
        $qr = BaileysGateway::getQr(null, $waitMs);

        if (! $qr['ok']) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_QR_FAILED,
                __('admin.whatsapp-log-qr-failed'),
                ['error' => $qr['error'] ?? null, 'wait_ms' => $waitMs],
                WhatsappSessionLog::LEVEL_ERROR,
                null,
                60
            );

            return response()->json([
                'ok' => false,
                'ready' => false,
                'data' => $qr['data'],
                'error' => $qr['error'] ?? __('admin.whatsapp-qr-failed'),
            ]);
        }

        $data = $qr['data'] ?? [];

        if (($data['status'] ?? '') === 'pending_qr' && ! empty($data['qrImage'])) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_QR_GENERATED,
                __('admin.whatsapp-log-qr-generated'),
                ['wait_ms' => $waitMs],
                WhatsappSessionLog::LEVEL_SUCCESS,
                null,
                90
            );
        }

        return response()->json([
            'ok' => true,
            'data' => $data,
            'ready' => ($data['ready'] ?? true) && ! empty($data['qrImage']),
            'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
            'error' => null,
        ]);
    }

    public function refreshQr(): RedirectResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return back()->with('error', __('admin.whatsapp-gateway-not-configured'));
        }

        BaileysGateway::startSession();

        WhatsAppSystemSessionLogService::record(
            WhatsappSessionLog::EVENT_ADMIN_PREPARE_QR,
            __('admin.whatsapp-log-refresh-qr'),
            [],
            WhatsappSessionLog::LEVEL_INFO
        );

        return back()
            ->with('wa_auto_generate', true)
            ->with('success', __('admin.whatsapp-qr-generating'));
    }

    public function disconnect(): RedirectResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return back()->with('error', __('admin.whatsapp-gateway-not-configured'));
        }

        $sessionId = BaileysGateway::systemSessionId();
        $record = WhatsAppSystemSessionService::record();

        WhatsAppSystemSessionService::markAdminDisconnect($sessionId);

        $result = BaileysGateway::deleteSession($sessionId, 35);

        if (! $result['ok']) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_ADMIN_DISCONNECT_FAILED,
                __('admin.whatsapp-log-disconnect-failed'),
                [
                    'error' => $result['error'] ?? null,
                    'previous_phone' => $record->phone,
                ],
                WhatsappSessionLog::LEVEL_ERROR
            );

            return back()->with('error', $result['error'] ?? __('admin.whatsapp-disconnect-failed'));
        }

        WhatsAppSystemSessionService::markDisconnected();

        WhatsAppSystemSessionLogService::record(
            WhatsappSessionLog::EVENT_ADMIN_DISCONNECT,
            __('admin.whatsapp-log-admin-disconnect'),
            ['previous_phone' => $record->phone],
            WhatsappSessionLog::LEVEL_WARNING
        );

        Log::info('WhatsApp system: admin disconnected session', ['session_id' => $sessionId]);

        return back()->with('success', __('admin.whatsapp-disconnected'));
    }

    public function testOtp(Request $request): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-gateway-not-configured'),
            ], 503);
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:32'],
            'country_code' => ['nullable', 'string', 'max:6'],
        ]);

        $countryCode = preg_replace('/\D+/', '', (string) ($validated['country_code'] ?? '966')) ?: '966';
        $to = PhoneNumber::e164ForWhatsAppPairing($countryCode, $validated['phone'], $validated['phone']);

        if ($to === '' || strlen($to) < 10) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-test-otp-invalid-phone'),
            ], 422);
        }

        $sessionId = BaileysGateway::systemSessionId();
        $masked = str_repeat('*', max(0, strlen($to) - 4)).substr($to, -4);
        $logContext = ['phone_masked' => $masked, 'test' => true];

        if (WhatsAppSystemSessionService::adminRequestedDisconnect($sessionId)) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_OTP_TEST_FAILED,
                __('admin.whatsapp-test-otp-failed-locked'),
                array_merge($logContext, ['failure' => 'admin_disconnect_locked']),
                WhatsappSessionLog::LEVEL_ERROR
            );

            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-test-otp-failed-locked'),
            ], 422);
        }

        $statusResult = BaileysGateway::getStatus($sessionId, true, 20);

        if ($statusResult['ok'] && is_array($statusResult['data'] ?? null)) {
            $statusResult['data'] = WhatsAppSystemSessionService::maybeReconnect(
                $sessionId,
                $statusResult['data']
            );
            WhatsAppSystemSessionService::syncFromGateway($statusResult['data']);
        }

        if (! $statusResult['ok']) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_OTP_TEST_FAILED,
                __('admin.whatsapp-test-otp-failed-gateway'),
                array_merge($logContext, ['failure' => 'gateway_unreachable', 'error' => $statusResult['error'] ?? null]),
                WhatsappSessionLog::LEVEL_ERROR,
                null,
                30
            );

            return response()->json([
                'ok' => false,
                'error' => $statusResult['error'] ?? __('admin.whatsapp-gateway-unreachable'),
            ], 503);
        }

        $data = $statusResult['data'];
        $connectionStatus = $data['status'] ?? 'disconnected';
        $socketAlive = (bool) ($data['socketAlive'] ?? false);

        if ($connectionStatus !== 'connected' || ! $socketAlive) {
            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_OTP_TEST_FAILED,
                __('admin.whatsapp-test-otp-failed-not-connected', ['status' => $connectionStatus]),
                array_merge($logContext, [
                    'failure' => 'not_connected',
                    'gateway_status' => $connectionStatus,
                ]),
                WhatsappSessionLog::LEVEL_WARNING,
                null,
                30
            );

            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-test-otp-failed-not-connected', ['status' => $connectionStatus]),
            ], 422);
        }

        $code = (string) random_int(1000, 9999);
        $response = BaileysWhatsApp::sendLegacy($to, $code);

        if (! isset($response->sent) || $response->sent !== 'true') {
            $errorMessage = is_object($response->error ?? null)
                ? ($response->error->message ?? 'unknown')
                : 'unknown';

            WhatsAppSystemSessionLogService::record(
                WhatsappSessionLog::EVENT_OTP_TEST_FAILED,
                __('admin.whatsapp-test-otp-failed-send', ['error' => $errorMessage]),
                array_merge($logContext, ['failure' => 'send_failed', 'gateway_error' => $errorMessage]),
                WhatsappSessionLog::LEVEL_ERROR
            );

            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-test-otp-failed-send', ['error' => $errorMessage]),
            ], 503);
        }

        WhatsAppSystemSessionLogService::record(
            WhatsappSessionLog::EVENT_OTP_TEST_SUCCESS,
            __('admin.whatsapp-test-otp-sent', ['phone' => $masked]),
            array_merge($logContext, ['message_id' => $response->id ?? null]),
            WhatsappSessionLog::LEVEL_SUCCESS
        );

        Log::info('WhatsApp system: test OTP sent', ['phone_masked' => $masked, 'admin_id' => auth('admin')->id()]);

        return response()->json([
            'ok' => true,
            'message' => __('admin.whatsapp-test-otp-sent', ['phone' => $masked]),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeRegisteredReconnectStatus(array $data): array
    {
        $registered = (bool) ($data['registeredOnDisk'] ?? false);
        $socketAlive = (bool) ($data['socketAlive'] ?? false);
        $status = (string) ($data['status'] ?? 'disconnected');

        if ($registered && ! $socketAlive && in_array($status, ['pending_qr', 'starting', 'disconnected'], true)) {
            $data['status'] = 'reconnecting';
            $data['reconnecting'] = true;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeLiveConnectedFields(array $data): array
    {
        $status = $data['status'] ?? 'disconnected';
        $socketAlive = (bool) ($data['socketAlive'] ?? false);

        if ($status !== 'connected' || ! $socketAlive) {
            return $data;
        }

        $data['registeredOnDisk'] = true;
        $data['linkedOnWhatsApp'] = true;

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatLogEntry(WhatsappSessionLog $log): array
    {
        $displayTz = (string) config('app.display_timezone', 'Asia/Riyadh');
        $displayTime = $log->created_at?->timezone($displayTz);

        return [
            'id' => $log->id,
            'event' => $log->event,
            'event_label' => __('admin.whatsapp-log-event-'.$log->event),
            'level' => $log->level,
            'level_badge' => $log->levelBadgeClass(),
            'message' => $log->message,
            'context' => $log->context ?? [],
            'admin_name' => $log->admin?->name,
            'created_at' => $displayTime?->toIso8601String(),
            'created_at_display' => $displayTime?->format('Y-m-d H:i:s'),
            'created_at_human' => $displayTime?->diffForHumans(),
        ];
    }
}
