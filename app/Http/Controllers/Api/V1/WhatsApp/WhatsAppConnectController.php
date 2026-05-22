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
            Log::error('WhatsApp connect: gateway outdated (need v1.2.4+)');

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
            ]);

            return RespondActive::clientError(__('messages.whatsapp_phone_required'));
        }

        $dbSession = WhatsappSession::query()->where('session_id', $sessionId)->first();
        $existingStatus = BaileysGateway::getStatus($sessionId);
        $existingConnection = $existingStatus['data']['status'] ?? 'disconnected';

        Log::info('WhatsApp connect: start', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'link_phone' => $phone,
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

        // Reuse in-progress pairing (do not wipe — invalidates code user is entering in WhatsApp)
        if ($this->shouldReusePendingPairing($dbSession, $existingConnection)) {
            $pairing = BaileysGateway::getPairingCode($sessionId, $phone);
            $pairingCode = $this->formatPairingCodeForDisplay($pairing['data']['pairingCode'] ?? null);

            if ($pairing['ok'] && $pairingCode) {
                Log::info('WhatsApp connect: reusing active pairing code', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                ]);

                return RespondActive::success(__('messages.whatsapp_pairing_code_ready'), [
                    'status' => 'pending_pairing',
                    'session_id' => $sessionId,
                    'pairing_code' => $pairingCode,
                    'link_phone' => $phone,
                    'phone' => $phone,
                    'poll_status' => true,
                    'poll_interval_seconds' => 3,
                    'instructions' => $this->pairingInstructions(),
                ]);
            }
        }

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
                'link_phone' => $phone,
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

        $pairingCode = $this->formatPairingCodeForDisplay($data['pairingCode'] ?? null);
        $pairingError = null;

        if (! $pairingCode) {
            $pairing = BaileysGateway::getPairingCode($sessionId, $phone);
            if ($pairing['ok']) {
                $pairingCode = $this->formatPairingCodeForDisplay($pairing['data']['pairingCode'] ?? null);
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
                'link_phone' => $phone,
                'gateway_status' => $status,
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
            'link_phone' => $phone,
            'pairing_code' => $pairingCode,
        ]);

        return RespondActive::success(__('messages.whatsapp_pairing_code_ready'), [
            'status' => 'pending_pairing',
            'session_id' => $sessionId,
            'pairing_code' => $pairingCode,
            'link_phone' => $phone,
            'phone' => $phone,
            'poll_status' => true,
            'poll_interval_seconds' => 3,
            'instructions' => $this->pairingInstructions(),
        ]);
    }

    public function status(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $user = auth()->user();
        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);

        $quick = BaileysGateway::getStatus($sessionId);
        $connectionStatus = $quick['data']['status'] ?? 'disconnected';

        if ($connectionStatus === 'pending_pairing' || $connectionStatus === 'starting') {
            Log::info('WhatsApp status: attempting finalize', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'status' => $connectionStatus,
            ]);

            $finalize = BaileysGateway::finalizePairing($sessionId);

            Log::info('WhatsApp status: finalize result', [
                'user_id' => $user->id,
                'ok' => $finalize['ok'],
                'connected' => $finalize['data']['connected'] ?? false,
                'status' => $finalize['data']['status'] ?? null,
                'registered_on_disk' => $finalize['data']['registeredOnDisk'] ?? null,
                'error' => $finalize['error'] ?? null,
            ]);
        }

        $result = BaileysGateway::getStatus($sessionId);

        if (! $result['ok']) {
            return RespondActive::clientError($result['error'] ?? __('messages.whatsapp_status_failed'));
        }

        $data = $result['data'] ?? [];
        $connectionStatus = $data['status'] ?? 'disconnected';
        $phone = $data['phone'] ?? null;
        $registeredOnDisk = (bool) ($data['registeredOnDisk'] ?? false);

        $this->syncSessionRecord($user->id, $sessionId, $connectionStatus, $phone);

        if ($connectionStatus === 'connected') {
            Log::info('WhatsApp status: connected', [
                'user_id' => $user->id,
                'phone_suffix' => $phone ? substr((string) $phone, -4) : null,
            ]);
        } elseif ($connectionStatus === 'pending_pairing') {
            Log::warning('WhatsApp status: still pending_pairing', [
                'user_id' => $user->id,
                'registered_on_disk' => $registeredOnDisk,
                'hint' => 'Enter XXXX-XXXX in WhatsApp on the SAME phone as link_phone; do not tap Connect again',
            ]);
        }

        return RespondActive::success('OK', [
            'status' => $connectionStatus,
            'phone' => $phone,
            'session_id' => $sessionId,
            'connected' => $connectionStatus === 'connected',
            'registered_on_disk' => $registeredOnDisk,
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

    protected function shouldReusePendingPairing(?WhatsappSession $dbSession, string $gatewayStatus): bool
    {
        if ($gatewayStatus !== 'pending_pairing') {
            return false;
        }

        return $dbSession
            && $dbSession->status === 'pending_pairing'
            && $dbSession->last_seen_at
            && $dbSession->last_seen_at->greaterThan(now()->subMinutes(5));
    }

    /**
     * WhatsApp expects XXXX-XXXX (8 chars with hyphen).
     */
    protected function formatPairingCodeForDisplay(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        $raw = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));

        if (strlen($raw) === 8) {
            return substr($raw, 0, 4).'-'.substr($raw, 4);
        }

        return $raw !== '' ? $raw : null;
    }

    /**
     * @return list<string>
     */
    protected function pairingInstructions(): array
    {
        return [
            __('messages.whatsapp_pairing_step_1'),
            __('messages.whatsapp_pairing_step_2'),
            __('messages.whatsapp_pairing_step_3'),
        ];
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
