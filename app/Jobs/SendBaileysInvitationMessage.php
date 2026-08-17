<?php

namespace App\Jobs;

use App\Helpers\Constant;
use App\Models\Invitation;
use App\Services\External\BaileysWhatsApp;
use App\Services\Invitation\InvitationBuilderService;
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
        $mediaUrl = null;
        $mediaType = null;

        try {
            $media = app(InvitationBuilderService::class)->guestShareMedia(
                Invitation::query()->find($this->invitationId)
            );
            if (is_array($media)) {
                $mediaUrl = $media['url'] ?? null;
                $mediaType = $media['type'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to resolve invitation share media', [
                'invitation_id' => $this->invitationId,
                'error' => $e->getMessage(),
            ]);
        }

        $response = BaileysWhatsApp::sendFromSession(
            'user_'.$this->hostUserId,
            $this->countryCode.$this->phone,
            $this->message,
            $this->referenceId,
            $mediaUrl,
            $mediaType
        );

        if ((! isset($response->sent) || $response->sent !== 'true') && $mediaUrl) {
            $response = BaileysWhatsApp::sendFromSession(
                'user_'.$this->hostUserId,
                $this->countryCode.$this->phone,
                $this->message,
                $this->referenceId
            );
        }

        if (isset($response->sent) && $response->sent === 'true') {
            DB::table('invitation_user')
                ->where('invitation_id', $this->invitationId)
                ->where('user_id', $this->guestUserId)
                ->update(['seen' => Constant::SEEN_STATUS['Sent']]);

            try {
                $qrMedia = app(InvitationBuilderService::class)->guestQrShareMedia(
                    Invitation::query()->find($this->invitationId),
                    null,
                    $this->guestUserId
                );
                $qrBase64 = is_array($qrMedia) ? ($qrMedia['base64'] ?? null) : null;
                if (is_string($qrBase64) && $qrBase64 !== '') {
                    $qrResponse = BaileysWhatsApp::sendFromSession(
                        'user_'.$this->hostUserId,
                        $this->countryCode.$this->phone,
                        __('messages.invitation_qr_caption'),
                        $this->referenceId !== '' ? $this->referenceId.'-qr' : '',
                        null,
                        'image',
                        $qrBase64
                    );
                    if (! isset($qrResponse->sent) || $qrResponse->sent !== 'true') {
                        Log::warning('Invitation QR WhatsApp send failed', [
                            'invitation_id' => $this->invitationId,
                            'guest_user_id' => $this->guestUserId,
                            'error' => $qrResponse->error ?? null,
                        ]);
                    }
                } else {
                    Log::warning('Invitation QR PNG was not generated', [
                        'invitation_id' => $this->invitationId,
                        'guest_user_id' => $this->guestUserId,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to send invitation QR to guest', [
                    'invitation_id' => $this->invitationId,
                    'guest_user_id' => $this->guestUserId,
                    'error' => $e->getMessage(),
                ]);
            }

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
