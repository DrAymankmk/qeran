<?php

namespace App\Http\Controllers\Api\V1\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSession;
use App\Services\External\BaileysGateway;
use App\Services\RespondActive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppConnectController extends Controller
{
    public function connect(Request $request): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $user = auth()->user();
        $phone = preg_replace('/\D+/', '', (string) (
            $request->input('phone')
            ?? ($user->country_code.$user->phone)
        ));

        if ($phone === '') {
            return RespondActive::clientError(__('messages.whatsapp_phone_required'));
        }

        $sessionId = BaileysGateway::sessionIdForUser((int) $user->id);

        $result = BaileysGateway::startSessionWithPairing($sessionId, $phone);

        if (! $result['ok']) {
            return RespondActive::clientError($result['error'] ?? __('messages.whatsapp_connect_failed'));
        }

        $data = $result['data'] ?? [];
        $status = $data['status'] ?? 'starting';

        $this->syncSessionRecord($user->id, $sessionId, $status, $data['phone'] ?? $phone);

        if ($status === 'connected') {
            return RespondActive::success(__('messages.whatsapp_already_connected'), [
                'status' => 'connected',
                'phone' => $data['phone'] ?? $phone,
                'session_id' => $sessionId,
            ]);
        }

        $pairingCode = $data['pairingCode'] ?? null;
        if (! $pairingCode) {
            $pairing = BaileysGateway::getPairingCode($sessionId, $phone);
            if ($pairing['ok']) {
                $pairingCode = $pairing['data']['pairingCode'] ?? null;
                $status = $pairing['data']['status'] ?? $status;
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
            return RespondActive::clientError(__('messages.whatsapp_pairing_code_failed'));
        }

        return RespondActive::success(__('messages.whatsapp_pairing_code_ready'), [
            'status' => 'pending_pairing',
            'session_id' => $sessionId,
            'pairing_code' => $pairingCode,
            'phone' => $phone,
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
            return RespondActive::clientError($result['error'] ?? __('messages.whatsapp_status_failed'));
        }

        $data = $result['data'] ?? [];
        $connectionStatus = $data['status'] ?? 'disconnected';
        $phone = $data['phone'] ?? null;

        $this->syncSessionRecord($user->id, $sessionId, $connectionStatus, $phone);

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

        return RespondActive::success(__('messages.whatsapp_disconnected'));
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
