<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Telegram Bot Routes
|--------------------------------------------------------------------------
*/

// Webhook endpoint (POST from Telegram)
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('api.telegram.webhook');

// Set webhook (GET - call this once)
Route::get('/telegram/set-webhook', [TelegramWebhookController::class, 'setWebhook'])
    ->name('api.telegram.set-webhook');

// Get webhook info (GET - check status)
Route::get('/telegram/webhook-info', [TelegramWebhookController::class, 'getWebhookInfo'])
    ->name('api.telegram.webhook-info');
?>


