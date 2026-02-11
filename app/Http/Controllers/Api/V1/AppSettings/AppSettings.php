<?php

namespace App\Http\Controllers\Api\V1\AppSettings;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppSettings\AppSettingsResource;
use App\Models\AppSetting;
use App\Services\RespondActive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppSettings extends Controller
{
    /**
     * Get all app settings.
     */
    public function index(): JsonResponse
    {
        $settings = AppSetting::orderBy('key')->get();

        return RespondActive::success('Settings retrieved successfully.', AppSettingsResource::collection($settings));
    }

    /**
     * Get a single app setting by key.
     */
    public function show(Request $request): JsonResponse
    {
        $setting = AppSetting::key($request->key)->first();

        if (!$setting) {
            return RespondActive::error('Setting not found.', 404);
        }

        return RespondActive::success('Setting retrieved successfully.', new AppSettingsResource($setting));
    }

    /**
     * Get app settings filtered by category.
     */
    public function byCategory(Request $request): JsonResponse
    {
        $category = $request->get('category');

        $query = AppSetting::query()->orderBy('key');

        if ($category) {
            $query->where('category', $category);
        }

        $settings = $query->get();

        return RespondActive::success(
            'Settings retrieved successfully.',
            AppSettingsResource::collection($settings)
        );
    }
}