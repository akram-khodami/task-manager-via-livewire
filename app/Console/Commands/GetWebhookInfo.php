<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetWebhookInfo extends Command
{
    protected $signature = 'bale:webhook-info';
    protected $description = 'Get webhook info';

    public function handle()
    {
        $token = config('services.blebot.token');

        $response = Http::get("https://tapi.bale.ai/bot{$token}/getWebhookInfo");

        if ($response->successful() && $response['ok']) {
            $info = $response['result'];
            $this->info('📡 Webhook Info:');
            $this->info('URL: ' . ($info['url'] ?? 'Not set'));
            $this->info('Pending updates: ' . ($info['pending_update_count'] ?? 0));
            $this->info('Last error: ' . ($info['last_error_message'] ?? 'None'));
        } else {
            $this->error('Failed to get webhook info');
        }
    }
}
