<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Bale\BaseBaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhook from Bale
     */
    public function handle(Request $request, string $botType)
    {
        try {
            // Validate bot type exists in config
            $botConfig = config("bale.bots.{$botType}");

            if (!$botConfig) {
                Log::warning("Unknown bot type: {$botType}");
                return response()->json(['error' => 'Unknown bot type'], 400);
            }

            // Get update from request
            $update = $request->all();

            // Log incoming update for debugging
            Log::debug('Bale Webhook Update', [
                'bot_type' => $botType,
                'update' => $update
            ]);

            // Create service instance
            $serviceClass = $botConfig['service'];
            $service = new $serviceClass($botConfig['token']);

            // Process the update
            $service->processUpdate($update);

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage(), [
                'bot_type' => $botType,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Setup webhook for a bot
     */
    public function setup(string $botType)
    {
        $botConfig = config("bale.bots.{$botType}");

        if (!$botConfig) {
            return response()->json(['error' => 'Unknown bot type'], 400);
        }

        $serviceClass = $botConfig['service'];
        $service = new $serviceClass($botConfig['token']);

        $result = $service->setWebhook($botConfig['webhook_url']);

        return response()->json($result);
    }

    /**
     * Get webhook info
     */
    public function info(string $botType)
    {
        $botConfig = config("bale.bots.{$botType}");

        if (!$botConfig) {
            return response()->json(['error' => 'Unknown bot type'], 400);
        }

        $serviceClass = $botConfig['service'];
        $service = new $serviceClass($botConfig['token']);

        $result = $service->getWebhookInfo();

        return response()->json($result);
    }
}
