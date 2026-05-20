<?php

namespace App\Jobs;

use App\Helpers\Constant;
use App\Services\External\BaileysWhatsApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendBaileysInvitationMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $hostUserId,
        public int $invitationId,
        public int $guestUserId,
        public string $countryCode,
        public string $phone,
        public string $message,
        public string $referenceId = '',
    ) {}

    public function handle(): void
    {
        $response = BaileysWhatsApp::sendFromSession(
            'user_'.$this->hostUserId,
            $this->countryCode.$this->phone,
            $this->message,
            $this->referenceId
        );

        if (isset($response->sent) && $response->sent === 'true') {
            DB::table('invitation_user')
                ->where('invitation_id', $this->invitationId)
                ->where('user_id', $this->guestUserId)
                ->update(['seen' => Constant::SEEN_STATUS['Sent']]);

            return;
        }

        Log::warning('Baileys invitation send failed', [
            'host_user_id' => $this->hostUserId,
            'invitation_id' => $this->invitationId,
            'guest_user_id' => $this->guestUserId,
            'error' => $response->error ?? null,
        ]);
    }
}
