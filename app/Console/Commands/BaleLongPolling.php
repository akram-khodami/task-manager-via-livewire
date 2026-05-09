<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BaleLongPolling extends Command
{
    protected $signature = 'bale:poll';
    protected $description = 'دریافت مداوم آپدیت‌ها از بازوی بله';

    private $token = 'YOUR_BALE_BOT_TOKEN';
    private $baseUrl;

    public function __construct()
    {
        parent::__construct();

        $this->token = config('services.blebot.token');
        $this->baseUrl = "https://tapi.bale.ai/bot{$this->token}";
    }

    public function handle()
    {
        $this->info("Start getting updates...");
        $offset = 0; // برای جلوگیری از دریافت آپدیت‌های تکراری

        while (true) { // حلقه بی‌نهایت
            // ۱. ایجاد درخواست GET به متد getUpdates
            $response = Http::get("{$this->baseUrl}/getUpdates", [
                'offset' => $offset, // شناسه آپدیت بعدی
                'timeout' => 30,     // ۳۰ ثانیه منتظر پیام جدید بمان
            ]);

            if ($response->successful() && $response['ok']) {
                $updates = $response['result'];

                foreach ($updates as $update) {
                    $this->processUpdate($update); // پردازش هر آپدیت
                    $offset = $update['update_id'] + 1; // آپدیت را تایید شده علامت‌گذاری کن
                }
            } else {
                $this->error("Error in getting update: " . $response->body());
                sleep(2); // اگر خطایی رخ داد، ۲ ثانیه صبر کن و دوباره تلاش کن
            }
        }
    }

    /**
     * یک آپدیت دریافتی را پردازش می‌کند.
     */
    private function processUpdate(array $update)
    {
        // بررسی می‌کنیم که آیا این آپدیت حاوی یک پیام متنی است
        if (isset($update['message']['text'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'];
            $firstName = $update['message']['chat']['first_name'] ?? 'User';

            $this->info("Message from {$firstName} (Chat ID: {$chatId}): {$text}");

            // منطق اصلی ربات را اینجا صدا می‌زنیم
            if ($text === '/start') {
                $this->sendMessage($chatId, "Hello {$firstName}!wellcome to Taskmanager.How can I help you?؟");
            } else {
                $this->sendMessage($chatId, "response: {$text}"); // پیام را اکو می‌کند
            }
        }
    }

     /**
     * یک پیام متنی ساده به یک گفتگو ارسال می‌کند.
     */
    private function sendMessage(int|string $chatId, string $text)
    {
        $response = Http::post("{$this->baseUrl}/sendMessage", [
           'chat_id' => $chatId,
           'text' => $text,
         ]);


        if (!$response->successful() || !$response['ok']) {
            $this->error("Error in sending message: " . $response->body());
        }
    }
}
