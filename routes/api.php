<?php
// api.php - Main API routes file
use Illuminate\Support\Facades\Route;

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!', 'status' => 'Alive', 'Goal' => 'Make something amazing!']);
});

require __DIR__.'/api/auth.php';
require __DIR__.'/api/client.php';
require __DIR__.'/api/organizer.php';
require __DIR__.'/api/admin.php';