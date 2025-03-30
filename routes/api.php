<?php
// api.php - Main API routes file
use App\Http\Controllers\QrVerifyController;
use Illuminate\Support\Facades\Route;

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!', 'status' => 'Alive', 'Goal' => 'Make something amazing!']);
});

//Qr Verify
Route::get('/qr-verify', [QrVerifyController::class, 'verify'])
    ->name('qr.verify');

// Barcode Verify
Route::get('/barcode-verify', [QrVerifyController::class, 'verifyBarcode'])
    ->name('barcode.verify');

require __DIR__.'/api/auth.php';
require __DIR__.'/api/client.php';
require __DIR__.'/api/organizer.php';
require __DIR__.'/api/admin.php';