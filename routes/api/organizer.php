<?php

use App\Http\Controllers\Events\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'mobile.verified', 'is.organizer'])->group(function () {
    // Event management routes
    Route::prefix('organizer')->group(function () {
        Route::get('/events', [EventController::class, 'organizerEvents'])
            ->name('organizer.events.index');
        Route::get('/events/{id}', [EventController::class, 'show'])
            ->name('organizer.events.show');
        Route::post('/events', [EventController::class, 'store'])
            ->name('organizer.events.store');
        Route::put('/events/{id}', [EventController::class, 'update'])
            ->name('organizer.events.update');
        Route::delete('/events/{id}', [EventController::class, 'destroy'])
            ->name('organizer.events.destroy');

        // Add more organizer-specific routes here
    });
});