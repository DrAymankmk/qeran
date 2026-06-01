<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsappSession;
use App\Services\External\BaileysGateway;
use App\Services\WhatsApp\UserWhatsAppSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppClientsController extends Controller
{
    public function index(Request $request): View
    {
        $configured = BaileysGateway::isConfigured();
        $filter = $request->query('filter', 'all');

        $query = WhatsappSession::query()
            ->with('user:id,name,email,phone,country_code')
            ->where('session_id', 'like', 'user\_%')
            ->orderByDesc('updated_at');

        if ($filter === 'connected') {
            $query->where('status', 'connected');
        } elseif ($filter === 'disconnected') {
            $query->where('status', 'disconnected');
        }

        $sessions = $query->paginate(20)->withQueryString();

        $clients = $sessions->getCollection()->map(function (WhatsappSession $session) use ($configured) {
            $user = $session->user;
            $live = $configured
                ? UserWhatsAppSessionService::liveGatewayStatus($session->session_id)
                : null;
            $interpreted = UserWhatsAppSessionService::interpretLiveStatus($live, $session->status);

            return [
                'session' => $session,
                'user' => $user,
                'user_id' => $session->user_id,
                'session_id' => $session->session_id,
                'db_status' => $session->status,
                'db_phone' => $session->phone,
                'phone_display' => UserWhatsAppSessionService::formatPhoneDisplay(
                    $user?->country_code,
                    $user?->phone,
                    $session->phone
                ),
                'user_name' => $user?->name ?? ('#'.$session->user_id),
                'user_email' => $user?->email,
                'connected_at' => $session->connected_at,
                'disconnected_at' => $session->disconnected_at,
                'last_seen_at' => $session->last_seen_at,
                'live' => $interpreted,
                'live_raw' => $live,
            ];
        });

        $sessions->setCollection($clients);

        return view('admin.whatsapp-clients.index', [
            'configured' => $configured,
            'sessions' => $sessions,
            'filter' => $filter,
            'gatewayUrl' => config('services.baileys.gateway_internal_url')
                ?: config('services.baileys.gateway_url'),
        ]);
    }

    public function disconnect(int $user): RedirectResponse
    {
        if (! User::query()->whereKey($user)->exists()) {
            return back()->with('error', __('admin.whatsapp-client-not-found'));
        }

        if (! BaileysGateway::isConfigured()) {
            return back()->with('error', __('admin.whatsapp-gateway-not-configured'));
        }

        UserWhatsAppSessionService::disconnectUser($user);

        return back()->with('success', __('admin.whatsapp-client-disconnected'));
    }
}
