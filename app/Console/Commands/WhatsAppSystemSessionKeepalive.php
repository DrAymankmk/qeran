<?php

namespace App\Console\Commands;

use App\Services\External\BaileysGateway;
use App\Services\WhatsApp\WhatsAppSystemSessionService;
use Illuminate\Console\Command;

class WhatsAppSystemSessionKeepalive extends Command
{
    protected $signature = 'whatsapp:system-keepalive';

    protected $description = 'Probe the system WhatsApp session and reconnect if the gateway socket dropped (OTP number).';

    public function handle(): int
    {
        if (! BaileysGateway::isConfigured()) {
            $this->warn('Baileys gateway is not configured.');

            return self::SUCCESS;
        }

        $sessionId = BaileysGateway::systemSessionId();

        if (WhatsAppSystemSessionService::adminRequestedDisconnect($sessionId)) {
            $this->line('System session is admin-locked — skipping keepalive.');

            return self::SUCCESS;
        }

        $status = BaileysGateway::getStatus($sessionId, true, 20);

        if (! $status['ok']) {
            $this->error('Gateway unreachable: '.($status['error'] ?? 'unknown'));

            return self::FAILURE;
        }

        $data = is_array($status['data'] ?? null) ? $status['data'] : [];
        $data = WhatsAppSystemSessionService::maybeReconnect($sessionId, $data);
        WhatsAppSystemSessionService::syncFromGateway($data);

        $live = ($data['status'] ?? '') === 'connected' && ($data['socketAlive'] ?? false);

        if ($live) {
            $this->info('System WhatsApp session is connected.');

            return self::SUCCESS;
        }

        $registered = (bool) ($data['registeredOnDisk'] ?? false);
        $reported = (string) ($data['status'] ?? 'disconnected');

        if ($registered) {
            $this->warn("Socket down but still registered on gateway (status: {$reported}) — phone may still show linked device.");
        } else {
            $this->warn("System session not live (status: {$reported}). Scan QR in admin if needed.");
        }

        return self::SUCCESS;
    }
}
