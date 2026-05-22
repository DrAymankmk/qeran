<?php

namespace App\Http\Controllers\Api\V1\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSession;
use App\Services\External\BaileysGateway;
use App\Services\RespondActive;
use App\Support\PhoneNumber;
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

        $phone = PhoneNumber::e164ForWhatsAppPairing(
            $user->country_code,
            $user->phone,
            $request->input('phone')
        );
        $phoneDisplay = PhoneNumber::formatForWhatsAppDisplay($phone);

        if ($phone === '' || strlen($phone) < 10) {
            Log::warning('WhatsApp connect: invalid phone', [
                'user_id' => $user->id,
                'country_code' => $user->country_code,
                'profile_phone' => $user->phone,
            ]);

            return RespondActive::clientError(__('messages.whatsapp_phone_required'));
        }

        if (! PhoneNumber::isValidWhatsAppPairingNumber($phone, $user->country_code)) {
            Log::warning('WhatsApp connect: pairing phone format invalid', [
                'user_id' => $user->id,
                'link_phone' => $phone,
                'profile_phone' => $user->phone,
                'country_code' => $user->country_code,
            ]);

            return RespondActive::clientError(__('messages.whatsapp_pairing_phone_invalid'));
        }

        $existingStatus = BaileysGateway::getStatus($sessionId);
        $existingConnection = $existingStatus['data']['status'] ?? 'disconnected';
        $registeredOnDisk = (bool) ($existingStatus['data']['registeredOnDisk'] ?? false);

        Log::info('WhatsApp connect: start', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'link_phone' => $phone,
            'link_phone_display' => $phoneDisplay,
            'profile_phone' => $user->phone,
            'gateway_status' => $existingConnection,
            'registered_on_disk' => $registeredOnDisk,
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

        // User tapped Connect → always issue a fresh code (reusing failed codes causes WhatsApp errors)
        if (in_array($existingConnection, ['pending_pairing', 'pending_qr', 'starting', 'disconnected'], true)) {
            BaileysGateway::deleteSession($sessionId);
            Log::info('WhatsApp connect: fresh pairing code requested', [
                'user_id' => $user->id,
                'previous_status' => $existingConnection,
                'had_registered_creds' => $registeredOnDisk,
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

        return RespondActive::success(__('messages.whatsapp_pairing_code_ready'), $this->pairingPayload(
            $sessionId,
            $pairingCode,
            $phone,
            $phoneDisplay
        ));
    }

    /**
     * @return array<string, mixed>
     */
    protected function pairingPayload(
        string $sessionId,
        string $pairingCode,
        string $linkPhoneE164,
        string $linkPhoneDisplay
    ): array {
        return [
            'status' => 'pending_pairing',
            'session_id' => $sessionId,
            'pairing_code' => $pairingCode,
            'link_phone' => $linkPhoneE164,
            'link_phone_display' => $linkPhoneDisplay,
            'phone' => $linkPhoneE164,
            'poll_status' => true,
            'poll_interval_seconds' => 3,
            'code_valid_seconds' => 120,
            'do_not_connect_again' => true,
            'instructions' => $this->pairingInstructions($linkPhoneDisplay),
        ];
    }

    public function status(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $user = auth()->user();
        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);

        // Single gateway call: status endpoint tries finalize + up to ~10s wait (avoid 60s double-call)
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
                'action' => $registeredOnDisk ? 'wait_for_connection' : 'enter_code_in_whatsapp',
            ]);
        }

        $dbSession = WhatsappSession::query()->where('session_id', $sessionId)->first();
        $linkDisplay = $dbSession?->phone
            ? PhoneNumber::formatForWhatsAppDisplay(PhoneNumber::e164ForWhatsAppPairing($user->country_code, $dbSession->phone))
            : PhoneNumber::formatForWhatsAppDisplay(PhoneNumber::e164ForWhatsAppPairing($user->country_code, $user->phone));

        return RespondActive::success('OK', [
            'status' => $connectionStatus,
            'phone' => $phone,
            'session_id' => $sessionId,
            'connected' => $connectionStatus === 'connected',
            'registered_on_disk' => $registeredOnDisk,
            'awaiting_user' => $connectionStatus === 'pending_pairing' && ! $registeredOnDisk,
            'action' => $this->statusAction($connectionStatus, $registeredOnDisk),
            'link_phone_display' => $linkDisplay,
            'message' => $this->statusMessage($connectionStatus, $registeredOnDisk),
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

    protected function statusAction(string $connectionStatus, bool $registeredOnDisk): string
    {
        if ($connectionStatus === 'connected') {
            return 'connected';
        }

        if ($connectionStatus === 'pending_pairing' && $registeredOnDisk) {
            return 'wait_for_connection';
        }

        if ($connectionStatus === 'pending_pairing') {
            return 'enter_code_in_whatsapp';
        }

        return 'connect_whatsapp';
    }

    protected function statusMessage(string $connectionStatus, bool $registeredOnDisk): string
    {
        if ($connectionStatus === 'connected') {
            return __('messages.whatsapp_connected');
        }

        if ($connectionStatus === 'pending_pairing' && $registeredOnDisk) {
            return __('messages.whatsapp_pairing_finishing');
        }

        if ($connectionStatus === 'pending_pairing') {
            return __('messages.whatsapp_pairing_waiting_user');
        }

        return __('messages.whatsapp_not_connected');
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
    protected function pairingInstructions(string $linkPhoneDisplay): array
    {
        return [
            __('messages.whatsapp_pairing_step_1'),
            __('messages.whatsapp_pairing_step_2', ['phone' => $linkPhoneDisplay]),
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
