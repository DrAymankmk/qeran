<?php

namespace App\Services\Auth;

use App\Models\AppSetting;

class OtpDeliveryChannel
{
    public const WHATSAPP = 'whatsapp';

    public const SMS = 'sms';

    public static function current(): string
    {
        $value = AppSetting::query()
            ->where('key', 'otp_delivery_channel')
            ->value('value');

        return in_array($value, [self::WHATSAPP, self::SMS], true) ? $value : self::WHATSAPP;
    }

    public static function isWhatsApp(): bool
    {
        return self::current() === self::WHATSAPP;
    }

    public static function isSms(): bool
    {
        return self::current() === self::SMS;
    }

    public static function set(string $channel): void
    {
        if (! in_array($channel, [self::WHATSAPP, self::SMS], true)) {
            return;
        }

        AppSetting::query()->updateOrCreate(
            ['key' => 'otp_delivery_channel'],
            [
                'title' => 'OTP Delivery Channel',
                'category' => 'general',
                'type' => 'text',
                'value' => $channel,
            ]
        );
    }

    public static function label(): string
    {
        return self::isSms()
            ? __('admin.otp-channel-sms')
            : __('admin.otp-channel-whatsapp');
    }
}
