<?php


namespace App\Services\Bale;

use App\Models\HafezFalPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HafezFalBotService extends BaseBaleService
{
    private string $paymentToken;
    private int $pricePerFal = 10000;//The least price is 10000 Rials

    //step1:
    public function __construct(string $token)
    {
        parent::__construct($token);

//        $this->paymentToken = config('bale.payment_token', env('BALE_PAYMENT_TOKEN'));
        $this->paymentToken = "WALLET-U96ivv2PdbJQH9r4";//Get from BotFather

        Log::info('HafezFalBotService initialized', [
            'payment_token_exists' => !empty($this->paymentToken),
            'payment_token_length' => strlen($this->paymentToken),
            'price' => $this->pricePerFal
        ]);
    }

    //step2:get upate of proccesses
    public function processUpdate(array $update): void
    {
        Log::debug('Processing update', ['update' => $update]);

        // 1. هندل پیام‌های معمولی (بدون successful_payment)
        if (isset($update['message']) && !isset($update['message']['successful_payment'])) {
            $this->handleMessage($update['message']);
        }

        // 2. هندل Pre-Checkout Query (باید در 10 ثانیه پاسخ داده بشه)
        if (isset($update['pre_checkout_query'])) {
            $this->handlePreCheckoutQuery($update['pre_checkout_query']);
        }

        // 3. هندل SuccessfulPayment (تایید نهایی پرداخت)
        if (isset($update['message']['successful_payment'])) {
            $this->handleSuccessfulPayment($update['message']);
        }

        // 4. هندل Callback Query
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    /**
     * مدیریت پیام‌های متنی
     */
    private function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        Log::info('Message received', [
            'chat_id' => $chatId,
            'text' => $text
        ]);

        if (str_starts_with($text, '/start')) {
            $this->sendWelcomeMessage($chatId);
        } elseif (str_starts_with($text, '/fal')) {
            $this->requestFalPayment($chatId);
        }
    }

    /**
     * ارسال پیام خوش‌آمدگویی و منو
     */
    private function sendWelcomeMessage(int $chatId): void
    {
        $text = "✨ به ربات فال حافظ خوش آمدید! ✨\n\n";
        $text .= "برای دریافت فال، روی دکمه زیر کلیک کنید.";

        $keyboard = [
            [
                ['text' => '🔮 دریافت فال (۱۰,۰۰۰ ریال)', 'callback_data' => 'buy_fal']
            ],
            [
                ['text' => '📜 تاریخچه فال‌های من', 'callback_data' => 'my_fals']
            ]
        ];

        $result = $this->sendInlineKeyboard($chatId, $text, $keyboard);
        Log::info('Welcome message sent', ['result' => $result]);
    }

    /**
     * شروع فرآیند پرداخت و ارسال فاکتور
     */
    private function requestFalPayment(int $chatId): void
    {
        $payload = (string)Str::uuid();

        Log::info('Starting payment request', [
            'chat_id' => $chatId,
            'payload' => $payload
        ]);

        // ذخیره تراکنش
        try {
            $transaction = HafezFalPayment::create([
                'chat_id' => $chatId,
                'payload' => $payload,
                'amount' => $this->pricePerFal,
                'status' => 'pending'
            ]);

            Log::info('Transaction saved', ['transaction_id' => $transaction->id]);
        } catch (\Exception $e) {
            Log::error('Failed to save transaction', ['error' => $e->getMessage()]);
            $this->sendMessage($chatId, "❌ خطا در سیستم. لطفاً دوباره تلاش کنید.");
            return;
        }

        // ساخت پارامترهای فاکتور
        $invoiceParams = [
            'chat_id' => $chatId,
            'title' => 'فال حافظ',
            'description' => 'دریافت یک نوبت فال کامل با تعبیر از دیوان حافظ',
            'payload' => $payload,
            'provider_token' => $this->paymentToken,
            'currency' => 'IRR',
            'prices' => json_encode([
                ['label' => 'فال کامل با تعبیر', 'amount' => $this->pricePerFal]
            ]),
            // این پارامترها مهم هستن
            'need_name' => false,
            'need_phone_number' => false,
            'need_email' => false,
            'need_shipping_address' => false,
            'is_flexible' => false,
            // می‌تونید عکس هم اضافه کنید
            // 'photo_url' => 'https://your-server.com/images/hafez.jpg',
        ];

        Log::info('Sending invoice', ['params' => $invoiceParams]);

        // ارسال فاکتور
        $result = $this->sendRequest('sendInvoice', $invoiceParams);

        Log::info('Invoice API response', ['result' => $result]);

        if (!($result['ok'] ?? false)) {
            Log::error('Failed to send invoice', [
                'result' => $result,
                'payment_token' => substr($this->paymentToken, 0, 10) . '...'
            ]);

            // پیام خطای کاربرپسند
            $errorMessage = match($result['error_code'] ?? 0){
            400 => "❌ خطا در اطلاعات پرداخت. لطفاً با پشتیبانی تماس بگیرید.",
                403 => "❌ ربات مجوز پرداخت ندارد. لطفاً تنظیمات را بررسی کنید.",
                default => "❌ خطای سیستمی. لطفاً دوباره تلاش کنید.\nکد: " . ($result['error_code'] ?? 'unknown')
            };

            $this->sendMessage($chatId, $errorMessage);
        }
    }

    /**
     * تایید قبل از کسر وجه (Pre-Checkout Query)
     */
    private function handlePreCheckoutQuery(array $preCheckoutQuery): void
    {
        Log::info('Pre-checkout query received', [
            'query_id' => $preCheckoutQuery['id'],
            'from' => $preCheckoutQuery['from']['id'] ?? 'unknown',
            'payload' => $preCheckoutQuery['invoice_payload'] ?? 'no-payload',
            'total_amount' => $preCheckoutQuery['total_amount'] ?? 0,
            'currency' => $preCheckoutQuery['currency'] ?? 'unknown'
        ]);

        $queryId = $preCheckoutQuery['id'];

        // همیشه تایید کن برای تست، ولی در تولید باید چک‌های امنیتی داشته باشی
        $result = $this->answerPreCheckoutQuery($queryId, true);

        Log::info('Pre-checkout query answered', [
            'query_id' => $queryId,
            'answer' => $result
        ]);
    }

    /**
     * پاسخ به pre_checkout_query
     */
    public function answerPreCheckoutQuery(string $preCheckoutQueryId, bool $ok, string $errorMessage = ''): array
    {
        $params = [
            'pre_checkout_query_id' => $preCheckoutQueryId,
            'ok' => $ok
        ];

        if (!$ok && !empty($errorMessage)) {
            $params['error_message'] = $errorMessage;
        }

        Log::info('Answering pre-checkout', ['params' => $params]);

        return $this->sendRequest('answerPreCheckoutQuery', $params);
    }

    /**
     * مدیریت تایید نهایی پرداخت موفق
     * این مهم‌ترین متد برای تضمین عدم از دست رفتن پول کاربر است
     */
    private function handleSuccessfulPayment(array $message): void
    {
        $chatId = $message['chat']['id'];
        $paymentInfo = $message['successful_payment'];

        // توجه: کلید payload ممکن است متفاوت باشد
        $payload = $paymentInfo['invoice_payload'] ?? $paymentInfo['payload'] ?? null;

        Log::info('🎉 SUCCESSFUL PAYMENT RECEIVED', [
            'chat_id' => $chatId,
            'payload' => $payload,
            'payment_info' => $paymentInfo,
            'message_id' => $message['message_id'] ?? null
        ]);

        if (!$payload) {
            Log::error('Payment without payload!', ['payment_info' => $paymentInfo]);
            $this->sendMessage($chatId,
                "✅ پرداخت شما موفق بود، اما در شناسایی سفارش مشکل داریم.\n" .
                "لطفاً با پشتیبانی تماس بگیرید و کد پیگیری را ارسال کنید:\n" .
                "Telegram Charge ID: " . ($paymentInfo['telegram_payment_charge_id'] ?? 'نامشخص')
            );
            return;
        }

        // پیدا کردن تراکنش
        $transaction = HafezFalPayment::where('payload', $payload)->first();

        if (!$transaction) {
            Log::error('Transaction not found for successful payment', [
                'payload' => $payload,
                'chat_id' => $chatId
            ]);

            // با این حال فال رو بده، چون پول پرداخت شده
            $this->sendMessage($chatId, "✅ پرداخت شما موفق بود!");
            $this->sendFalResult($chatId);
            return;
        }

        // جلوگیری از تکراری بودن (Idempotency)
        if ($transaction->status === 'paid') {
            Log::warning('Duplicate payment received - resending fal', [
                'transaction_id' => $transaction->id,
                'payload' => $payload
            ]);

            // فال رو دوباره بفرست، شاید کاربر پیام قبلی رو ندیده
            $this->sendFalResult($chatId);
            return;
        }

        // بروزرسانی وضعیت تراکنش
        $transaction->update([
            'status' => 'paid',
            'bale_payment_id' => $paymentInfo['provider_payment_charge_id'] ?? null,
            'paid_at' => now(),
            'payment_info' => json_encode($paymentInfo)
        ]);

        Log::info('Transaction updated to paid', [
            'transaction_id' => $transaction->id,
            'payload' => $payload
        ]);

        // ارسال تاییدیه و فال
        $this->sendMessage($chatId, "✅ پرداخت شما با موفقیت انجام شد!");
        $this->sendFalResult($chatId);

        Log::info('Fal result sent after payment', ['chat_id' => $chatId]);
    }

    /**
     * ارسال فال به کاربر
     */
    private function sendFalResult(int $chatId): void
    {
        $falText = $this->getRandomFal();

        $message = "🎯 *فال شما:*\n\n";
        $message .= "_{$falText}_\n\n";
        $message .= "📿 *تعبیر:*\n";
        $message .= $this->getInterpretation($falText);
        $message .= "\n\n✨ امیدوارم از فال خود لذت برده باشید!";

        $keyboard = [
            [
                ['text' => '🔮 فال جدید', 'callback_data' => 'buy_fal'],
                ['text' => '📜 تاریخچه', 'callback_data' => 'my_fals']
            ]
        ];

        $result = $this->sendInlineKeyboard($chatId, $message, $keyboard);

        Log::info('Fal sent', [
            'chat_id' => $chatId,
            'result' => $result
        ]);
    }

    /**
     * تفسیر فال
     */
    private function getInterpretation(string $falText): string
    {
        // می‌تونید از یه آرایه تعبیرات یا API استفاده کنید
        $interpretations = [
            'الا یا ایها الساقی' => 'نشان از شروعی تازه و دعوت به شادی و سرخوشی دارد...',
            'دوش دیدم که ملائک' => 'نشان از ارزش والای انسانی و جایگاه رفیع شما دارد...',
            'یوسف گم گشته' => 'مژده بازگشت عزیزی یا موفقیتی دور از انتظار...',
            'مژده ای دل' => 'نویدبخش روزهای خوب و تغییرات مثبت در زندگی...',
            'دلا دلی ز غم' => 'توصیه به صبر و استقامت در برابر مشکلات...'
        ];

        foreach ($interpretations as $key => $value) {
            if (str_contains($falText, mb_substr($key, 0, 10))) {
                return $value;
            }
        }

        return 'فال شما نشان از تغییرات مثبت در راه است. صبور باشید.';
    }

    /**
     * پردازش کلیک روی دکمه‌ها
     */
    private function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackId = $callbackQuery['id'];
        $chatId = $callbackQuery['from']['id'] ?? $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        Log::info('Callback received', [
            'callback_id' => $callbackId,
            'chat_id' => $chatId,
            'data' => $data
        ]);

        switch ($data) {
            case 'buy_fal':
                $this->answerCallbackQuery($callbackId, "🔄 در حال ایجاد فاکتور...");
                $this->requestFalPayment($chatId);
                break;

            case 'my_fals':
                $this->answerCallbackQuery($callbackId, "📜 در حال بارگذاری...");
                $this->showUserFals($chatId);
                break;

            default:
                $this->answerCallbackQuery($callbackId, "⚠️ عملیات نامشخص");
                Log::warning('Unknown callback data', ['data' => $data]);
        }
    }
    /**
     * نمایش تاریخچه فال‌های کاربر
     */
    private function showUserFals(int $chatId): void
    {
        $payments = HafezFalPayment::where('chat_id', $chatId)
            ->where('status', 'paid')
            ->latest()
            ->take(10)
            ->get();

        Log::info('Showing user history', [
            'chat_id' => $chatId,
            'count' => $payments->count()
        ]);

        if ($payments->isEmpty()) {
            $this->sendMessage($chatId, "📜 شما هنوز فالی دریافت نکرده‌اید!");
            return;
        }

        $message = "📜 *تاریخچه فال‌های شما:*\n\n";
        foreach ($payments as $index => $payment) {
            $date = $payment->paid_at ? $payment->paid_at->format('Y/m/d H:i') : 'نامشخص';
        $message .= ($index + 1) . ". دریافت در تاریخ: {$date}\n";
        }

        $message .= "\nتعداد کل فال‌ها: {$payments->count()}";

        $this->sendMessage($chatId, $message);
    }

    /**
 * دریافت یک فال تصادفی از دیوان حافظ
     */
    private function getRandomFal(): string
    {
        $fals = [
            "الا یا ایها الساقی ادر کأساً و ناولها\nکه عشق آسان نمود اول ولی افتاد مشکل‌ها",

            "دوش دیدم که ملائک در میخانه زدند\nگل آدم بسرشتند و به پیمانه زدند",

            "یوسف گم گشته باز آید به کنعان غم مخور\nکلبه احزان شود روزی گلستان غم مخور",

            "مژده ای دل که دگر باره بهار آمد و گشت\nگل ز خار و می لعل از دل خارا پیداست",

            "دلا دلی ز غم یار بی‌قرار مباد\nکه هست در پی آن یار بی‌قرار مرا"
        ];

        return $fals[array_rand($fals)];
    }
}
