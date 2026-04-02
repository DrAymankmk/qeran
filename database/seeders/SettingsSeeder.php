<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // delete all settings
        AppSetting::truncate();

        // youtube -tiktok - snapchat - instagram - twitter -  extra_invitation_fees - extra_guard_fees - whatsapp_number - mail-to - about us-  البريد الإلكتروني	 - رقم الواتساب- account_number -

        // create new settings
        $data = [
            [
                'key' => 'youtube',
                'value' => '',
                'category' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'tiktok',
                'value' => '',
                'category' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'snapchat',
                'value' => '',
                'category' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'instagram',
                'value' => '',
                'category' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'twitter',
                'value' => '',
                'category' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'facebook',
                'value' => '',
                'category' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'extra_guard_fees',
                'value' => '30',
                'category' => 'general',
                'type' => 'number',
            ],
            [
                'key' => 'extra_invitation_fees',
                'value' => '30',
                'category' => 'general',
                'type' => 'number',
            ],

            [
                'key' => 'phone_number',
                'value' => '',
                'category' => 'general',
                'type' => 'number',
            ],

            [
                'key' => 'whatsapp_number',
                'value' => '',
                'category' => 'general',
                'type' => 'number',
            ],
            [
                'key' => 'email_address',
                'value' => '',
                'category' => 'general',
                'type' => 'email',
            ],
            [
                'key' => 'about_us',
                'value' => '',
                'category' => 'general',
                'type' => 'textarea',
            ],
            [
                'key' => 'account_number',
                'value' => '',
                'category' => 'general',
                'type' => 'text',
            ],
        ];

        foreach ($data as $d) {
            AppSetting::create($d);
        }
    }
}