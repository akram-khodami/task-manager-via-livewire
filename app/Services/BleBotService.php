<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BleBotService
{
    private $token;
    protected $url;

    public function __construct()
    {
        $this->token = config('services.blebot.token');
//        $this->url="https://tapi.bale.ai/bot<token>/METHOD_NAME";
        $this->url = "https://tapi.bale.ai/bot{$this->token}/sendMessage";

    }

    public function sendMessage($chatId, $text)
    {


        $response = Http::post($this->url, [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);

        return $response->json();
    }

    public function setWebhook($url)
    {
        $this->url = "https://tapi.bale.ai/bot{$this->token}/setWebhook";

        $response = Http::post($this->url, [
            'url' => $url
        ]);

        return $response->json();
    }
}
