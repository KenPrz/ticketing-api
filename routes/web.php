<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Message' => 'Hello Human, Welcome to Q-phoria!'];
});

require __DIR__.'/auth.php';