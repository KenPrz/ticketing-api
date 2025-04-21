<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Message' => 'Hello Human, Welcome to Q-phoria!'];
});

Route::prefix('/admin')->group(function () {
    Route::get('/', function () {
        return view('admin.main');
    });
});

require __DIR__.'/auth.php';