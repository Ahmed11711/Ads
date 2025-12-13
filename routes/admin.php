<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Company\CompanyController;
use App\Http\Controllers\Admin\notifications\notificationsController;
use App\Http\Controllers\Admin\setting\settingController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Admin\User\UpdateUserController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\userWithAds\userWithAdsController;
use App\Http\Controllers\Admin\withdraw\withdrawController;
use App\Http\Controllers\heleperController;
use App\Http\Middleware\CheckJwtTokenByAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Route;


Route::get('users/', [UpdateUserController::class, 'index']);


Route::post('admin/v1/login', [AuthController::class, 'login'])->name('admin.login');
Route::prefix('admin/v1')->middleware(CheckJwtTokenByAdmin::class)->group(function () {
 Route::apiResource('users', UserController::class)->names('user')->except(['put']);

 Route::patch('users/{user}', [UserController::class, 'update']);

 Route::apiResource('withdraws', withdrawController::class)->names('withdraw');
 Route::apiResource('notifications', notificationsController::class)->names('notifications')->except('post');
 Route::post('notifications', [heleperController::class, 'notification']);
 Route::get('my-affiliate', [AuthController::class, 'myAffiliate']);
 Route::apiResource('companies', CompanyController::class)->names('company');
 Route::apiResource('settings', settingController::class)->names('setting');
 Route::apiResource('user_with_ads', userWithAdsController::class)->names('user_with_ads');
 Route::get('dashboard', [StatsController::class, 'getStats']);
 Route::get('all-emails', function () {

  return User::select(['id', 'email'])->get();
 });
});

Route::get('sssss', function () {
 return "ss";
});

Route::prefix('v1')->group(function () {});
