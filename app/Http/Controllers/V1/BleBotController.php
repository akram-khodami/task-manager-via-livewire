<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\BleBotService;
use Illuminate\Http\Request;

class BleBotController extends Controller
{
    private $bot;

    public function __construct(BleBotService $bot)
    {
        $this->bot = $bot;
    }

    public function handle(Request $request)
    {
        $result = [];

        $update = $request->all();

        if (isset($update['message']['chat']['id'])) {


            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'] ?? '/start';

            $response = $this->processMessage($text);

            $res = $this->bot->sendMessage($chatId, $response);

            if ($res) {
                return response()->json($res);

            }

        }

        return response()->json(['ok' => true]);
    }

    private function processMessage($text)
    {
        return match(strtolower($text)){
        '/start' => '🌟 به بات Laravel خوش آمدید!\n/start - شروع\n/help - راهنما',
            '/help' => '📋 دستورات:\n/start - شروع\n/help - این راهنما',
            default => '❓ دستور ناشناخته!\n/help برای مشاهده دستورات'
        };
    }
}
