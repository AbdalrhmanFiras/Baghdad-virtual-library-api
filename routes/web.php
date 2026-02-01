<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageProxyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('auth/google/redirect', [AuthController::class, 'redirect']);
// Route::get('auth/google/callback', [AuthController::class, 'callback']);
// https://abdalrhman.cupital.xyz/auth/google/redirect

Route::get('/file-proxy/{encodedPath}', ImageProxyController::class)
    ->name('file-proxy');
