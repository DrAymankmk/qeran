<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\External\BaileysGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WhatsAppSystemController extends Controller
{
    public function index(): View
    {
        $configured = BaileysGateway::isConfigured();
        $status = $configured ? BaileysGateway::getStatus() : null;
        $qr = null;

        if ($configured && $this->shouldLoadQr($status)) {
            BaileysGateway::startSession();
            $qr = BaileysGateway::getQr();
        }

        return view('admin.whatsapp-system.index', [
            'configured' => $configured,
            'gatewayUrl' => config('services.baileys.gateway_url'),
            'sessionId' => BaileysGateway::systemSessionId(),
            'status' => $status ?? ['ok' => false, 'data' => null, 'error' => null],
            'qr' => $qr ?? ['ok' => false, 'data' => null, 'error' => null],
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

    public function refreshQr(): RedirectResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return back()->with('error', __('admin.whatsapp-gateway-not-configured'));
        }

        BaileysGateway::startSession();
        $qr = BaileysGateway::getQr();

        if (! $qr['ok']) {
            return back()->with('error', $qr['error'] ?? __('admin.whatsapp-qr-failed'));
        }

        $data = $qr['data'] ?? [];
        if (($data['status'] ?? '') === 'connected') {
            return back()->with('success', __('admin.whatsapp-already-connected'));
        }

        if (empty($data['qrImage'])) {
            return back()->with('error', __('admin.whatsapp-qr-not-ready'));
        }

        return back()->with('success', __('admin.whatsapp-qr-generated'));
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

    /**
     * @param  array{ok: bool, data?: array<string, mixed>|null}|null  $status
     */
    protected function shouldLoadQr(?array $status): bool
    {
        if (! $status || ! $status['ok']) {
            return true;
        }

        $connectionStatus = $status['data']['status'] ?? 'disconnected';

        return $connectionStatus !== 'connected';
    }
}
