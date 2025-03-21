<?php

use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Events\EventController;
use Illuminate\Support\Facades\Route;

Route::prefix('/clients')
    ->middleware(['auth:sanctum'])->group(function () {
        // Phone verified routes
        Route::middleware(['mobile.verified'])->group(function () {
            // Home dashboard
            Route::get('/', [HomeController::class, 'index'])
                ->name('home');
            // Client event viewing routes
            Route::get('/events', [EventController::class, 'index'])
                ->name('events.index');
            Route::get('/events/{id}', [EventController::class, 'show'])
                ->name('events.show');
            // Add more client-specific routes here
        });
});