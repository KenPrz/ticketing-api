<?php

use App\Http\Controllers\Common\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!', 'status' => 'Alive', 'Goal' => 'Make something amazing!']);
});

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('guest');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register')
    ->middleware('guest');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    Route::get('/user', [AuthController::class, 'user'])
        ->name('user');
});
