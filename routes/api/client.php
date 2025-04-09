<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransferController;
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

            // Map routes
            Route::get('/map', [MapController::class, 'index'])
                ->name('map.index');

            // Check if email is valid for transfer
            Route::post('/transfers/check-email', [TransferController::class, 'checkTransferEmailValidity'])
                ->name('transfers.check-email');
            // Initiate ticket transfer
            Route::post('/transfers/ticket', [TransferController::class, 'transferTicket'])
                ->name('transfers.ticket');
            // Cancel ticket transfer
            Route::post('/transfers/cancel', [TransferController::class, 'cancelTicketTransfer'])
                ->name('transfers.cancel');

            // Friend routes
            Route::get('/friends', [FriendController::class, 'index'])
                ->name('friends.index');
            Route::get('/friends/sent-requests', [FriendController::class, 'getSentRequests'])
                ->name('friends.sent-requests');
            Route::get('/friends/received-requests', [FriendController::class, 'getReceivedRequests'])
                ->name('friends.received-requests');
            Route::post('/friends/send-request', [FriendController::class, 'sendRequest'])
                ->name('friends.send-request');
            Route::post('/friends/accept-request', [FriendController::class, 'acceptRequest'])
                ->name('friends.accept-request');
            Route::post('/friends/reject-request', [FriendController::class, 'rejectRequest'])
                ->name('friends.reject-request');
            Route::post('/friends/block-user', [FriendController::class, 'blockUser'])
                ->name('friends.block-user');
            Route::post('/friends/unblock-user', [FriendController::class, 'unblockUser'])
                ->name('friends.unblock-user');
        });
});