<?php

use App\Models\AppSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AppSetting::query()->firstOrCreate(
            ['key' => 'otp_delivery_channel'],
            [
                'title' => 'OTP Delivery Channel',
                'category' => 'general',
                'type' => 'text',
                'value' => 'whatsapp',
            ]
        );
    }

    public function down(): void
    {
        AppSetting::query()->where('key', 'otp_delivery_channel')->delete();
    }
};
