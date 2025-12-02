<?php

use App\Models\Invitation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\InvitationsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ContactsController;
use App\Http\Controllers\Admin\PackagesController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\AppSettingsController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\InvitationRequestController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Website\V1\Invitation\InvitationsController as WebsiteInvitationController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/phpinfo', function () {
//     return phpinfo();
// });

// Route::get('/extensions', function () {
//     echo '<pre>';
// print_r(get_loaded_extensions());
// echo '</pre>';
// });

// Route::get('/test-gd', function () {
//     dd(phpversion(), extension_loaded('imagick'));
// });

// Route::get('/test-imagic', function () {
//     try {
//     $image = new Imagick();
//     echo 'Imagick is working!';
// } catch (Exception $e) {
//     echo 'Error: ' . $e->getMessage();
// }
// });



// Route::get('/run-storage-link', function () {
//     Artisan::call('storage:link');
//     return "Storage link created successfully.";
// });

Route::get('/run-optimize', function () {
    Artisan::call('optimize:clear');
    // Artisan::call('optimize');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    // Artisan::call('event:cache');
    // Artisan::call('migrate');
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

    return 'Optimization completed';
    // 'composer_execution' => $composerResult

});

Route::get('/migrate', function () {
    Artisan::call('migrate');
    return "migrated successfully.";
});




Route::get('/email', function () {
    $invitation = Invitation::whereId(128)->first();

    return view('emails.new-invitation-mail', compact('invitation'));
});
// Route::resource('category', CategoryController::class);

Route::get('/invitation/{invitation_code}/{user_id}/{inserted_by?}', [WebsiteInvitationController::class, 'show'])->name('user.invitation.show');
Route::post('/invitation/{invitation_code}/{user_id}/accept', [WebsiteInvitationController::class, 'accept'])->name('user.invitation.accept');
Route::post('/invitation/{invitation_code}/{user_id}/decline', [WebsiteInvitationController::class, 'decline'])->name('user.invitation.decline');
Route::get('/delete-account-instruction', function () {
    return view('instruction');
});
Route::get('/privacy-policy', function () {
    return view('privacy_policy');
});
Route::group(['prefix' => 'admin'], function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('/', 'loginForm')->name('admin.login.form');
        Route::post('/', 'login')->name('admin.login');
    });

    Route::group(['middleware' => 'auth:admin'], function () {
        Route::controller(AuthController::class)->group(function () {

            Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
            Route::get('/logout', 'logout')->name('admin.logout');
            Route::get('/profile', 'profile')->name('admin.profile');
            Route::post('/profile', 'updateProfile')->name('admin.profile.update');
        });
        Route::controller(ContactsController::class)->group(function () {

            Route::get('/contacts', 'index')->name('contact.index');
            // show
            Route::get('/contacts/{id}', 'show')->name('contact.show');
            Route::delete('/contacts/{id}', 'destroy')->name('contact.destroy');
            Route::get('/reply', 'reply')->name('contact.reply');
            Route::post('/contacts', 'sendReply')->name('contact.reply.submit');
            Route::post('/contacts/{id}/update-status', 'updateStatus')->name('contact.update-status');
            Route::get('contacts/export/pdf', 'contactExportPdf')->name('contact.export.pdf');

        });
        Route::resource('category', CategoryController::class);
        Route::get('category/export/pdf', [CategoryController::class, 'exportPdf'])->name('category.export.pdf');
        Route::resource('notifications', NotificationsController::class);
        Route::get('notifications/export/pdf', [NotificationsController::class, 'notificationsExportPdf'])->name('notifications.export.pdf');
        Route::get('notifications/{id}/details', [NotificationsController::class, 'getDetails'])->name('notifications.details');
        Route::post('notifications/{id}/mark-as-read', [NotificationsController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::get('notifications/recent/list', [NotificationsController::class, 'getRecent'])->name('notifications.recent');
        Route::post('notifications/mark-all-read', [NotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('app-settings', [AppSettingsController::class, 'index'])->name('app-settings.index');
        Route::post('app-settings', [AppSettingsController::class, 'store'])->name('app-settings.store');
        Route::get('app-settings/{key}', [AppSettingsController::class, 'edit'])->name('app-settings.edit');
        Route::post('app-settings/{key}', [AppSettingsController::class, 'update'])->name('app-settings.update');
        Route::delete('app-settings/{key}', [AppSettingsController::class, 'destroy'])->name('app-settings.destroy');
        Route::get('app-settings/export/pdf', [AppSettingsController::class, 'exportPdf'])->name('app-settings.export.pdf');
        // Route::resource('promo-code', PromoCodeController::class);
        // Route::get('promo-code/export/pdf', [PromoCodeController::class, 'exportPdf'])->name('promo-code.export.pdf');
        Route::controller(InvitationsController::class)->group(function () {
            Route::get('/invitations/status/{invitation}', 'changeStatus')->name('invitations.change-status');
            Route::get('/invitations/get-packages-by-invitation', 'getPackagesByInvitationId')->name('invitations.getPackagesByInvitationId');
            Route::get('/invitations/packages/change', 'changePackageStatus')->name('invitations.packages.change-status');
            Route::get('/invitations/guards/{invitation}', 'guards')->name('invitation.guards');
            Route::get('/invitations/details/{id}', 'show')->name('invitations.details');
            Route::get('/invitations/export/pdf', 'invitationsExportPdf')->name('invitations.export.pdf');
        });

        Route::controller(InvitationRequestController::class)->group(function () {
            Route::get('/invitation-requests', 'index')->name('invitation-request.index');
            Route::get('/invitation-requests/export/pdf', 'invitationRequestExportPdf')->name('invitation-request.export.pdf');
        });


        Route::resource('invitation', InvitationsController::class);
        Route::resource('users', UsersController::class);
        Route::controller(UsersController::class)->group(function () {
            Route::post('users/status/{user}', 'status')->name('users.change-status');
            Route::get('/users/export/pdf', 'usersExportPdf')->name('users.export.pdf');
        });

        Route::resource('admins', AdminsController::class);
        Route::get('admins/export/pdf', [AdminsController::class, 'adminsExportPdf'])->name('admins.export.pdf');


        Route::resource('package', PackagesController::class);
        Route::get('package/export/pdf', [PackagesController::class, 'packageExportPdf'])->name('packages.export.pdf');

        Route::controller(\App\Http\Controllers\Admin\FinancialController::class)->group(function () {
            Route::get('/financial', 'index')->name('financial.index');
            Route::get('/financial/monthly-report', 'monthlyReport')->name('financial.monthly-report');
            Route::get('/financial/annual-report', 'annualReport')->name('financial.annual-report');
            Route::get('/financial/export/pdf', 'exportPdf')->name('financial.export.pdf');
            Route::get('/financial/chart-data', 'getChartData')->name('financial.chart-data');
        });

        // promo code
        Route::resource('promo-code', PromoCodeController::class);
        Route::get('promo-code/export/pdf', [PromoCodeController::class, 'exportPdf'])->name('promo-code.export.pdf');
        Route::get('promo-code/status/{promoCode}', [PromoCodeController::class, 'status'])->name('promo-code.change-status');
        Route::get('promo-code/details/{id}', [PromoCodeController::class, 'show'])->name('promo-code.details');

        // Roles and Permissions
        Route::resource('roles', RolesController::class);
        Route::resource('permissions', PermissionsController::class);
    });
});

Route::get('/debug-category-title', function () {
    $category = App\Models\Category::first();
    if (!$category) {
        return 'No categories found';
    }

    // Check current title values
    $beforeAr = $category->getTranslation('ar')->title ?? 'NULL';
    $beforeEn = $category->getTranslation('en')->title ?? 'NULL';

    // Try to update with title
    $category->update([
        'ar' => [
            'title' => 'Test Arabic Title',
            'name' => $category->getTranslation('ar')->name,
            'slug' => $category->getTranslation('ar')->slug,
            'description' => $category->getTranslation('ar')->description,
        ],
        'en' => [
            'title' => 'Test English Title',
            'name' => $category->getTranslation('en')->name,
            'slug' => $category->getTranslation('en')->slug,
            'description' => $category->getTranslation('en')->description,
        ]
    ]);

    // Reload category
    $category = $category->fresh();
    $afterAr = $category->getTranslation('ar')->title ?? 'NULL';
    $afterEn = $category->getTranslation('en')->title ?? 'NULL';

    return [
        'before_ar' => $beforeAr,
        'before_en' => $beforeEn,
        'after_ar' => $afterAr,
        'after_en' => $afterEn,
        'translated_attributes' => $category->translatedAttributes
    ];
});

// Test route to see invitation error page
Route::get('/test-invitation-error', function () {
    return view('invitation-error', ['message' => 'هذا مثال على صفحة خطأ الدعوة للاختبار']);
});
