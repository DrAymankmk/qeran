<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\UpdateUserProfileRequest;
use App\Http\Resources\User\ProfileResource;
use App\Services\RespondActive;

class  ProfileController extends Controller
{
    public function show()
    {
        return RespondActive::success('The action ran successfully!', new ProfileResource(auth('sanctum')->user()));


    }

    public function update(UpdateUserProfileRequest $request)
    {
        $user = auth()->user();
        $user->update($request->validated());
        if ($request->image) {
            if ($user->hubFiles()->exists()) {
                deleteImage($user->hubFiles->get_folder_file(), $user->hubFiles());
            }

            storeImage([
                'value' => $request->image,
                'folderName' => Constant::USER_IMAGE_FOLDER_NAME,
                'model' => $user,
                'saveInDatabase' => true
            ]);
        }
        auth()->user()['token'] = auth()->user()->createToken('token' . auth()->id())->plainTextToken;

        return RespondActive::success(__('Updated successfully'), new ProfileResource(auth()->user()));
    }


}
