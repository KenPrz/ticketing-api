<?php

use App\Http\Controllers\Common\ApiKeyController;
use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\Common\OtpController;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('guest');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register')
    ->middleware('guest');

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user/verification-status', [AuthController::class, 'verificationStatus'])
        ->name('verification-status');

    Route::get('/validate', [ApiKeyController::class, 'validateApiKey']);
    // User information
    Route::delete('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    Route::get('/user', [AuthController::class, 'user'])
        ->name('user');

    // OTP verification routes
    Route::post('/get-otp', [OtpController::class, 'getOtp'])
        ->name('get-otp');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])
        ->name('verify-otp');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])
        ->name('resend-otp');
});