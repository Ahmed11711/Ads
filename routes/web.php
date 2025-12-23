<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TheoremReachController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('privacy-policy', function () {
    return view('privacyPolicy');
});

// routes/web.php أو api.php
Route::get('/theoremreach/callback-test', [TheoremReachController::class, 'debug']);
