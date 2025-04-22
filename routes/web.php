<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::prefix('/admin')->group(function () {
    Route::get('/', function () {
        return view('admin.main');
    })->name('admin.main');
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
        Route::get('/users/{user}', [AdminDashboardController::class, 'userDetails'])
            ->name('admin.user.details');
        Route::get('/users/{user}/edit', [AdminDashboardController::class, 'editUser'])
            ->name('admin.user.edit');
        Route::patch('/users/{user}', [AdminDashboardController::class, 'updateUser'])
            ->name('admin.user.update');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])
            ->name('admin.user.delete');
    });
});

require __DIR__.'/auth.php';