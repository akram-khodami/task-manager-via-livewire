<?php

use App\Http\Controllers\V1\BleBotController;
use App\Http\Controllers\V1\BotController;
use Illuminate\Support\Facades\Route;

Route::post('v1/blebot/webhook', [BleBotController::class, 'handle']);
Route::post('v1/bot/webhook', [BotController::class, 'handle']);

