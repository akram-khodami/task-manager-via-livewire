<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetWebhook extends Command
{
    protected $signature = 'bale:set-webhook';
    protected $description = 'Set webhook for Bale bot';

    public function handle()
    {
        $token = config('services.blebot.token');
        $webhookUrl = 'https://akramkhodami.runflare.run/api/v1/bot/webhook';

        $response = Http::post("https://tapi.bale.ai/bot{$token}/setWebhook", [
            'url' => $webhookUrl,
        ]);

        if ($response->successful() && $response['ok']) {
            $this->info('✅ Webhook set successfully!');
            $this->info('Response: ' . json_encode($response->json()));
        } else {
            $this->error('❌ Failed to set webhook: ' . $response->body());
        }
    }
}
