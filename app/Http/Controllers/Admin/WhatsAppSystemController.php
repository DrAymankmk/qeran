<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\External\BaileysGateway;
use App\Services\WhatsApp\WhatsAppSystemSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $data = $this->normalizeLiveConnectedFields($data);
        $record = WhatsAppSystemSessionService::syncFromGateway($data);

        return response()->json([
            'ok' => true,
            'data' => $data,
            'session_meta' => WhatsAppSystemSessionService::sessionMeta($record),
            'error' => null,
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
        WhatsAppSystemSessionService::clearAdminDisconnect($sessionId);

        $status = BaileysGateway::getStatus($sessionId, true, 15);
        $data = is_array($status['data'] ?? null) ? $status['data'] : [];

        if (($data['status'] ?? '') === 'connected' && ($data['socketAlive'] ?? false)) {
            WhatsAppSystemSessionService::syncFromGateway($data);

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
            $result = BaileysGateway::startSession($sessionId, 45);
            if ($result['ok']) {
                $retry = BaileysGateway::getStatus($sessionId, true, 20);
                if ($retry['ok'] && is_array($retry['data'] ?? null)) {
                    WhatsAppSystemSessionService::syncFromGateway($retry['data']);
                }
            }

            return response()->json([
                'ok' => $result['ok'],
                'data' => $result['data'],
                'session_meta' => WhatsAppSystemSessionService::sessionMeta(),
                'error' => $result['error'],
            ]);
        }

        $result = BaileysGateway::startSession($sessionId, 45);

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
            return response()->json([
                'ok' => false,
                'ready' => false,
                'data' => $qr['data'],
                'error' => $qr['error'] ?? __('admin.whatsapp-qr-failed'),
            ]);
        }

        $data = $qr['data'] ?? [];

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
        WhatsAppSystemSessionService::markAdminDisconnect($sessionId);

        $result = BaileysGateway::deleteSession($sessionId, 35);

        if (! $result['ok']) {
            return back()->with('error', $result['error'] ?? __('admin.whatsapp-disconnect-failed'));
        }

        WhatsAppSystemSessionService::markDisconnected();

        Log::info('WhatsApp system: admin disconnected session', ['session_id' => $sessionId]);

        return back()->with('success', __('admin.whatsapp-disconnected'));
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
}
