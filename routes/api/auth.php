<?php

use App\Http\Controllers\Common\ApiKeyController;
use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\Common\OtpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Response;

// Public authentication routes
Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('guest');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register')
    ->middleware('guest');

Route::get('/auth/validate', function (Request $request) {
    $token = $request->bearerToken();
    $accessToken = PersonalAccessToken::findToken($token);

    if ($accessToken && $accessToken->tokenable) {
        return Response::json(['valid' => true], 200);
    }

    return Response::json(['valid' => false], 401);
});
    

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user/verification-status', [AuthController::class, 'verificationStatus'])
        ->name('verification-status');

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