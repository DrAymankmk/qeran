<?php

namespace App\Http\Controllers\Api\V1\AppSettings;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppSettings\AppSettingsResource;
use App\Models\AppSetting;
use App\Services\RespondActive;

class AppSettings extends Controller
{
    public function __invoke()
    {
        return RespondActive::success('The action ran successfully!', AppSettingsResource::collection(AppSetting::get()));
    }
}
