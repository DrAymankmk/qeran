<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Setting\GetSettingRequest;
use App\Http\Resources\Setting\SettingResource;
use App\Services\RespondActive;

class GetSettings extends Controller
{
    public function __invoke(GetSettingRequest $request)
    {
        $settings = SettingResource::collection(settings($request->type));

        return RespondActive::success('The action ran successfully!', $settings);
    }
}
