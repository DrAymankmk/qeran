<?php

namespace App\Services\Invitation;

use App\Models\AppSetting;

class InvitationContactReminderSettings
{
    public const ENABLED_KEY = 'invitation_contact_reminder_enabled';

    public const HOURS_BEFORE_KEY = 'invitation_contact_reminder_hours_before';

    public const DEFAULT_HOURS_BEFORE = 24;

    public static function enabled(): bool
    {
        $value = AppSetting::query()
            ->where('key', self::ENABLED_KEY)
            ->value('value');

        if ($value === null) {
            return false;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }

    public static function hoursBefore(): int
    {
        $value = AppSetting::query()
            ->where('key', self::HOURS_BEFORE_KEY)
            ->value('value');

        $hours = (int) $value;

        return $hours > 0 ? $hours : self::DEFAULT_HOURS_BEFORE;
    }

    public static function setEnabled(bool $enabled): void
    {
        AppSetting::query()->updateOrCreate(
            ['key' => self::ENABLED_KEY],
            [
                'title' => 'Invitation Contact Reminder SMS Enabled',
                'category' => 'general',
                'type' => 'text',
                'value' => $enabled ? '1' : '0',
            ]
        );
    }

    public static function setHoursBefore(int $hours): void
    {
        $hours = max(1, $hours);

        AppSetting::query()->updateOrCreate(
            ['key' => self::HOURS_BEFORE_KEY],
            [
                'title' => 'Invitation Contact Reminder Hours Before Event',
                'category' => 'general',
                'type' => 'text',
                'value' => (string) $hours,
            ]
        );
    }
}
