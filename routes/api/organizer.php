<?php

use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'mobile.verified', 'is.organizer'])->group(function () {
    // Event management routes
    Route::prefix('organizer')->group(function () {

        // Common routes for organizers
        Route::get('/', [HomeController::class, 'organizerHome'])
            ->name('organizer.home');

        Route::get('/events', [EventController::class, 'organizerEvents'])
            ->name('organizer.events.index');
        Route::get('/events/{id}', [EventController::class, 'show'])
            ->name('organizer.events.show');
        Route::get('/events/{id}/details', [EventController::class, 'eventDetails'])
            ->name('organizer.events.details');
        Route::post('/events', [EventController::class, 'store'])
            ->name('organizer.events.store');
        Route::post('/events/{id}/images', [EventController::class, 'addImages'])
            ->name('organizer.events.addImages');
        Route::put('/events/{id}', [EventController::class, 'update'])
            ->name('organizer.events.update');
        Route::put('/events/{id}/publish', [EventController::class, 'publishEvent'])
            ->name('organizer.events.publish');
        Route::put('/events/{id}/unpublish', [EventController::class, 'unpublishEvent'])
            ->name('organizer.events.unpublish');
        Route::delete('/events/{id}', [EventController::class, 'destroy'])
            ->name('organizer.events.destroy');
        Route::patch('/tickets/{id}/mark-as-used', [TicketController::class, 'markAsUsed'])
            ->name('organizer.ticket.mark-as-used');
        // Add more organizer-specific routes here
    });
});