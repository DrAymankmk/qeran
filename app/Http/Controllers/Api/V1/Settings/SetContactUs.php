<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Setting\SetContactUsRequest;
use App\Models\ContactUs;
use App\Services\RespondActive;

class SetContactUs extends Controller
{
    public function __invoke(SetContactUsRequest $request)
    {
        ContactUs::create($request->validated() + ['user_id' => auth('sanctum')->id()]);

        return RespondActive::success('Message Sent, we will contact you soon.');
    }
}
