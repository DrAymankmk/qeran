<?php

use App\Helpers\Constant;
use App\Http\Controllers\Api\V1\AppSettings\AppSettings;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Home\HomeController;
use App\Http\Controllers\Api\V1\Invitation\InvitationsController;
use App\Http\Controllers\Api\V1\Notification\NotificationController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use App\Http\Controllers\Api\V1\Packages\PackageController;
use App\Http\Controllers\Api\V1\Settings\GetSettings;
use App\Http\Controllers\Api\V1\Settings\SetContactUs;
use App\Http\Controllers\Webhook\WhatsAppController;
use App\Services\External\Notification;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('command', function () {
    return 'Optimization completed';
});

Route::get('/run-optimize', function () {
    Artisan::call('optimize:clear');
    // Artisan::call('optimize');
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('view:cache');
    Artisan::call('event:cache');
    Artisan::call('migrate');
    // Artisan::call('db:seed');

    // Execute composer require twilio/sdk with output capture
    // $output = [];
    // $returnCode = 0;
    // exec('composer require twilio/sdk 2>&1', $output, $returnCode);

    // $composerResult = [
    //     'command' => 'composer require twilio/sdk',
    //     'return_code' => $returnCode,
    //     'output' => implode("\n", $output),
    //     'success' => $returnCode === 0
    // ];

    return 'Optimization clear';
    // 'composer_execution' => $composerResult
});

Route::prefix('/v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('login-guard', [AuthController::class, 'loginGuard']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('change_password', [AuthController::class, 'changePassword']);
    Route::post('verify', [AuthController::class, 'verifyCode']);
    Route::post('send_code', [AuthController::class, 'sendCode']);

    Route::get('home', [HomeController::class, '__invoke']);
    Route::get('settings', GetSettings::class);
    Route::post('contact-us', SetContactUs::class);
    Route::get('app-settings', [AppSettings::class, 'index']);
    Route::get('app-settings/show', [AppSettings::class, 'show']);
Route::get('app-settings/by-category', [AppSettings::class, 'byCategory']);
    Route::post('whatsapp-webhook', [WhatsAppController::class, 'handle']);

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('store', 'store');
        Route::post('update/{category_id}', 'update');
    });
    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('', 'index');
        Route::put('read/{notification}', 'read');
        Route::put('read-all', 'readAll');
        Route::get('delete/{notification}', 'destroy');
        Route::post('pusher/auth', 'pusherAuth');
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/send-notify', function () {
            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Invitations'],
                [auth()->id()],
                181,
                'Modern Invitation',
                __('This is a test notify body!'));

            return 'done';
        });

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/delete', [AuthController::class, 'delete']);
        Route::get('/generate-beams-token', [AuthController::class, 'generateAuthToken']);
        Route::post('auth/change-language', [AuthController::class, 'changeLanguage']);

        Route::prefix('profile')->controller(ProfileController::class)->group(function () {
            Route::get('', 'show');
            Route::post('update', 'update');
        });

//             Route::get('/packages/{invitation}', 'packages');
	Route::get('/packages/{invitation}',[PackageController::class ,'invitationPackages'])->name('packages');

        Route::prefix('invitations')->controller(InvitationsController::class)->group(function () {
            Route::get('', 'index');
            Route::post('store', 'store');
            Route::get('/{invitation}', 'show');
            Route::get('show-by-id/{id}', 'showById');
            Route::post('update/{invitation}', 'update');
            Route::post('add-admin/{invitation}', 'addAdmin');
            Route::post('add-guard/{invitation}', 'addGuard');
            Route::post('add-user/{invitation}', 'addUser');
            Route::post('edit-user/{user}', 'editUser');
            Route::post('/{invitation}/update-admin-invitation-count/{admin}', 'updateAdminInvitationCount');
            Route::post('send-notification/{invitation}', 'sendNotificationToUser');
            Route::post('update-admin-host-name/{invitation}', 'updateAdminHostName');
            Route::post('send-sms/{invitation}', 'sendSMSToUser');
            Route::post('send-template-message/{invitation}', 'sendTemplateMessage');
            Route::get('/share-sms-invitation-app/{invitation}/{user}', 'shareSmsInvitationApp');
            Route::get('/invited/users/{invitation}', 'users');
            Route::get('/invited/admins/{invitation}', 'admins');
            Route::get('/invited/guards/{invitation}', 'guards');
            Route::post('/status/{invitation}', 'updateStatus');
            Route::post('/user/delete/{invitation}', 'removeUser');
            Route::get('/check/invitation', 'checkInvitation');
            Route::get('/share/{invitation}', 'shareInvitation');
            Route::get('/share-sms/{invitation}', 'shareInvitationSms');
            Route::post('payment/receipt/{invitation}', 'PaymentReceipt');

            Route::post('/add-extra-package/{invitation}', 'addExtraPackages');

            Route::get('/complete-request-invitation/{invitation}', 'completeRequestInvitation');
        });
    });
});