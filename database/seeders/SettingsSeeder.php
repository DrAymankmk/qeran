<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

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

// create new settings
AppSetting::create([
    'key' => 'extra_guard_fees',
    'value' => '30'
]);
    }
}