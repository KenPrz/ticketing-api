<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('/clients')
    ->middleware(['auth:sanctum'])->group(function () {
        // Phone verified routes
        Route::middleware(['mobile.verified'])->group(function () {
            Route::post('/location-update', [HomeController::class, 'updateLocation'])
                ->name('location.update');
            // Home dashboard
            Route::get('/', [HomeController::class, 'index'])
                ->name('home');
            // Client event viewing routes
            Route::get('/events', [EventController::class, 'index'])
                ->name('events.index');
            Route::get('/events/{id}', [EventController::class, 'show'])
                ->name('events.show');
            // Add more client-specific routes here

            // Bookmark routes
            Route::get('/bookmarks', [BookmarkController::class, 'index'])
                ->name('bookmarks.index');
            Route::post('/bookmarks', [BookmarkController::class, 'store'])
                ->name('bookmarks.store');
            Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy'])
                ->name('bookmarks.destroy');

            // Ticket routes for clients
            Route::get('/tickets', [TicketController::class, 'index'])
                ->name('tickets.index');
            Route::get('/tickets/{id}', [TicketController::class, 'show'])
                ->name('tickets.show');
            Route::get('/tickets/{id}/download', [TicketController::class, 'download'])
                ->name('tickets.download');
            Route::post('/tickets/{id}/transfer', [TicketController::class, 'transfer'])
                ->name('tickets.transfer');

            // Purchase routes
            Route::get('/purchases', [PurchaseController::class, 'index'])
                ->name('purchases.index');
            Route::get('/purchases/{id}', [PurchaseController::class, 'show'])
                ->name('purchases.show');
            Route::post('/purchases', [PurchaseController::class, 'store'])
                ->name('purchases.store');
            Route::get('/purchases/show-tickets/{event_ticket_tier_id}', [PurchaseController::class, 'showPurchaseScreen'])
                ->name('purchase.ticket');
        });
});