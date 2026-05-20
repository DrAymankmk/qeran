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
use App\Services\Auth\Exceptions\VerificationOtpDeliveryException;
use App\Services\Auth\VerificationService;
use App\Services\RespondActive;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Log;
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
                } catch (VerificationOtpDeliveryException $exception) {
                    return $this->otpDeliveryErrorResponse($exception, 'login_unverified');
                } catch (Exception $exception) {
                    Log::error('OTP unexpected error on login', [
                        'user_id' => $user->id,
                        'error' => $exception->getMessage(),
                    ]);

                    return RespondActive::clientError(__('messages.otp_unexpected_error'));
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
        } catch (VerificationOtpDeliveryException $exception) {
            return $this->otpDeliveryErrorResponse($exception, 'register');
        } catch (Exception $exception) {
            Log::error('OTP unexpected error on register', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return RespondActive::clientError(__('messages.otp_unexpected_error'));
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
        $objective = (int) ($request->type ?? Constant::VERIFICATION_OBJECTIVE['Verify']);
        $phone = (string) $request->phone;
        $countryCode = (string) $request->country_code;

        $user = $this->resolveUserForSendCode($request, $objective);

        if (! $user) {
            $message = $objective === (int) Constant::VERIFICATION_OBJECTIVE['Reset']
                ? __('messages.otp_reset_user_not_found')
                : __('messages.otp_user_not_found');

            Log::warning('sendCode: user not found', [
                'objective' => $objective,
                'phone_variants' => VerificationCode::phoneVariants($phone, $countryCode),
            ]);

            return RespondActive::clientError($message);
        }

        if ($objective === (int) Constant::VERIFICATION_OBJECTIVE['Reset'] && ! $user->hasPassword()) {
            Log::warning('sendCode: reset requested for account without password', [
                'user_id' => $user->id,
            ]);

            return RespondActive::clientError(__('messages.otp_reset_account_incomplete'));
        }

        $storedPhone = PhoneNumber::informationForStorage(
            $countryCode,
            $phone,
            (string) $user->phone
        );

        try {
            VerificationService::verifyAccount(
                $user->id,
                $objective,
                Constant::VERIFICATION_INFORMATION_TYPE['Phone'],
                $storedPhone,
                $user->country_code ?? $countryCode
            );
        } catch (VerificationOtpDeliveryException $exception) {
            return $this->otpDeliveryErrorResponse($exception, 'send_code');
        } catch (Exception $exception) {
            Log::error('OTP unexpected error on sendCode', [
                'user_id' => $user->id,
                'objective' => $objective,
                'error' => $exception->getMessage(),
            ]);

            return RespondActive::clientError(__('messages.otp_unexpected_error'));
        }

        $successMessage = $objective === (int) Constant::VERIFICATION_OBJECTIVE['Reset']
            ? __('messages.otp_reset_code_sent')
            : __('messages.otp_code_sent');

        Log::info('sendCode: OTP sent', [
            'user_id' => $user->id,
            'objective' => $objective,
            'stored_phone_suffix' => substr($storedPhone, -4),
        ]);

        return RespondActive::success($successMessage);
    }

    protected function resolveUserForSendCode(
        SendUserConfirmationCodeRequest $request,
        int $objective
    ): ?User {
        $phone = (string) $request->phone;
        $countryCode = (string) $request->country_code;

        if ($objective === (int) Constant::VERIFICATION_OBJECTIVE['Reset']) {
            return User::findByPhone($phone, $countryCode);
        }

        if (auth('sanctum')->user()) {
            return auth('sanctum')->user();
        }

        return User::findByPhone($phone, $countryCode);
    }

    protected function otpDeliveryErrorResponse(VerificationOtpDeliveryException $exception, string $flow): \Illuminate\Http\JsonResponse
    {
        Log::warning('OTP delivery failed', array_merge([
            'flow' => $flow,
            'reason' => $exception->reason,
        ], $exception->context));

        $message = $exception->getMessage();

        if (config('app.debug') && ! empty($exception->context['gateway_error'])) {
            $message .= ' ('.$exception->context['gateway_error'].')';
        }

        return RespondActive::clientError($message);
    }

    public function verifyCode(VerifyUserConfirmationCodeRequest $request)
    {
        $objective = (int) ($request->type ?? Constant::VERIFICATION_OBJECTIVE['Verify']);
        $code = trim((string) $request->code);

        $check = VerificationCode::findActive(
            (string) $request->phone,
            (string) $request->country_code,
            $code,
            $objective
        );

        if (! $check) {
            $reason = VerificationCode::failureReason(
                (string) $request->phone,
                (string) $request->country_code,
                $code,
                $objective
            );

            VerificationCode::logVerificationFailure(
                $reason,
                (string) $request->phone,
                (string) $request->country_code,
                $code,
                $objective,
                auth('sanctum')->id()
            );

            return RespondActive::clientError($this->verifyCodeFailureMessage($reason));
        }

        $user = $this->resolveUserForVerifyCode($request);

        if (! $user) {
            Log::warning('OTP verify: user not found after valid code', [
                'verification_id' => $check->id,
                'user_id_on_code' => $check->user_id,
                'phone_variants' => VerificationCode::phoneVariants(
                    (string) $request->phone,
                    (string) $request->country_code
                ),
            ]);

            return RespondActive::clientError(__('messages.otp_verify_user_not_found'));
        }

        if ($check->user_id && (int) $check->user_id !== (int) $user->id) {
            Log::warning('OTP verify: code belongs to another user', [
                'verification_id' => $check->id,
                'code_user_id' => $check->user_id,
                'resolved_user_id' => $user->id,
            ]);

            return RespondActive::clientError(__('messages.otp_verify_user_mismatch'));
        }

        try {
            $check->update(['used' => Constant::VERIFICATION_USED['Used']]);

            if ($objective === (int) Constant::VERIFICATION_OBJECTIVE['Reset']) {
                Log::info('OTP verify: reset code accepted', [
                    'user_id' => $user->id,
                    'verification_id' => $check->id,
                ]);

                return RespondActive::success(__('messages.otp_reset_verified'), [
                    'password_reset_allowed' => true,
                    'phone' => $user->phone,
                    'country_code' => $user->country_code,
                ]);
            }

            $user->update(['verified' => 2]);
            $user['token'] = $user->createToken('token'.$user->id)->plainTextToken;
        } catch (\Throwable $e) {
            Log::error('OTP verify: failed after valid code', [
                'user_id' => $user->id,
                'verification_id' => $check->id,
                'objective' => $objective,
                'error' => $e->getMessage(),
            ]);

            return RespondActive::clientError(__('messages.otp_verify_failed'));
        }

        Log::info('OTP verify: success', ['user_id' => $user->id, 'verification_id' => $check->id]);

        return RespondActive::success(__('messages.otp_verify_success'), new UserResource($user));
    }

    protected function resolveUserForVerifyCode(VerifyUserConfirmationCodeRequest $request): ?User
    {
        $objective = (int) ($request->type ?? Constant::VERIFICATION_OBJECTIVE['Verify']);
        $phone = (string) $request->phone;
        $countryCode = (string) $request->country_code;

        if ($objective === (int) Constant::VERIFICATION_OBJECTIVE['Reset']) {
            return User::findByPhone($phone, $countryCode);
        }

        if (auth('sanctum')->user()) {
            $user = auth('sanctum')->user();
            $user->update([
                'phone' => PhoneNumber::informationForStorage($countryCode, $phone, (string) $user->phone),
                'country_code' => $countryCode,
            ]);

            return $user->fresh();
        }

        return User::findByPhone($phone, $countryCode);
    }

    protected function verifyCodeFailureMessage(string $reason): string
    {
        return match ($reason) {
            'wrong_code' => __('messages.otp_verify_wrong_code'),
            'expired' => __('messages.otp_verify_expired'),
            'already_used' => __('messages.otp_verify_already_used'),
            'wrong_type' => __('messages.otp_verify_wrong_type'),
            'no_record' => __('messages.otp_verify_no_record'),
            default => __('messages.otp_verify_invalid'),
        };
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
        }

        $user = User::findByPhone(
            (string) $request->phone,
            $request->filled('country_code') ? (string) $request->country_code : null
        );

        if (! $user) {
            Log::warning('changePassword: user not found', [
                'phone_variants' => VerificationCode::phoneVariants(
                    (string) $request->phone,
                    $request->country_code
                ),
            ]);

            return RespondActive::clientError(__('messages.otp_reset_user_not_found'));
        }

        if (! $this->hasRecentPasswordResetVerification($user)) {
            Log::warning('changePassword: no recent reset verification', ['user_id' => $user->id]);

            return RespondActive::clientError(__('messages.otp_reset_verify_required'));
        }

        $user->password = $request->password;
        $user->save();

        Log::info('changePassword: password reset via OTP', ['user_id' => $user->id]);

        return RespondActive::success(__('Password changed successfully.'));
    }

    protected function hasRecentPasswordResetVerification(User $user): bool
    {
        $phoneVariants = VerificationCode::phoneVariants(
            (string) $user->phone,
            (string) $user->country_code
        );

        return VerificationCode::query()
            ->where('user_id', $user->id)
            ->where('objective', Constant::VERIFICATION_OBJECTIVE['Reset'])
            ->where('used', Constant::VERIFICATION_USED['Used'])
            ->where(function ($query) use ($phoneVariants) {
                $query->whereIn('information', $phoneVariants);
            })
            ->where('updated_at', '>=', now()->subMinutes(30))
            ->exists();
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