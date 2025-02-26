<?php

use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Events\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Home dashboard
    Route::get('/home', [HomeController::class, 'index'])
        ->name('home');
    
    // Phone verified routes
    Route::middleware(['phone.verified'])->group(function () {
        // Client event viewing routes
        Route::get('/events', [EventController::class, 'index'])
            ->name('events.index');
        Route::get('/events/{id}', [EventController::class, 'show'])
            ->name('events.show');
        
        // Add more client-specific routes here
    });
});