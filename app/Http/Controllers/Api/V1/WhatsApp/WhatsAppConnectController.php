<?php

namespace App\Http\Controllers\Api\V1\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSession;
use App\Services\External\BaileysGateway;
use App\Services\RespondActive;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $this->clearUserDisconnectIntent($sessionId);

        if ($request->input('link_method') === 'qr') {
            return $this->connectViaQr($sessionId, $user);
        }

        $cooldownKey = 'whatsapp_pairing_cooldown:'.$user->id;
        if (Cache::has($cooldownKey) && ! $request->boolean('force')) {
            $wait = (int) Cache::get($cooldownKey, 45);

            return RespondActive::clientError(__('messages.whatsapp_pairing_cooldown', ['seconds' => $wait]));
        }

        Cache::put($cooldownKey, 45, now()->addSeconds(45));

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

        // Saved creds exist but socket is down — reconnect instead of issuing a new pairing code
        if ($registeredOnDisk && $existingConnection === 'disconnected') {
            Log::info('WhatsApp connect: reconnecting saved session (registered on disk)', [
                'user_id' => $user->id,
            ]);
            BaileysGateway::startSession($sessionId);
            $reconnectStatus = BaileysGateway::getStatus($sessionId);
            if (($reconnectStatus['data']['status'] ?? '') === 'connected') {
                $connectedPhone = $reconnectStatus['data']['phone'] ?? $phone;
                $this->syncSessionRecord($user->id, $sessionId, 'connected', $connectedPhone);

                return RespondActive::success(__('messages.whatsapp_connected'), [
                    'status' => 'connected',
                    'phone' => $connectedPhone,
                    'session_id' => $sessionId,
                ]);
            }
            Log::info('WhatsApp connect: reconnect failed — wiping for fresh pairing', [
                'user_id' => $user->id,
            ]);
        }

        // Wipe before pairing so disk pairingCode always matches the code shown in the app
        BaileysGateway::deleteSession($sessionId);
        Log::info('WhatsApp connect: fresh pairing code requested', [
            'user_id' => $user->id,
            'previous_status' => $existingConnection,
            'had_registered_creds' => $registeredOnDisk,
        ]);

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
            $phoneDisplay,
            (string) $user->country_code
        ));
    }

    public function connectViaQr(string $sessionId, $user): JsonResponse
    {
        BaileysGateway::deleteSession($sessionId);

        $result = BaileysGateway::getQr($sessionId);

        if (! $result['ok']) {
            Log::warning('WhatsApp QR connect failed', [
                'user_id' => $user->id,
                'error' => $result['error'] ?? null,
            ]);

            return RespondActive::clientError(
                $this->formatGatewayError($result['error'] ?? null, __('messages.whatsapp_connect_failed'))
            );
        }

        $data = $result['data'] ?? [];
        $status = $data['status'] ?? 'pending_qr';

        if ($status === 'connected') {
            $this->syncSessionRecord($user->id, $sessionId, 'connected', $data['phone'] ?? null);

            return RespondActive::success(__('messages.whatsapp_connected'), [
                'status' => 'connected',
                'phone' => $data['phone'] ?? null,
                'session_id' => $sessionId,
                'link_method' => 'qr',
            ]);
        }

        $this->syncSessionRecord($user->id, $sessionId, 'pending_qr', null);

        Log::info('WhatsApp QR ready', ['user_id' => $user->id, 'session_id' => $sessionId]);

        return RespondActive::success(__('messages.whatsapp_qr_ready'), [
            'status' => 'pending_qr',
            'session_id' => $sessionId,
            'link_method' => 'qr',
            'qr_image' => $data['qrImage'] ?? null,
            'poll_status' => true,
            'poll_interval_seconds' => 3,
            'instructions' => [
                __('messages.whatsapp_qr_step_1'),
                __('messages.whatsapp_qr_step_2'),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function pairingPayload(
        string $sessionId,
        string $pairingCode,
        string $linkPhoneE164,
        string $linkPhoneDisplay,
        string $countryCode = ''
    ): array {
        $entryOptions = PhoneNumber::whatsAppPhoneEntryOptions($linkPhoneE164, $countryCode);
        $primary = $entryOptions[0] ?? $linkPhoneE164;
        $alternate = $entryOptions[1] ?? PhoneNumber::localWithLeadingZero($linkPhoneE164, $countryCode);

        return [
            'status' => 'pending_pairing',
            'session_id' => $sessionId,
            'pairing_code' => $pairingCode,
            'link_phone' => $linkPhoneE164,
            'link_phone_display' => $linkPhoneDisplay,
            'phone' => $linkPhoneE164,
            'whatsapp_phone_primary' => $primary,
            'whatsapp_phone_alternate' => $alternate,
            'whatsapp_phone_hint' => __('messages.whatsapp_pairing_phone_try', [
                'primary' => $primary,
                'alternate' => $alternate,
            ]),
            'link_method' => 'pairing',
            'fallback_link_method' => 'qr',
            'poll_status' => true,
            'poll_interval_seconds' => 3,
            'code_valid_seconds' => 120,
            'expires_at' => now()->addSeconds(120)->toIso8601String(),
            'do_not_connect_again' => true,
            'open_whatsapp_immediately' => true,
            'instructions' => $this->pairingInstructions($linkPhoneDisplay, $primary, $alternate),
            'warning' => __('messages.whatsapp_pairing_enter_fast'),
        ];
    }

    public function status(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $user = auth()->user();
        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);
        $dbSession = WhatsappSession::query()->where('session_id', $sessionId)->first();

        if ($this->shouldForceDisconnectedStatus($sessionId, $dbSession)) {
            return $this->disconnectedStatusResponse($user, $sessionId, $dbSession);
        }

        // Gateway keeps socket open while user taps "Link device" on WhatsApp — do not call finalize here
        $result = BaileysGateway::getStatus($sessionId);

        if (! $result['ok']) {
            if ($this->shouldTrustStoredConnection($dbSession)) {
                return RespondActive::success('OK', $this->buildStatusPayload(
                    $user,
                    $sessionId,
                    'connected',
                    $dbSession->phone,
                    true,
                    true,
                    false,
                    'registered',
                    false,
                    null,
                    $dbSession
                ));
            }

            return RespondActive::clientError($result['error'] ?? __('messages.whatsapp_status_failed'));
        }

        $data = $this->refreshStatusAfterReconnect($sessionId, $result['data'] ?? [], $dbSession);
        $data = $this->refreshStatusAfterPairingAccepted($sessionId, $data, $dbSession);
        $data = $this->normalizeConnectedStatusFields($data);

        $connectionStatus = $data['status'] ?? 'disconnected';
        $phone = $data['phone'] ?? null;
        $registeredOnDisk = (bool) ($data['registeredOnDisk'] ?? false);
        $pairingAccepted = (bool) ($data['pairingAccepted'] ?? false);
        $pairingProgress = (string) ($data['pairingProgress'] ?? 'awaiting_code');
        $socketAlive = (bool) ($data['socketAlive'] ?? false);
        $codeAge = $data['pairingCodeAgeSeconds'] ?? null;
        $linkedOnWhatsApp = (bool) ($data['linkedOnWhatsApp'] ?? false);

        if ($this->shouldForceDisconnectedStatus($sessionId, $dbSession)) {
            return $this->disconnectedStatusResponse($user, $sessionId, $dbSession);
        }

        [$connectionStatus, $appConnected] = $this->resolveAppConnectionState(
            $connectionStatus,
            $registeredOnDisk,
            $pairingAccepted,
            $socketAlive,
            $linkedOnWhatsApp
        );

        if (! $linkedOnWhatsApp) {
            $linkedOnWhatsApp = $appConnected;
        }

        $this->syncSessionRecord($user->id, $sessionId, $connectionStatus, $appConnected ? $phone : null);

        if ($connectionStatus === 'connected') {
            Log::info('WhatsApp status: connected', [
                'user_id' => $user->id,
                'phone_suffix' => $phone ? substr((string) $phone, -4) : null,
                'socket_alive' => $socketAlive,
                'registered_on_disk' => $registeredOnDisk,
            ]);
        } elseif ($connectionStatus === 'pending_pairing') {
            Log::info('WhatsApp status: pending_pairing', [
                'user_id' => $user->id,
                'registered_on_disk' => $registeredOnDisk,
                'pairing_accepted' => $pairingAccepted,
                'pairing_progress' => $pairingProgress,
                'wa_id' => $data['waId'] ?? null,
                'action' => $this->statusAction($connectionStatus, $registeredOnDisk, $pairingAccepted),
            ]);
        }

        return RespondActive::success('OK', $this->buildStatusPayload(
            $user,
            $sessionId,
            $connectionStatus,
            $phone,
            $appConnected,
            $registeredOnDisk,
            $pairingAccepted,
            $pairingProgress,
            $socketAlive,
            $codeAge,
            $dbSession,
            $linkedOnWhatsApp
        ));
    }

    public function disconnect(): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $user = auth()->user();
        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);

        $this->markUserDisconnectIntent($sessionId);
        $this->clearStatusReconnectCaches($sessionId);

        $deleteResult = BaileysGateway::deleteSession($sessionId, 35);
        if (! $deleteResult['ok']) {
            Log::warning('WhatsApp disconnect: gateway delete failed', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'error' => $deleteResult['error'] ?? null,
            ]);
        }

        $this->enforceGatewayDisconnect($sessionId);

        WhatsappSession::query()->where('user_id', $user->id)->update([
            'status' => 'disconnected',
            'phone' => null,
            'connected_at' => null,
            'disconnected_at' => now(),
            'last_seen_at' => now(),
        ]);

        Log::info('WhatsApp disconnect', ['user_id' => $user->id, 'session_id' => $sessionId]);

        return RespondActive::success(__('messages.whatsapp_disconnected'));
    }

    protected function statusAction(string $connectionStatus, bool $registeredOnDisk, bool $pairingAccepted = false): string
    {
        if ($connectionStatus === 'connected') {
            return 'connected';
        }

        if ($connectionStatus === 'pending_pairing' && $pairingAccepted) {
            return 'tap_link_device_in_whatsapp';
        }

        if ($connectionStatus === 'pending_pairing' && $registeredOnDisk) {
            return 'wait_for_connection';
        }

        if ($connectionStatus === 'pending_pairing') {
            return 'enter_code_in_whatsapp';
        }

        return 'connect_whatsapp';
    }

    protected function statusMessage(
        string $connectionStatus,
        bool $registeredOnDisk,
        bool $pairingAccepted = false,
        bool $socketAlive = true
    ): string {
        if ($connectionStatus === 'connected') {
            return __('messages.whatsapp_connected');
        }

        if ($connectionStatus === 'pending_pairing' && $pairingAccepted) {
            return __('messages.whatsapp_pairing_tap_link_device');
        }

        if ($connectionStatus === 'pending_pairing' && $registeredOnDisk) {
            return __('messages.whatsapp_pairing_finishing');
        }

        if ($connectionStatus === 'pending_pairing') {
            if (! $socketAlive) {
                return __('messages.whatsapp_pairing_socket_down');
            }

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
    protected function pairingInstructions(string $linkPhoneDisplay, string $primaryDigits, string $alternateDigits): array
    {
        return [
            __('messages.whatsapp_pairing_step_1'),
            __('messages.whatsapp_pairing_step_2_phone', ['primary' => $primaryDigits, 'alternate' => $alternateDigits]),
            __('messages.whatsapp_pairing_step_3'),
            __('messages.whatsapp_pairing_step_4_scam'),
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function refreshStatusAfterReconnect(string $sessionId, array $data, ?WhatsappSession $dbSession = null): array
    {
        if ($this->shouldForceDisconnectedStatus($sessionId, $dbSession)) {
            return $data;
        }

        $connectionStatus = $data['status'] ?? 'disconnected';
        $registeredOnDisk = (bool) ($data['registeredOnDisk'] ?? false);
        $pairingAccepted = (bool) ($data['pairingAccepted'] ?? false);
        $socketAlive = (bool) ($data['socketAlive'] ?? false);

        if (
            $connectionStatus === 'connected'
            || $socketAlive
            || $this->isEnteringPairingCode($connectionStatus, $registeredOnDisk, $pairingAccepted)
            || ! $registeredOnDisk
        ) {
            return $data;
        }

        $reconnectKey = 'whatsapp_status_reconnect:'.$sessionId;
        if (Cache::has($reconnectKey)) {
            return $data;
        }

        Cache::put($reconnectKey, 1, now()->addSeconds(8));

        Log::info('WhatsApp status: reconnecting registered session', ['session_id' => $sessionId]);
        BaileysGateway::startSession($sessionId);
        $retry = BaileysGateway::getStatus($sessionId, true);

        return $retry['ok'] ? ($retry['data'] ?? $data) : $data;
    }

    /**
     * Code accepted on phone (me.id set) but registered:false — complete link via gateway finalize.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function refreshStatusAfterPairingAccepted(string $sessionId, array $data, ?WhatsappSession $dbSession = null): array
    {
        if ($this->shouldForceDisconnectedStatus($sessionId, $dbSession)) {
            return $data;
        }

        $connectionStatus = $data['status'] ?? 'disconnected';
        $registeredOnDisk = (bool) ($data['registeredOnDisk'] ?? false);
        $pairingAccepted = (bool) ($data['pairingAccepted'] ?? false);
        $socketAlive = (bool) ($data['socketAlive'] ?? false);

        if (
            $connectionStatus === 'connected'
            || $registeredOnDisk
            || ! $pairingAccepted
            || $socketAlive
        ) {
            return $data;
        }

        $finalizeKey = 'whatsapp_finalize_pairing:'.$sessionId;
        if (Cache::has($finalizeKey)) {
            return $data;
        }

        Cache::put($finalizeKey, 1, now()->addSeconds(12));

        Log::info('WhatsApp status: finalizing link after pairing code accepted', [
            'session_id' => $sessionId,
            'wa_id' => $data['waId'] ?? null,
        ]);

        BaileysGateway::finalizePairing($sessionId, true);
        $retry = BaileysGateway::getStatus($sessionId, true);

        return $retry['ok'] ? ($retry['data'] ?? $data) : $data;
    }

    /**
     * Align pairing metadata when the live session is connected (quick status used to skip disk read).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeConnectedStatusFields(array $data): array
    {
        $connectionStatus = $data['status'] ?? 'disconnected';
        $socketAlive = (bool) ($data['socketAlive'] ?? false);

        if ($connectionStatus !== 'connected' || ! $socketAlive) {
            return $data;
        }

        $data['registeredOnDisk'] = true;
        $data['pairingAccepted'] = false;
        $data['pairingProgress'] = 'registered';
        $data['pairingCodeAgeSeconds'] = null;

        return $data;
    }

    protected function isEnteringPairingCode(
        string $connectionStatus,
        bool $registeredOnDisk,
        bool $pairingAccepted
    ): bool {
        return $connectionStatus === 'pending_pairing' && ! $registeredOnDisk && ! $pairingAccepted;
    }

    /**
     * True only when the session is a live linked companion device on WhatsApp (not mid-pairing).
     */
    protected function isLinkedOnWhatsApp(
        string $connectionStatus,
        bool $registeredOnDisk,
        bool $pairingAccepted,
        bool $socketAlive = false
    ): bool {
        return $connectionStatus === 'connected'
            && $socketAlive
            && $registeredOnDisk
            && ! $pairingAccepted;
    }

    /**
     * @return array{0: string, 1: bool}
     */
    protected function resolveAppConnectionState(
        string $connectionStatus,
        bool $registeredOnDisk,
        bool $pairingAccepted,
        bool $socketAlive,
        bool $linkedOnWhatsApp = false
    ): array {
        if ($linkedOnWhatsApp) {
            return ['connected', true];
        }

        if ($connectionStatus === 'connected' && $socketAlive && $registeredOnDisk) {
            return ['connected', true];
        }

        if (
            $registeredOnDisk
            && ! $socketAlive
            && ! $this->isEnteringPairingCode($connectionStatus, $registeredOnDisk, $pairingAccepted)
        ) {
            return ['disconnected', false];
        }

        if ($connectionStatus === 'pending_pairing') {
            return ['pending_pairing', false];
        }

        $appConnected = $connectionStatus === 'connected' && $socketAlive && $registeredOnDisk;

        if ($appConnected) {
            return ['connected', true];
        }

        if (in_array($connectionStatus, ['connected', 'pending_pairing', 'pending_qr', 'starting'], true)) {
            return ['disconnected', false];
        }

        return [$connectionStatus, false];
    }

    protected function shouldForceDisconnectedStatus(string $sessionId, ?WhatsappSession $dbSession): bool
    {
        if ($this->userRequestedDisconnect($sessionId)) {
            return true;
        }

        return $dbSession?->disconnected_at !== null;
    }

    protected function disconnectedStatusResponse($user, string $sessionId, ?WhatsappSession $dbSession): JsonResponse
    {
        $this->enforceGatewayDisconnect($sessionId);
        $this->syncSessionRecord($user->id, $sessionId, 'disconnected', null);

        return RespondActive::success('OK', $this->buildStatusPayload(
            $user,
            $sessionId,
            'disconnected',
            null,
            false,
            false,
            false,
            'awaiting_code',
            false,
            null,
            $dbSession,
            false
        ));
    }

    protected function userDisconnectCacheKey(string $sessionId): string
    {
        return 'whatsapp_session_disconnected:'.$sessionId;
    }

    protected function userRequestedDisconnect(string $sessionId): bool
    {
        return Cache::has($this->userDisconnectCacheKey($sessionId));
    }

    protected function markUserDisconnectIntent(string $sessionId): void
    {
        Cache::put($this->userDisconnectCacheKey($sessionId), 1, now()->addDays(30));
    }

    protected function clearUserDisconnectIntent(string $sessionId): void
    {
        Cache::forget($this->userDisconnectCacheKey($sessionId));

        WhatsappSession::query()
            ->where('session_id', $sessionId)
            ->update(['disconnected_at' => null]);
    }

    protected function clearStatusReconnectCaches(string $sessionId): void
    {
        Cache::forget('whatsapp_status_reconnect:'.$sessionId);
        Cache::forget('whatsapp_finalize_pairing:'.$sessionId);
    }

    protected function enforceGatewayDisconnect(string $sessionId): void
    {
        $this->clearStatusReconnectCaches($sessionId);
        BaileysGateway::deleteSession($sessionId);
    }

    protected function shouldTrustStoredConnection(?WhatsappSession $dbSession): bool
    {
        if (! $dbSession || $dbSession->status !== 'connected') {
            return false;
        }

        $lastSeen = $dbSession->connected_at ?? $dbSession->last_seen_at;

        return $lastSeen !== null && $lastSeen->greaterThan(now()->subHours(24));
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildStatusPayload(
        $user,
        string $sessionId,
        string $connectionStatus,
        ?string $phone,
        bool $appConnected,
        bool $registeredOnDisk,
        bool $pairingAccepted,
        string $pairingProgress,
        bool $socketAlive,
        ?int $codeAge,
        ?WhatsappSession $dbSession,
        ?bool $linkedOnWhatsApp = null
    ): array {
        $linkedOnWhatsApp ??= $this->isLinkedOnWhatsApp(
            $connectionStatus,
            $registeredOnDisk,
            $pairingAccepted,
            $socketAlive
        );
        $linkDisplay = $dbSession?->phone
            ? PhoneNumber::formatForWhatsAppDisplay(PhoneNumber::e164ForWhatsAppPairing($user->country_code, $dbSession->phone))
            : PhoneNumber::formatForWhatsAppDisplay(PhoneNumber::e164ForWhatsAppPairing($user->country_code, $user->phone));

        return [
            'status' => $connectionStatus,
            'phone' => $phone,
            'session_id' => $sessionId,
            'connected' => $appConnected,
            'linked' => $linkedOnWhatsApp,
            'linked_on_whatsapp' => $linkedOnWhatsApp,
            'socket_alive' => $socketAlive,
            'registered_on_disk' => $registeredOnDisk,
            'pairing_accepted' => $pairingAccepted,
            'pairing_progress' => $pairingProgress,
            'pairing_code_age_seconds' => $codeAge,
            'awaiting_user' => $this->isEnteringPairingCode($connectionStatus, $registeredOnDisk, $pairingAccepted),
            'action' => $this->statusAction($connectionStatus, $registeredOnDisk, $pairingAccepted),
            'link_phone_display' => $linkDisplay,
            'message' => $this->statusMessage($connectionStatus, $registeredOnDisk, $pairingAccepted, $socketAlive),
        ];
    }
}
