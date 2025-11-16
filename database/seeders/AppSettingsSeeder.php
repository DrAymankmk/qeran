<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
            [
                'key' => 'extra_guard_fees',
                'value' =>'30'
            ],
            [
                'key' => 'extra_invitation_fees',
                'value' =>'30'
            ],
            [
                'key' => 'facebook',
                'value' => '#'
            ],
            [
                'key' => 'twitter',
                'value' => '#'
            ],
            [
                'key' => 'instagram',
                'value' => '#'
            ],
            [
                'key' => 'snapchat',
                'value' => '#'
            ],
            [
                'key' => 'youtube',
                'value' => '#'
            ],
            [
                'key' => 'tiktok',
                'value' => '#'
            ],
        ];
        foreach ($data as $d){
            AppSetting::create($d);
        }

    }
}
