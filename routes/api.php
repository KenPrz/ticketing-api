<?php

use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\Common\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!', 'status' => 'Alive', 'Goal' => 'Make something amazing!']);
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])
        ->name('home');
});

require __DIR__.'/auth-api.php';
