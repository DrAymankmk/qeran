<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\External\BaileysGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $status = BaileysGateway::getStatus();

        return response()->json([
            'ok' => $status['ok'],
            'data' => $status['data'],
            'error' => $status['error'],
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

        $result = BaileysGateway::startSession();

        return response()->json([
            'ok' => $result['ok'],
            'data' => $result['data'],
            'error' => $result['error'],
        ], $result['ok'] ? 200 : 502);
    }

    public function qr(Request $request): JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return response()->json([
                'ok' => false,
                'error' => __('admin.whatsapp-gateway-not-configured'),
            ], 503);
        }

        $waitMs = max(0, min(60_000, (int) $request->query('waitMs', 8000)));
        $qr = BaileysGateway::getQr(null, $waitMs);

        if (! $qr['ok']) {
            return response()->json([
                'ok' => false,
                'data' => $qr['data'],
                'error' => $qr['error'] ?? __('admin.whatsapp-qr-failed'),
            ], 502);
        }

        $data = $qr['data'] ?? [];

        return response()->json([
            'ok' => true,
            'data' => $data,
            'ready' => ($data['ready'] ?? true) && ! empty($data['qrImage']),
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

        $result = BaileysGateway::deleteSession();

        if (! $result['ok']) {
            return back()->with('error', $result['error'] ?? __('admin.whatsapp-disconnect-failed'));
        }

        return back()->with('success', __('admin.whatsapp-disconnected'));
    }
}
