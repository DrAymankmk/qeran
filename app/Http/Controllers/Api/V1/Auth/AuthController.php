<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\AuthenticationRequest;
use App\Http\Requests\Api\V1\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\V1\Auth\GuardLoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\SendUserConfirmationCodeRequest;
use App\Http\Requests\Api\V1\Auth\VerifyUserConfirmationCodeRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Invitation;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\Auth\VerificationService;
use App\Services\RespondActive;
use App\Traits\SendsNotificationAndEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Pusher\PushNotifications\PushNotifications;

class AuthController extends Controller
{
    use SendsNotificationAndEmail;

    /**
     * Get translated notification content (helper method)
     */
    protected function getTranslatedNotification(string $notificationKey, string $language, array $params = []): array
    {
        $originalLocale = app()->getLocale();
        app()->setLocale($language);

        try {
            $title = __("notifications.{$notificationKey}.title", $params);
            $body = __("notifications.{$notificationKey}.body", $params);

            if (str_contains($title, 'notifications.')) {
                $title = __('notifications.default.title', $params);
                $body = __('notifications.default.body', $params);
            }

            return [
                'title' => $title,
                'body' => $body,
            ];
        } finally {
            app()->setLocale($originalLocale);
        }
    }

    /**
     * Login for invitation guards
     */
    public function loginGuard(GuardLoginRequest $request)
    {
        $user = User::checkUserExist($request->phone)->first();
        $code = $request->code;

        // Special handling for test phone number
        if ($request->phone == '560452425') {
            $invitation = Invitation::whereHas('guards', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('date', 'asc')->first();

            if (! $invitation) {
                return RespondActive::clientError(__('User doesnot exist!'));
            }

            $invitationName = $invitation->event_name;
            $user['token'] = $user->createToken('token'.$user->id)->plainTextToken;
            $user['event_name'] = $invitationName;

            return RespondActive::success('Logged in successfully', new UserResource($user));
        }

        // Find invitation for the guard
        $invitation = Invitation::whereHas('guards', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('code', $code)->orderBy('date', 'asc')->whereDate('date', '>=', Carbon::now())->first();

        if (! $invitation) {
            return RespondActive::clientError(__('User doesnot exist!'));
        }

        // Verify guard password
        $guardPivot = $invitation->guards()
            ->where('invitation_user.password', '!=', null)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $invitationName = $invitation->event_name;

        // Check event timing restrictions
        $allowedRoles = [
            Constant::INVITATION_USER_ROLE['Guard'],
            Constant::INVITATION_USER_ROLE['Extra Guard'],
        ];

        if (in_array($user->role, $allowedRoles) &&
            (Carbon::now() < $invitation->date || Carbon::now() > Carbon::parse($invitation->date)->addDays(2))) {
            return RespondActive::clientError(__('please wait till the event start!'));
        }

        $user['token'] = $user->createToken('token'.$user->id)->plainTextToken;
        $user['event_name'] = $invitationName;

        return RespondActive::success('Logged in successfully', new UserResource($user));
    }

    /**
     * Login for regular users
     */
    public function login(AuthenticationRequest $request)
    {
        $user = User::checkUserExist($request->phone)->first();

        // Verify user password
        if (! Hash::check($request->password, $user->password)) {
            return RespondActive::clientError('Wrong Info!');
        }

        return $this->handleUserVerification($user, '', $request);
    }

    /**
     * Handle common user verification logic
     */
    private function handleUserVerification($user, $invitationName, $request)
    {
        switch ($user->verified) {
            case 0:
                try {
                    VerificationService::verifyAccount(
                        $user->id,
                        Constant::VERIFICATION_OBJECTIVE['Verify'],
                        Constant::VERIFICATION_INFORMATION_TYPE['Phone'],
                        $request->phone,
                        $request->country_code
                    );
                } catch (Exception $exception) {
                    return RespondActive::clientError(__('Wrong info!'));
                }

                return RespondActive::clientNotActivated('Code sent');

            case 2:
                $user['token'] = $user->createToken('token'.$user->id)->plainTextToken;
                $user['event_name'] = $invitationName;

                return RespondActive::success('Logged in successfully', new UserResource($user));

            default:
                return RespondActive::clientError(__('User account status is invalid!'));
        }
    }

    public function register(RegisterRequest $request)
    {
        if (User::where('phone', $request->phone)
            ->where('password', '!=', null)
            ->first()) {
            return RespondActive::clientError(__('Phone Already exists'));
        }
        $user = User::where(['phone' => $request->phone, 'password' => null])->first();
        if ($user) {
            $user->update($request->validated());
        } else {
            $user = User::create($request->validated());
            if ($request->image) {
                storeImage([
                    'value' => $request->image,
                    'folderName' => Constant::USER_IMAGE_FOLDER_NAME,
                    'model' => $user,
                    'saveInDatabase' => true,
                ]);
            }
        }
        try {
            VerificationService::verifyAccount(
                $user->id,
                Constant::VERIFICATION_OBJECTIVE['Verify'],
                Constant::VERIFICATION_INFORMATION_TYPE['Phone'],
                $request->phone,
                $request->country_code
            );
        } catch (Exception $exception) {
            return RespondActive::clientError(__('Wrong info!'));
        }
        $user['token'] = $user->createToken('token'.$user->id)->plainTextToken;

        // Send notification to admins about new user registration
        try {
            $this->sendAdminNotification(
                notificationKey: 'user_registered',
                targetId: $user->id,
                params: [
                    'user_id' => $user->id,
                    'user_name' => $user->name ?? 'User',
                ],
                category: Constant::NOTIFICATION_CATEGORY['User'] ?? 3,
                notificationType: Constant::NOTIFICATION_USER_TYPES['New User Registered'] ?? 1,
                 emailTo: env('MAIL_TO_ADDRESS'),
                emailSubject: 'New User Registered - '.($user->name ?? 'User').' (ID: '.$user->id.')',
                emailView: 'emails.user.new_user_registered'
            );
        } catch (\Exception $e) {
            // Log error but don't break registration flow
            \Illuminate\Support\Facades\Log::error('Failed to send registration notification: '.$e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getTraceAsString(),
            ]);
        }

        return RespondActive::clientNotActivated('Code send successfully.');
    }

    public function sendCode(SendUserConfirmationCodeRequest $request)
    {
        if (auth('sanctum')->user()) {
            $user = auth('sanctum')->user();
        } else {
            $user = User::checkUserExist($request->phone)->first();
        }

        try {
            VerificationService::verifyAccount(
                $user?->id,
                $request->type ?? Constant::VERIFICATION_OBJECTIVE['Verify'],
                Constant::VERIFICATION_INFORMATION_TYPE['Phone'],
                $request->phone,
                $request->country_code
            );
        } catch (Exception $exception) {
            return RespondActive::clientError(__('Wrong phone number!'));
        }

        return RespondActive::success(__('Code sent successfully.'));
    }

    public function verifyCode(VerifyUserConfirmationCodeRequest $request)
    {
        $check = VerificationCode::checkCode($request->phone, $request->code, $request->type ?? Constant::VERIFICATION_OBJECTIVE['Verify'])->first();

        if (! $check) {
            return RespondActive::clientError('Invalid code.');
        }

        switch (auth('sanctum')->user()) {
            case true:
                $user = auth('sanctum')->user();

                auth('sanctum')->user()->update([
                    'phone' => $request->phone,
                    'country_code' => $request->country_code,
                ]);
                break;
            case false:
                $user = User::checkUserExist($request->phone)->first();

                break;
        }
        $check->update(['used' => Constant::VERIFICATION_USED['Used']]);
        $user->update(['verified' => 2]);

        $user['token'] = $user->createToken('token'.$user->id)->plainTextToken;

        return RespondActive::success('Verified successfully.', new UserResource($user));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        if ($request->old_password) {
            $user = auth('sanctum')->user();

            if (Hash::check($request->old_password, auth('sanctum')->user()->password)) {
                $user->password = $request->password;
                $user->save();

                return RespondActive::success(__('Password changed successfully.'));
            }

            return RespondActive::clientError(__('Wrong password!'));
        } else {
            $user = User::checkUserExist($request->phone)->first();

            $user->password = $request->password;
            $user->save();

            return RespondActive::success(__('Password changed successfully.'));
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return RespondActive::success('Logged out successfully.');
    }

    public function delete()
    {
        $user = auth()->user();

        // Store user data before deletion
        $userId = $user->id;
        $userName = $user->name ?? 'User';
        $userEmail = $user->email;

        // Delete the user
        $user->delete();

        // Send notification to admins about user deleted (after deletion to avoid issues)
        try {
            $this->sendAdminNotification(
                notificationKey: 'user_deleted',
                targetId: $userId,
                params: [
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'deleted_at' => now()->utc()->format('Y-m-d H:i:s'),
                ],
                category: Constant::NOTIFICATION_CATEGORY['User'] ?? 3,
                notificationType: Constant::NOTIFICATION_USER_TYPES['Account Banned or Unbanned'] ?? 3, // Use existing type or add new one
                 emailTo: env('MAIL_TO_ADDRESS'),
                emailSubject: 'User Deleted - '.$userName.' (ID: '.$userId.')',
                emailView: 'emails.user.user_deleted',
                emailData: [
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'deleted_at' => now()->utc()->format('Y-m-d H:i:s'),
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't break deletion flow
            \Illuminate\Support\Facades\Log::error('Failed to send user deletion notification: '.$e->getMessage(), [
                'user_id' => $userId,
                'error' => $e->getTraceAsString(),
            ]);
        }

        return RespondActive::success('Account deleted successfully.');
    }

    public function changeLanguage(Request $request)
    {
        auth()->user()->update(['language' => $request->language]);

        return RespondActive::success(__('auth.language_changed'));
    }

    public function generateAuthToken(Request $request)
    {
        $beamsClient = new PushNotifications([
            'instanceId' => config('services.Beams.Beams_Instance_Id'),
            'secretKey' => config('services.Beams.Beams_Secret_key'),
        ]);

        $beamsToken = $beamsClient->generateToken('users-'.auth()->id());

        return response()->json($beamsToken);
    }
}