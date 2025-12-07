<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\notifications\notificationsController;
use App\Http\Controllers\Admin\withdraw\withdrawController;
use App\Http\Middleware\CheckJwtTokenByAdmin;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\User\UserController;


Route::post('admin/v1/login', [AuthController::class, 'login'])->name('admin.login');
Route::prefix('admin/v1')->middleware(CheckJwtTokenByAdmin::class)->group(function () {
    Route::apiResource('users', UserController::class)->names('user');
    Route::apiResource('withdraws', withdrawController::class)->names('withdraw');
    Route::apiResource('notifications', notificationsController::class)->names('notifications');
    Route::get('my-affiliate', [AuthController::class, 'myAffiliate']);
});


Route::prefix('v1')->group(function () {
    
});
