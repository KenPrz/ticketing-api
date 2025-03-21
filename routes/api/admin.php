<?php
// routes/api/admin.php - Admin-specific routes

use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin authenticated routes
Route::middleware(['auth:sanctum', 'is.admin'])->group(function () {
    // Admin dashboard and management routes
    Route::prefix('admin')->group(function () {
        // Add more admin-specific routes here
    });
});