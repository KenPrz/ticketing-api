<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
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
            Route::get('/events/upcoming', [HomeController::class, 'listUpcomingEvents'])
                ->name('home.upcoming');
            Route::get('/events/nearby', [HomeController::class, 'listNearbyEvents'])
                ->name('home.nearby');
            Route::get('/events/for-you', [HomeController::class, 'listForYouEvents'])
                ->name('home.for-you');

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

            // User profile routes
            Route::get('/profile/users/home', [UserController::class, 'usersHome'])
                ->name('profile.users.home');
            Route::get('/profile/{user_id}', [UserController::class, 'getUserProfile'])
                ->name('profile.show');
            Route::post('/profile/search-user', [UserController::class, 'searchUsers'])
                ->name('profile.search');

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
            Route::post('/friends/remove-friend', [FriendController::class, 'removeFriend'])
                ->name('friends.remove-friend');
            Route::post('/friends/cancel-request', [FriendController::class, 'cancelFriendRequest'])
                ->name('friends.remove-friend');
            
            // Notification routes
            // List all notifications
            Route::get('/notifications', [NotificationController::class, 'index'])
                ->name('notifications.index');

            // Get count of unread notifications
            Route::get('/notifications/count', [NotificationController::class, 'unreadCount'])
                ->name('notifications.unread.count');

            // Mark a specific notification as read
            Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
                ->name('notifications.mark.read');

            // Mark all notifications as read
            Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
                ->name('notifications.mark.all.read');

            // Voucher management routes
            Route::get('/vouchers', [VoucherController::class, 'index'])
                ->name('organizer.vouchers.index');
            Route::get('/vouchers/{id}', [VoucherController::class, 'show'])
                ->name('organizer.vouchers.show');
            Route::get('/vouchers/check/{code}', [VoucherController::class, 'checkVoucher'])
                ->name('organizer.vouchers.check');

            // Posts routes
            Route::get('/posts', [PostController::class, 'index'])
                ->name('posts.index');
            Route::post('/posts', [PostController::class, 'store'])
                ->name('posts.store');
            Route::patch('/posts/{id}', [PostController::class, 'update'])
                ->name('posts.update');
            Route::delete('/posts/{id}', [PostController::class, 'destroy'])
                ->name('posts.destroy');
            Route::post('/posts/upvote/{id}', [PostController::class, 'upvote'])
                ->name('posts.upvote');
            Route::post('/posts/downvote/{id}', [PostController::class, 'downvote'])
                ->name('posts.downvote');
            Route::post('/posts/unvote/{id}', [PostController::class, 'unvote'])
                ->name('posts.unvote');
            Route::get('/posts/user-posts', [PostController::class, 'getUserPosts'])
                ->name('posts.user.posts');

            // Post creation routes for attachments
            Route::get('/get-tickets', [PostController::class, 'getUserTickets'])
                ->name('user.tickets');
            Route::get('/search-events', [PostController::class, 'searchEvents'])
                ->name('user.events');
        });
});