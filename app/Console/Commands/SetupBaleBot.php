<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Bale\DocumentationBotService;

class SetupBaleBot extends Command
{
    protected $signature = 'bale:setup {type}';
    protected $description = 'Setup Bale bot webhook';

    public function handle()
    {
        $type = $this->argument('type');
        $config = config("bale.bots.{$type}");

        if (!$config) {
            $this->error("Bot type '{$type}' not found in config!");
            return 1;
        }

        $this->info("Setting up webhook for {$config['name']}...");

        $serviceClass = $config['service'];
        $service = new $serviceClass($config['token']);

        $result = $service->setWebhook($config['webhook_url']);

        if ($result['ok'] ?? false) {
            $this->info("✅ Webhook set successfully!");
            $this->info("URL: {$config['webhook_url']}");
        } else {
            $this->error("❌ Failed to set webhook: " . ($result['description'] ?? 'Unknown error'));
        }

        // Get bot info
        $botInfo = $service->getMe();
        if ($botInfo['ok'] ?? false) {
            $this->info("Bot Info: " . json_encode($botInfo['result'], JSON_UNESCAPED_UNICODE));
        }

        return 0;
    }
}
