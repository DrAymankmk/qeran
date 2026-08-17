<?php

namespace App\Jobs;

use App\Helpers\Constant;
use App\Models\Invitation;
use App\Models\InvitationContactLog;
use App\Support\PhoneNumber;
use App\Services\External\BaileysWhatsApp;
use App\Services\Invitation\InvitationBuilderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBaileysInvitationContactMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public int $contactLogId,
        public int $hostUserId,
        public int $invitationId,
        public string $countryCode,
        public string $phone,
        public string $message,
        public string $referenceId = '',
    ) {}

    public function handle(): void
    {
        Log::info('SendBaileysInvitationContactMessage started', [
            'contact_log_id' => $this->contactLogId,
            'invitation_id' => $this->invitationId,
            'host_user_id' => $this->hostUserId,
            'attempt' => $this->attempts(),
        ]);

        $log = InvitationContactLog::query()->find($this->contactLogId);

        if (! $log) {
            Log::warning('SendBaileysInvitationContactMessage skipped — contact log missing', [
                'contact_log_id' => $this->contactLogId,
            ]);

            return;
        }

        $targetPhone = PhoneNumber::e164ForWhatsAppPairing($this->countryCode, $this->phone);

        if ($this->referenceId !== '') {
            $log->update(['reference_id' => $this->referenceId]);
        }

        $media = app(InvitationBuilderService::class)->guestShareMedia(
            Invitation::query()->find($this->invitationId)
        );

        $response = BaileysWhatsApp::sendFromSession(
            'user_'.$this->hostUserId,
            $targetPhone,
            $this->message,
            $this->referenceId,
            $media['url'] ?? null,
            $media['type'] ?? null
        );

        if (isset($response->sent) && $response->sent === 'true') {
            $messageId = is_string($response->id ?? null) ? $response->id : null;

            $log->update([
                'send_status' => Constant::INVITATION_CONTACT_SEND_STATUS['sent'],
                'sent_at' => now(),
                'whatsapp_message_id' => $messageId,
                'error_message' => null,
            ]);

            Log::info('Baileys contact invitation sent — awaiting delivery/read webhooks', [
                'contact_log_id' => $this->contactLogId,
                'reference_id' => $this->referenceId,
                'whatsapp_message_id' => $messageId,
                'host_session' => 'user_'.$this->hostUserId,
            ]);

            if (! $messageId) {
                Log::warning('Baileys send ok but no idMessage from gateway — delivered_at/read_at cannot be matched', [
                    'contact_log_id' => $this->contactLogId,
                    'reference_id' => $this->referenceId,
                ]);
            }

            if ($this->referenceId === '') {
                Log::warning('Baileys send without reference_id — receipt webhook matching is weaker', [
                    'contact_log_id' => $this->contactLogId,
                ]);
            }

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
            'target_phone' => $targetPhone,
            'error' => $errorMessage,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendBaileysInvitationContactMessage failed permanently', [
            'contact_log_id' => $this->contactLogId,
            'invitation_id' => $this->invitationId,
            'host_user_id' => $this->hostUserId,
            'error' => $exception->getMessage(),
        ]);

        InvitationContactLog::query()
            ->where('id', $this->contactLogId)
            ->update([
                'send_status' => Constant::INVITATION_CONTACT_SEND_STATUS['failed'],
                'error_message' => $exception->getMessage(),
            ]);
    }
}
