<?php

namespace App\Http\Controllers\Api\V1\Packages;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Package;
use App\Helpers\Constant;
use App\Http\Resources\Package\PackageResource;
use App\Services\RespondActive;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class PackageController extends Controller
{

    /**
     * Get packages for an invitation (Static + Free packages).
     * Free packages are shown only once per user; if already used, they are excluded.
     */
    public function invitationPackages(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            return RespondActive::clientError(__('This invitation does not belong to you'), [], 403);
        }

        $appSetting = AppSetting::query()
            ->where('key', 'account_number')
            ->first();

        $dynamicPackage = Package::active()
            ->invitationPackageType($invitation->invitation_type)
            ->PackageType(Constant::PACKAGE_TYPE['Dynamic Package'])
            ->latest()
            ->first();

        $packages = Package::active()
            ->invitationPackageType($invitation->invitation_type)
            ->whereIn('package_type', [
                Constant::PACKAGE_TYPE['Static Package'],
                Constant::PACKAGE_TYPE['Free Package'],
            ])
            ->excludeUsedFreePackagesForUser(auth()->id())
            ->get();

        $data = [
            'single_invitation_price' => $dynamicPackage?->price ?? 0,
            'account_number' => $appSetting?->value ?? '',
            'packages' => PackageResource::collection($packages),
            'invitation_type' => $invitation->invitation_type,
        ];

        return RespondActive::success(__('action ran successfully'), $data);
    }
}