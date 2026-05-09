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

        //$this->url="https://tapi.bale.ai/bot<token>/METHOD_NAME";
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

    //===get Updates:

    //method1:web hook
    public function setWebhook(String $url = '')
    {
        //خروجی این متد در صورت اجرای موفقیت‌آمیز True است.

        $this->url = "https://tapi.bale.ai/bot{$this->token}/setWebhook";

        $response = Http::post($this->url, [
            'url' => $url
        ]);

        return $response->json();
    }

    //در صورتی که تصمیم دارید مجدداً از متد getUpdates استفاده کنید، از این متد برای غیرفعال‌سازی اتصال وبهوک استفاده کنید. در صورت موفقیت، مقدار True بازگردانده می‌شود
    public function deleteWebhook()
    {
        $this->url = "https://tapi.bale.ai/bot{$this->token}/deleteWebhook";

    }

    //از این متد برای دریافت وضعیت فعلی وب‌هوک استفاده کنید. این متد به هیچ پارامتری نیاز ندارد. در صورت اجرای موفقیت آمیز، یک شی WebhookInfo برگردانده می‌شود. اگر بازو در حال استفاده از متد getUpdates است، خروجی یک شی خواهد بود که فیلد url آن خالی است.
    public function getWebhookInfo()
    {
        $this->url = "https://tapi.bale.ai/bot{$this->token}/getWebhookInfo";

    }

    public function WebhookInfo()
    {

    }

    //method2:long polling with getUpdates
    public function getUpdates()
    {
        $this->url = "https://tapi.bale.ai/bot{$this->token}/getUpdates";//long polling

    }


}
