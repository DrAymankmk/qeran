<?php

namespace App\Http\Controllers\Api\V1\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSession;
use App\Services\External\BaileysGateway;
use App\Services\RespondActive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppConnectController extends Controller
{
    public function connect(Request $request): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        if (! BaileysGateway::gatewaySupportsPairing()) {
            Log::error('WhatsApp connect: gateway missing pairing-code support (deploy v1.2.2+)');

            return RespondActive::clientError(__('messages.whatsapp_gateway_outdated'));
        }

        $user = auth()->user();
        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);

        $phone = BaileysGateway::normalizeUserPhone(
            $user->country_code,
            $user->phone,
            $request->input('phone')
        );

        if ($phone === '' || strlen($phone) < 10) {
            Log::warning('WhatsApp connect: invalid phone', [
                'user_id' => $user->id,
                'country_code' => $user->country_code,
                'profile_phone_suffix' => substr((string) $user->phone, -4),
            ]);

            return RespondActive::clientError(__('messages.whatsapp_phone_required'));
        }

        $existingStatus = BaileysGateway::getStatus($sessionId);
        $existingConnection = $existingStatus['data']['status'] ?? 'disconnected';

        Log::info('WhatsApp connect: start', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'phone_suffix' => substr($phone, -4),
            'gateway_status' => $existingConnection,
        ]);

        if ($existingConnection === 'connected') {
            $connectedPhone = $existingStatus['data']['phone'] ?? $phone;
            $this->syncSessionRecord($user->id, $sessionId, 'connected', $connectedPhone);

            return RespondActive::success(__('messages.whatsapp_already_connected'), [
                'status' => 'connected',
                'phone' => $connectedPhone,
                'session_id' => $sessionId,
            ]);
        }

        // Clear stale QR/pairing attempts so the user gets a fresh code
        if (in_array($existingConnection, ['pending_pairing', 'pending_qr', 'starting', 'disconnected'], true)) {
            BaileysGateway::deleteSession($sessionId);
            Log::info('WhatsApp connect: cleared stale gateway session', [
                'user_id' => $user->id,
                'previous_status' => $existingConnection,
            ]);
        }

        $result = BaileysGateway::startSessionWithPairing($sessionId, $phone);

        if (! $result['ok']) {
            Log::warning('WhatsApp connect: gateway start failed', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'phone_suffix' => substr($phone, -4),
                'http_status' => $result['status'] ?? 0,
                'error' => $result['error'] ?? null,
            ]);

            return RespondActive::clientError(
                $this->formatGatewayError($result['error'] ?? null, __('messages.whatsapp_connect_failed'))
            );
        }

        $data = $result['data'] ?? [];
        $status = $data['status'] ?? 'starting';

        $this->syncSessionRecord($user->id, $sessionId, $status, $data['phone'] ?? $phone);

        if ($status === 'connected') {
            return RespondActive::success(__('messages.whatsapp_connected'), [
                'status' => 'connected',
                'phone' => $data['phone'] ?? $phone,
                'session_id' => $sessionId,
            ]);
        }

        $pairingCode = $this->normalizePairingCode($data['pairingCode'] ?? null);
        $pairingError = null;

        if (! $pairingCode) {
            Log::info('WhatsApp connect: fetching pairing code from gateway', [
                'user_id' => $user->id,
                'gateway_status' => $status,
            ]);

            $pairing = BaileysGateway::getPairingCode($sessionId, $phone);
            if ($pairing['ok']) {
                $pairingCode = $this->normalizePairingCode($pairing['data']['pairingCode'] ?? null);
                $status = $pairing['data']['status'] ?? $status;
            } else {
                $pairingError = $pairing['error'] ?? null;
            }
        }

        if ($status === 'connected') {
            $this->syncSessionRecord($user->id, $sessionId, 'connected', $data['phone'] ?? $phone);

            return RespondActive::success(__('messages.whatsapp_connected'), [
                'status' => 'connected',
                'phone' => $data['phone'] ?? $phone,
                'session_id' => $sessionId,
            ]);
        }

        if (! $pairingCode) {
            Log::warning('WhatsApp connect: pairing code missing', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'phone_suffix' => substr($phone, -4),
                'gateway_status' => $status,
                'start_response' => $data,
                'pairing_error' => $pairingError,
            ]);

            return RespondActive::clientError(
                $this->formatGatewayError(
                    $pairingError ?? ($data['error'] ?? null),
                    __('messages.whatsapp_pairing_code_failed')
                )
            );
        }

        Log::info('WhatsApp connect: pairing code issued', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'phone_suffix' => substr($phone, -4),
            'code_length' => strlen($pairingCode),
        ]);

        return RespondActive::success(__('messages.whatsapp_pairing_code_ready'), [
            'status' => 'pending_pairing',
            'session_id' => $sessionId,
            'pairing_code' => $pairingCode,
            'phone' => $phone,
            'poll_status' => true,
            'poll_interval_seconds' => 3,
            'instructions' => [
                __('messages.whatsapp_pairing_step_1'),
                __('messages.whatsapp_pairing_step_2'),
                __('messages.whatsapp_pairing_step_3'),
            ],
        ]);
    }

    public function status(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $user = auth()->user();
        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);
        $result = BaileysGateway::getStatus($sessionId);

        if (! $result['ok']) {
            Log::warning('WhatsApp status: gateway error', [
                'user_id' => $user->id,
                'error' => $result['error'] ?? null,
            ]);

            return RespondActive::clientError($result['error'] ?? __('messages.whatsapp_status_failed'));
        }

        $data = $result['data'] ?? [];
        $connectionStatus = $data['status'] ?? 'disconnected';
        $phone = $data['phone'] ?? null;

        $this->syncSessionRecord($user->id, $sessionId, $connectionStatus, $phone);

        if ($connectionStatus === 'pending_pairing') {
            Log::info('WhatsApp status: still pending_pairing (gateway will try to finalize)', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'hint' => 'User should enter 8-char code in WhatsApp → Link with phone number; keep polling',
            ]);
        } elseif ($connectionStatus !== 'connected') {
            Log::debug('WhatsApp status: not connected yet', [
                'user_id' => $user->id,
                'status' => $connectionStatus,
            ]);
        } else {
            Log::info('WhatsApp status: connected', [
                'user_id' => $user->id,
                'phone_suffix' => $phone ? substr((string) $phone, -4) : null,
            ]);
        }

        return RespondActive::success('OK', [
            'status' => $connectionStatus,
            'phone' => $phone,
            'session_id' => $sessionId,
            'connected' => $connectionStatus === 'connected',
        ]);
    }

    public function disconnect(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $user = auth()->user();
        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);

        BaileysGateway::deleteSession($sessionId);

        WhatsappSession::query()->where('user_id', $user->id)->update([
            'status' => 'disconnected',
            'phone' => null,
            'connected_at' => null,
            'last_seen_at' => now(),
        ]);

        Log::info('WhatsApp disconnect', ['user_id' => $user->id, 'session_id' => $sessionId]);

        return RespondActive::success(__('messages.whatsapp_disconnected'));
    }

    protected function normalizePairingCode(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        $normalized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));

        return $normalized !== '' ? $normalized : null;
    }

    protected function formatGatewayError(?string $gatewayError, string $fallback): string
    {
        if (! $gatewayError) {
            return $fallback;
        }

        if (config('app.debug')) {
            return $fallback.' ('.$gatewayError.')';
        }

        return $fallback;
    }

    protected function syncSessionRecord(int $userId, string $sessionId, string $status, ?string $phone): void
    {
        WhatsappSession::query()->updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $userId,
                'status' => $status,
                'phone' => $phone,
                'connected_at' => $status === 'connected' ? now() : null,
                'last_seen_at' => now(),
            ]
        );
    }
}
