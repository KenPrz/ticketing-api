<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Message' => 'Hello Human, Welcome to Q-phoria!'];
});

Route::prefix('/admin')->group(function () {
    Route::get('/', function () {
        return view('admin.main');
    });
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('admin.login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');

    Route::middleware('is.admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');
        Route::get('/events', [AdminDashboardController::class, 'events'])
            ->name('admin.events');
        Route::get('/users', [AdminDashboardController::class, 'users'])
            ->name('admin.users');
    });
});

require __DIR__.'/auth.php';