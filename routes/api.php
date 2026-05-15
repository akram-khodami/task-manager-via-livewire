<?php

use App\Http\Controllers\V1\BleBotController;
use App\Http\Controllers\V1\BotController;
use App\Http\Controllers\V1\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('v1/blebot/webhook', [BleBotController::class, 'handle']);
Route::post('v1/bot/webhook', [BotController::class, 'handle']);


// Webhook routes for Bale bots
Route::prefix('webhook/bale')->group(function () {
    // Main webhook endpoint
    Route::post('{botType}', [WebhookController::class, 'handle']);

    // Setup webhook (you might want to protect this in production)
    Route::get('setup/{botType}', [WebhookController::class, 'setup']);

    // Get webhook info
    Route::get('info/{botType}', [WebhookController::class, 'info']);
});
