<?php

namespace App\Jobs;

use App\Helpers\Constant;
use App\Models\InvitationContactLog;
use App\Services\External\BaileysWhatsApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendBaileysInvitationContactMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $contactLogId,
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
        $log = InvitationContactLog::query()->find($this->contactLogId);

        if (! $log) {
            return;
        }

        $response = BaileysWhatsApp::sendFromSession(
            'user_'.$this->hostUserId,
            $this->countryCode.$this->phone,
            $this->message,
            $this->referenceId
        );

        if (isset($response->sent) && $response->sent === 'true') {
            $log->update([
                'send_status' => Constant::INVITATION_CONTACT_SEND_STATUS['sent'],
                'sent_at' => now(),
                'error_message' => null,
            ]);

            DB::table('invitation_user')
                ->where('invitation_id', $this->invitationId)
                ->where('user_id', $this->guestUserId)
                ->update(['seen' => Constant::SEEN_STATUS['Sent']]);

            return;
        }

        $errorMessage = is_object($response->error ?? null)
            ? ($response->error->message ?? 'Unknown error')
            : (string) ($response->error ?? 'Unknown error');

        $log->update([
            'send_status' => Constant::INVITATION_CONTACT_SEND_STATUS['failed'],
            'error_message' => $errorMessage,
        ]);

        Log::warning('Baileys contact invitation send failed', [
            'contact_log_id' => $this->contactLogId,
            'host_user_id' => $this->hostUserId,
            'invitation_id' => $this->invitationId,
            'guest_user_id' => $this->guestUserId,
            'error' => $errorMessage,
        ]);
    }
}
