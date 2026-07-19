<?php

namespace App\Services\Invitation;

use App\Helpers\Constant;
use App\Models\Invitation;
use App\Models\InvitationContactLog;
use App\Services\External\FourJawalySms;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InvitationContactReminderService
{
    /**
     * @return array{sent: int, failed: int, skipped: int}
     */
    public function sendDueReminders(?int $hoursBefore = null, bool $force = false): array
    {
        $stats = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        if (! $force && ! InvitationContactReminderSettings::enabled()) {
            return $stats;
        }

        if (! FourJawalySms::isConfigured()) {
            Log::warning('Invitation contact reminder SMS skipped: 4jawaly not configured');

            return $stats;
        }

        $hoursBefore ??= InvitationContactReminderSettings::hoursBefore();
        $now = Carbon::now();

        $invitations = Invitation::query()
            ->with(['category', 'builderSetting', 'contactLogs'])
            ->whereNotNull('date')
            ->whereNotIn('status', [
                Constant::INVITATION_STATUS['Cancelled'],
                Constant::INVITATION_STATUS['Finished Invitation'],
                Constant::INVITATION_STATUS['Rejected'],
            ])
            ->whereHas('contactLogs', function ($query) {
                $query->whereNull('reminder_sent_at')
                    ->whereIn('send_status', [
                        Constant::INVITATION_CONTACT_SEND_STATUS['pending'],
                        Constant::INVITATION_CONTACT_SEND_STATUS['sent'],
                    ]);
            })
            ->get();

        foreach ($invitations as $invitation) {
            $eventAt = $this->eventDateTime($invitation);
            if ($eventAt === null) {
                continue;
            }

            $windowStart = $eventAt->copy()->subHours($hoursBefore);
            if ($now->lt($windowStart) || $now->gte($eventAt)) {
                continue;
            }

            /** @var Collection<int, InvitationContactLog> $logs */
            $logs = $invitation->contactLogs
                ->whereNull('reminder_sent_at')
                ->filter(function (InvitationContactLog $log) {
                    return filled($log->phone)
                        && in_array((int) $log->send_status, [
                            Constant::INVITATION_CONTACT_SEND_STATUS['pending'],
                            Constant::INVITATION_CONTACT_SEND_STATUS['sent'],
                        ], true);
                });

            foreach ($logs as $log) {
                $result = $this->sendReminderToContact($invitation, $log);
                $stats[$result]++;
            }
        }

        return $stats;
    }

    /**
     * @return 'sent'|'failed'|'skipped'
     */
    public function sendReminderToContact(Invitation $invitation, InvitationContactLog $log): string
    {
        if ($log->reminder_sent_at !== null) {
            return 'skipped';
        }

        $to = FourJawalySms::formatRecipient($log->country_code, $log->phone);
        $message = $this->buildReminderMessage($invitation);

        $response = FourJawalySms::send($to, $message, $log->country_code);

        if (! ($response['ok'] ?? false)) {
            $log->update([
                'reminder_error_message' => $response['error'] ?? 'send_failed',
            ]);

            Log::warning('Invitation contact reminder SMS failed', [
                'contact_log_id' => $log->id,
                'invitation_id' => $invitation->id,
                'error' => $response['error'] ?? null,
            ]);

            return 'failed';
        }

        $log->update([
            'reminder_sent_at' => now(),
            'reminder_error_message' => null,
        ]);

        return 'sent';
    }

    public function buildReminderMessage(Invitation $invitation): string
    {
        $previousLocale = app()->getLocale();
        app()->setLocale('ar');

        try {
            return __('messages.invitation_contact_reminder_sms_template', [
                'event_type' => $this->resolveEventType($invitation),
                'host_name' => $invitation->host_name ?: ($invitation->event_name ?: ''),
            ]);
        } finally {
            app()->setLocale($previousLocale);
        }
    }

    public function resolveEventType(Invitation $invitation): string
    {
        if ($invitation->category && filled($invitation->category->name)) {
            return (string) $invitation->category->name;
        }

        $eventCategory = $invitation->builderSetting?->event_category;
        if (filled($eventCategory)) {
            $labelKey = 'label_'.app()->getLocale();
            $fallbackKey = app()->getLocale() === 'ar' ? 'label_ar' : 'label_en';
            $types = config('invitation_builder.event_types', []);
            $type = $types[$eventCategory] ?? null;

            if (is_array($type)) {
                $label = $type[$labelKey] ?? $type[$fallbackKey] ?? $type['label_ar'] ?? null;
                if (filled($label)) {
                    return (string) $label;
                }
            }
        }

        if (filled($invitation->event_name)) {
            return (string) $invitation->event_name;
        }

        if (filled($invitation->name)) {
            return (string) $invitation->name;
        }

        return __('messages.invitation_contact_reminder_default_event');
    }

    public function eventDateTime(Invitation $invitation): ?Carbon
    {
        if (! $invitation->date) {
            return null;
        }

        try {
            return $invitation->time
                ? Carbon::parse($invitation->date.' '.$invitation->time)
                : Carbon::parse($invitation->date)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
