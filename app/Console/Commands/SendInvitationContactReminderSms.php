<?php

namespace App\Console\Commands;

use App\Services\External\FourJawalySms;
use App\Services\Invitation\InvitationContactReminderService;
use App\Services\Invitation\InvitationContactReminderSettings;
use Illuminate\Console\Command;

class SendInvitationContactReminderSms extends Command
{
    protected $signature = 'invitations:send-contact-reminders
                            {--hours= : Override hours before event}
                            {--force : Run even when reminders are disabled}';

    protected $description = 'Send SMS reminders to invitation contacts before the event time.';

    public function handle(InvitationContactReminderService $service): int
    {
        $force = (bool) $this->option('force');

        if (! $force && ! InvitationContactReminderSettings::enabled()) {
            $this->info('Invitation contact reminder SMS is disabled.');

            return self::SUCCESS;
        }

        if (! FourJawalySms::isConfigured()) {
            $this->warn('4jawaly SMS is not configured.');

            return self::FAILURE;
        }

        $hours = $this->option('hours');
        $hoursBefore = $hours !== null && $hours !== ''
            ? max(1, (int) $hours)
            : InvitationContactReminderSettings::hoursBefore();

        $stats = $service->sendDueReminders($hoursBefore, $force);

        $this->info(sprintf(
            'Reminders processed — sent: %d, failed: %d, skipped: %d (window: %d hours before event).',
            $stats['sent'],
            $stats['failed'],
            $stats['skipped'],
            $hoursBefore
        ));

        return self::SUCCESS;
    }
}
