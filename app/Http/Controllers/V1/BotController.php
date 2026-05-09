<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    private $token;
    private $baseUrl;

    public function __construct()
    {
        $this->token = config('services.blebot.token');
        $this->baseUrl = "https://tapi.bale.ai/bot{$this->token}";
    }

    public function handle(Request $request)
    {
        Log::info('===== WEBHOOK RECEIVED =====');
        Log::info('Full request data:', $request->all());
        Log::info('Request IP: ' . $request->ip());

        try {
            $update = $request->all();

            // بررسی وجود پیام
            if (isset($update['message'])) {
                $message = $update['message'];
                $chatId = $message['chat']['id'] ?? null;
                $text = $message['text'] ?? null;
                $firstName = $message['chat']['first_name'] ?? 'User';

                Log::info("📨 Message from {$firstName} (Chat ID: {$chatId}): {$text}");

                if ($text === '/start') {
                    $reply = "سلام {$firstName}! به ربات تسک منیجر خوش آمدی.\n\n";
                    $reply .= "📋 دستورات موجود:\n";
                    $reply .= "/tasks - مشاهده تسک‌های شما\n";
                    $reply .= "/help - راهنما";

                    $this->sendMessage($chatId, $reply);
                }
                elseif ($text === '/tasks') {
                    $this->sendMessage($chatId, "در حال دریافت تسک‌های شما...");
                    // کد تسک‌ها رو بعدا اضافه میکنیم
                }
                else {
                    $this->sendMessage($chatId, "دستور نامعتبر. لطفاً از /start استفاده کنید.");
                }
            }
            else {
                Log::warning('Webhook received but no message found', $update);
            }

        } catch (\Exception $e) {
            Log::error('❌ Bot Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        // همیشه باید پاسخ 200 برگردونه
        return response()->json(['status' => 'ok']);
    }

    private function sendMessage($chatId, $text)
    {
        Log::info("Sending message to {$chatId}: {$text}");

        $response = Http::post("{$this->baseUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);

        if (!$response->successful()) {
            Log::error('Failed to send message: ' . $response->body());
        } else {
            Log::info('Message sent successfully');
        }
    }
}
