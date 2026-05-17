<?php


namespace App\Services\Bale;

use App\Models\HafezFalPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HafezFalBotService extends BaseBaleService
{
    private string $paymentToken = "WALLET-TEST-1111111111111111"; // توکن پرداخت از کیف پول بله
    private int $pricePerFal = 5000; // قیمت هر فال به واحد پول خرد (مثلاً 5000 ریال)

    /**
     * پردازش آپدیت‌های دریافتی (قلب ربات)
     */
    public function processUpdate(array $update): void
    {
        // 1. هندل پیام‌های معمولی
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }

        // 2. هندل Pre-Checkout Query (یک مرحله تایید قبل از کسر وجه)
        if (isset($update['pre_checkout_query'])) {
            $this->handlePreCheckoutQuery($update['pre_checkout_query']);
        }

        // 3. هندل SuccessfulPayment (مهم‌ترین بخش - تایید نهایی پرداخت)
        if (isset($update['message']['successful_payment'])) {
            $this->handleSuccessfulPayment($update['message']);
        }

        // 4. هندل Callback Query های معمولی (دکمه‌ها)
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
                ['text' => '🔮 دریافت فال (۵۰۰۰ ریال)', 'callback_data' => 'buy_fal']
            ],
            [
                ['text' => '📜 تعبیر فال قبلی', 'callback_data' => 'my_fals']
            ]
        ];

        $this->sendInlineKeyboard($chatId, $text, $keyboard);
    }

    /**
     * شروع فرآیند پرداخت و ارسال فاکتور
     */
    private function requestFalPayment(int $chatId): void
    {
        $payload = (string)Str::uuid(); // شناسه یکتای تراکنش

        // ذخیره تراکنش در دیتابیس با وضعیت pending
        HafezFalPayment::create([
            'chat_id' => $chatId,
            'payload' => $payload,
            'amount' => $this->pricePerFal,
            'status' => 'pending'
        ]);

        $invoiceData = [
            'title' => 'فال حافظ',
            'description' => 'دریافت یک نوبت فال کامل با تعبیر',
            'payload' => $payload,
            'provider_token' => $this->paymentToken,
            'prices' => [
                ['label' => 'فال کامل', 'amount' => $this->pricePerFal]
            ],
            'photo_url' => 'https://yourdomain.com/images/hafez.jpg' // یک عکس برای فاکتور
        ];

        $result = $this->sendInvoice($chatId, $invoiceData);

        if (!$result['ok']) {
            Log::error("Failed to send invoice", ['result' => $result]);
            $this->sendMessage($chatId, "❌ متاسفانه در ایجاد فاکتور مشکلی پیش آمد. لطفاً دوباره تلاش کنید.");
        }
    }

    /**
     * تایید قبل از کسر وجه (Pre-Checkout Query)
     * این متد باید خیلی سریع (در کمتر از 10 ثانیه) بله را جواب دهد.
     */
    private function handlePreCheckoutQuery(array $preCheckoutQuery): void
    {
        $queryId = $preCheckoutQuery['id'];

        // می‌توانید اعتبار تراکنش را اینجا چک کنید
        // مثلاً آیا مبلغ درست است؟ آیا این محصول موجود است؟

        $this->answerPreCheckoutQuery($queryId, true); // true یعنی پرداخت تایید شود
        Log::info("Pre-checkout query answered for query ID: {$queryId}");
    }

    /**
     * مدیریت تایید نهایی پرداخت موفق (SuccessfulPayment)
     * این جایی است که "پول کاربر از دست نمی‌رود"
     */
    private function handleSuccessfulPayment(array $message): void
    {
        $chatId = $message['chat']['id'];
        $paymentInfo = $message['successful_payment'];
        $payload = $paymentInfo['payload'];

        // 1. پیدا کردن تراکنش بر اساس payload
        $transaction = HafezFalPayment::where('payload', $payload)->first();

        if (!$transaction) {
            Log::error("Payment received but transaction not found", ['payload' => $payload]);
            $this->sendMessage($chatId, "❌ خطا در شناسایی تراکنش. لطفاً با پشتیبانی تماس بگیرید.");
            return;
        }

        // 2. جلوگیری از تکراری بودن (Idempotency)
        if ($transaction->status === 'paid') {
            Log::warning("Duplicate successful payment received", ['payload' => $payload]);
            // ما سرویس رو دوباره ارسال می‌کنیم، چون شاید کاربر پیام قبلی رو گم کرده
            $this->sendFalResult($chatId);
            return;
        }

        // 3. تایید تراکنش و ارائه سرویس
        $transaction->update([
            'status' => 'paid',
            'bale_payment_id' => $paymentInfo['provider_payment_charge_id'] ?? null,
            'paid_at' => now()
        ]);

        // 4. ارسال فال برای کاربر
        $this->sendFalResult($chatId);
    }

    /**
     * ارسال نتیجه فال به کاربر (بعد از پرداخت موفق)
     */
    private function sendFalResult(int $chatId): void
    {
        $falText = $this->getRandomFal(); // متد فرضی برای انتخاب یک فال تصادفی از دیتابیس

        $message = "🔮 *فال شما:*\n\n";
        $message .= "{$falText}\n\n";
        $message .= "✨ امیدوارم از فال خود لذت برده باشید!";

        $this->sendMessage($chatId, $message);
        Log::info("Fal result sent to user", ['chat_id' => $chatId]);
    }

    /**
     * پردازش کلیک روی دکمه‌ها
     */
    private function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackId = $callbackQuery['id'];
        $chatId = $callbackQuery['from']['id'];
        $data = $callbackQuery['data'];

        if ($data === 'buy_fal') {
            $this->requestFalPayment($chatId);
            $this->answerCallbackQuery($callbackId, "در حال انتقال به درگاه پرداخت...");
        }
        // ... سایر callback ها
    }

    /**
     * یک متد فرضی برای دریافت فال تصادفی
     */
    private function getRandomFal(): string
    {
        $fals = [
            "دوش دیدم که ملایک در میخانه زدند\nگل آدم بسرشتند و به پیمانه زدند...",
            "یوسف گم گشته باز آید به کنعان غم مخور\nکلبه احزان شود روزی گلستان غم مخور..."
        ];
        return $fals[array_rand($fals)];
    }
}
