<?php 
use App\Http\Controllers\Common\{
    AuthController,
    OtpController,
};
use App\Http\Controllers\Events\EventController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('guest');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register')
    ->middleware('guest');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/get-otp', [OtpController::class, 'getOtp'])
        ->name('get-otp');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])
        ->name('verify-otp');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])
        ->name('resend-otp');
    Route::delete('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    Route::get('/user', [AuthController::class, 'user'])
        ->name('user');

    Route::get('/events/{id}', [EventController::class, 'show'])
        ->name('events.show');
    Route::post('/events', [EventController::class, 'store'])
        ->name('events.store');
    Route::put('/events/{id}', [EventController::class, 'update'])
        ->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])
        ->name('events.destroy');
});