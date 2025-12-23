<?php

use App\Http\Controllers\Api\Ads\MyHistroryController;
use App\Http\Controllers\Api\Affiliate\AffiliateController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Company\CompanyCOntroller;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Notifications\NotificationsController;
use App\Http\Controllers\Api\Withdraw\WithdrawController;
use App\Http\Middleware\CheckJwtToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Admin\User\UpdateUserController;

use App\Http\Controllers\TheoremReachController;



Route::post('/theoremreach/callback-test', [TheoremReachController::class, 'debug']);



Route::prefix('v1/')->group(function () {

    Route::get('tt', function () {
        return 55;
    });
    Route::prefix('auth/')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::get('otp', [AuthController::class, 'otp']);
        Route::post('verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('verify-affiliate', [AuthController::class, 'verifyAffiliate']);
        Route::post('resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('forget-password', [AuthController::class, 'forgotPassword']);
        Route::post('rest-password', [AuthController::class, 'resetPassword']);

        Route::post('logout/{id}', [AuthController::class, 'logout']);

        Route::middleware(CheckJwtToken::class)->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('update-profile', [AuthController::class, 'updateProfile']);
        });
    });


    Route::get('ss', [UpdateUserController::class, 'addMonyForSetting']);

    Route::get('/server-ip', function () {
        return response()->json([
            'SERVER_ADDR'       => request()->server('SERVER_ADDR'),
            'gethostname'       => gethostname(),
            'gethostbyname'     => gethostbyname(gethostname()),
            'REMOTE_ADDR'       => request()->ip(),
            'X_FORWARDED_FOR'   => request()->header('X-Forwarded-For'),
        ]);
    });







    Route::get('ahmed11711', function () {
        return 55545455555555;
    });
    Route::post('seocil-login', [AuthController::class, 'socailLogin']);
    Route::get('/run-migrate', function () {
        Artisan::call('migrate', [
            // '--seed' => true, // لو عايز يشغل الـ seeders كمان
            '--force' => true, // لتأكيد التنفيذ على بيئة الإنتاج
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Migration ran successfully',
        ]);
    });
    // Route::get('/run-storage-link', function () {

    //  // لو اللينك موجود قبل كده، امسحه
    //  if (File::exists(public_path('storage'))) {
    //   File::delete(public_path('storage'));
    //  }

    //  Artisan::call('storage:link');

    //  return response()->json([
    //   'status' => true,
    //   'message' => 'Storage link created successfully',
    //  ]);
    // });

    // Route::get('/run-seeder', function () {
    //  Artisan::call('db:seed', [
    //   '--force' => true,
    //  ]);
    //  return 'Database seeding completed!';
    // });
    Route::get('settings', [CompanyCOntroller::class, 'setting']);


    Route::middleware(CheckJwtToken::class)->group(function () {
        Route::get('withdraw', [WithdrawController::class, 'index']);
        Route::get('my-balance', [AuthController::class, 'getBalance']);

        // without auth routes
        Route::get('my-affiliate', [AffiliateController::class, 'myAffiliate']);
        Route::get('notification', [NotificationsController::class, 'index']);

        Route::post('withdraw', [WithdrawController::class, 'Withdraw']);
        Route::post('add-balance', [WithdrawController::class, 'addBalance']);
        Route::get('companies', [CompanyCOntroller::class, 'index']);
        Route::post('/send-notification', [NotificationController::class, 'send']);
        Route::post('read-all-notification', [NotificationController::class, 'readAll']);
        Route::post('see-ads', [CompanyCOntroller::class, 'userAds']);
        Route::get('my-history-ads', [MyHistroryController::class, 'index']);
    });
});

require __DIR__ . '/admin.php';
