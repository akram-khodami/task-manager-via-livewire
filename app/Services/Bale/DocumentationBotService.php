<?php

namespace App\Services\Bale;

use Illuminate\Support\Collection;

class DocumentationBotService extends BaseBaleService
{
    private Collection $docs;

    public function __construct(string $token)
    {
        parent::__construct($token);
        $this->loadDocumentations();
    }

    /**
     * Load documentations from database or config
     */
    private function loadDocumentations(): void
    {
        // می‌تونید از دیتابیس یا فایل کانفیگ لود کنید
        $this->docs = collect([
            'laravel' => [
                'title' => 'لاراول',
                'url' => 'https://laravel.com/docs',
                'description' => 'مستندات رسمی لاراول',
                'categories' => [
                    'installation' => 'نصب و راه‌اندازی',
                    'routing' => 'مسیریابی',
                    'controllers' => 'کنترلرها',
                    'models' => 'مدل‌ها',
                    'blade' => 'موتور قالب Blade',
                ]
            ],
            'php' => [
                'title' => 'PHP',
                'url' => 'https://www.php.net/docs.php',
                'description' => 'مستندات رسمی PHP',
                'categories' => [
                    'basics' => 'مبانی',
                    'functions' => 'توابع',
                    'oop' => 'شی‌گرایی',
                    'database' => 'پایگاه داده',
                ]
            ],
            'bale' => [
                'title' => 'پیام‌رسان بله',
                'url' => 'https://dev.bale.ai',
                'description' => 'مستندات API پیام‌رسان بله',
                'categories' => [
                    'getting-started' => 'شروع به کار',
                    'bot-api' => 'Bot API',
                    'messaging' => 'پیام‌رسانی',
                    'webhooks' => 'Webhook ها',
                ]
            ],
            'vue' => [
                'title' => 'Vue.js',
                'url' => 'https://vuejs.org',
                'description' => 'مستندات رسمی Vue.js',
            ],
            'react' => [
                'title' => 'React',
                'url' => 'https://react.dev',
                'description' => 'مستندات رسمی React',
            ],
        ]);
    }

    /**
     * Process incoming updates
     */
    public function processUpdate(array $update): void
    {
        // Handle messages
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }

        // Handle callback queries (inline buttons)
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    /**
     * Handle incoming messages
     */
    private function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        // Handle commands
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text);
            return;
        }

        // Handle text search
        $this->handleTextSearch($chatId, $text);
    }

    /**
     * Handle bot commands
     */
    private function handleCommand(int $chatId, string $command): void
    {
        $command = str_replace('/', '', $command);

        switch ($command) {
            case 'start':
                $this->sendWelcomeMessage($chatId);
                break;

            case 'help':
                $this->sendHelpMessage($chatId);
                break;

            case 'list':
            case 'docs':
                $this->sendDocumentationList($chatId);
                break;

            case 'search':
                $this->sendMessage($chatId,
                    "🔍 لطفاً عبارت مورد نظر را برای جستجو ارسال کنید:\n" .
                    "مثال: `laravel`"
                );
                break;

            default:
                // Search by command
                if ($this->docs->has($command)) {
                    $this->sendDocumentDetails($chatId, $command);
                } else {
                    $this->sendMessage($chatId,
                        "❌ دستور نامعتبر است.\n" .
                        "از /help برای مشاهده راهنما استفاده کنید."
                    );
                }
        }
    }

    /**
     * Handle text search
     */
    private function handleTextSearch(int $chatId, string $searchTerm): void
    {
        if (empty(trim($searchTerm))) {
            return;
        }

        $results = $this->docs->filter(function ($doc, $key) use ($searchTerm) {
            $searchLower = mb_strtolower($searchTerm);
            return str_contains(mb_strtolower($doc['title']), $searchLower) ||
                str_contains(mb_strtolower($doc['description']), $searchLower) ||
                str_contains(mb_strtolower($key), $searchLower);
        });

        if ($results->isEmpty()) {
            $this->sendMessage($chatId,
                "❌ نتیجه‌ای برای `{$searchTerm}` یافت نشد.\n" .
                "از /list برای مشاهده لیست کامل استفاده کنید."
            );
            return;
        }

        if ($results->count() === 1) {
            $key = $results->keys()->first();
            $this->sendDocumentDetails($chatId, $key);
            return;
        }

        // Multiple results - show list with inline keyboard
        $this->sendDocumentSearchResults($chatId, $results);
    }

    /**
     * Send welcome message
     */
    private function sendWelcomeMessage(int $chatId): void
    {
        // استفاده از متدهای helper برای Markdown
        $message = "👋 به ربات مستندات خوش آمدید!\n\n";
        $message .= "🎯 *قابلیت‌های ربات:*\n";
        $message .= "• جستجوی مستندات\n";
        $message .= "• نمایش لینک‌های مفید\n";
        $message .= "• راهنمایی برنامه‌نویسان\n\n";
        $message .= "📚 *دستورات اصلی:*\n";
        $message .= "`/list` \- لیست مستندات\n";
        $message .= "`/search` \- جستجوی مستندات\n";
        $message .= "`/help` \- راهنمای کامل\n\n";
        $message .= "می‌توانید نام تکنولوژی مورد نظر را مستقیماً ارسال کنید\.";

        $keyboard = [
            [
                ['text' => '📚 لیست مستندات', 'callback_data' => 'list_docs'],
                ['text' => '🔍 جستجو', 'callback_data' => 'search_docs']
            ],
            [
                ['text' => '🆘 راهنما', 'callback_data' => 'help']
            ]
        ];

        $this->sendInlineKeyboard($chatId, $message, $keyboard);
    }

    /**
     * Send help message
     */
    private function sendHelpMessage(int $chatId): void
    {
        $message = "🆘 *راهنمای ربات مستندات*\n\n";
        $message .= "*دستورات:*\n";
        $message .= "`/start` \- شروع و خوش‌آمدگویی\n";
        $message .= "`/list` \- لیست تمام مستندات\n";
        $message .= "`/search` \- جستجو در مستندات\n";
        $message .= "`/help` \- نمایش این راهنما\n";
        $message .= "`/[نام]` \- جستجوی مستقیم\n\n";
        $message .= "*نکات:*\n";
        $message .= "• می‌توانید از دستورات در گروه‌ها هم استفاده کنید\n";
        $message .= "• برای جستجو، نام تکنولوژی را تایپ کنید\n";
        $message .= "• مثال: `laravel`، `php`، `vue`";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Send documentation list
     */
    private function sendDocumentationList(int $chatId): void
    {
        if ($this->docs->isEmpty()) {
            $this->sendMessage($chatId, "📭 هیچ مستندی موجود نیست.");
            return;
        }

        $message = "📚 *لیست مستندات موجود:*\n\n";
        $keyboard = [];
        $row = [];
        $count = 0;

        foreach ($this->docs as $key => $doc) {
            $emoji = $this->getTechEmoji($key);
            $escapedKey = $this->escapeMarkdown($key);
            $escapedTitle = $this->escapeMarkdown($doc['title']);

            $message .= "{$emoji} `/{$escapedKey}` \- {$escapedTitle}\n";

            $row[] = ['text' => "{$emoji} {$doc['title']}", 'callback_data' => "doc_{$key}"];
            $count++;

            if ($count % 2 === 0) {
                $keyboard[] = $row;
                $row = [];
            }
        }

        if (!empty($row)) {
            $keyboard[] = $row;
        }

        $message .= "\n🔍 می‌توانید روی دکمه‌های زیر کلیک کنید:";

        $this->sendInlineKeyboard($chatId, $message, $keyboard);
    }

    /**
     * Send document details
     */
    private function sendDocumentDetails(int $chatId, string $docKey): void
    {
        if (!$this->docs->has($docKey)) {
            $this->sendMessage($chatId, "❌ مستند مورد نظر یافت نشد.");
            return;
        }

        $doc = $this->docs[$docKey];
        $emoji = $this->getTechEmoji($docKey);

        // استفاده از Markdown برای لینک‌ها و فرمت
        $message = "{$emoji} *{$this->escapeMarkdown($doc['title'])}*\n\n";
        $message .= "📝 {$this->escapeMarkdown($doc['description'])}\n";
        $message .= "🔗 [لینک مستندات]({$doc['url']})\n";

        if (isset($doc['categories'])) {
            $message .= "\n📂 *دسته‌بندی‌ها:*\n";
            foreach ($doc['categories'] as $catKey => $catName) {
                $escapedCatName = $this->escapeMarkdown($catName);
                $message .= "• {$escapedCatName}\n";
            }
        }

        $keyboard = [
            [
                ['text' => '🌐 مشاهده آنلاین', 'url' => $doc['url']],
                ['text' => '📋 لیست کامل', 'callback_data' => 'list_docs']
            ],
            [
                ['text' => '🔙 بازگشت', 'callback_data' => 'back_to_list']
            ]
        ];

        $this->sendInlineKeyboard($chatId, $message, $keyboard);
    }

    /**
     * Send search results with inline keyboard
     */
    private function sendDocumentSearchResults(int $chatId, $results): void
    {
        $message = "🔍 *نتایج جستجو:*\n\n";
        $keyboard = [];

        foreach ($results as $key => $doc) {
            $emoji = $this->getTechEmoji($key);
            $escapedTitle = $this->escapeMarkdown($doc['title']);

            $message .= "{$emoji} {$escapedTitle}\n";

            $keyboard[] = [[
                'text' => "{$emoji} {$doc['title']}",
                'callback_data' => "doc_{$key}"
            ]];
        }

        $message .= "\n👆 برای مشاهده جزئیات روی دکمه مورد نظر کلیک کنید.";

        $keyboard[] = [
            ['text' => '📋 لیست کامل', 'callback_data' => 'list_docs']
        ];

        $this->sendInlineKeyboard($chatId, $message, $keyboard);
    }

    /**
     * Handle callback queries from inline buttons
     */
    private function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackData = $callbackQuery['data'];
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'] ?? null;
        $callbackId = $callbackQuery['id'];

        // Answer callback immediately
        $this->answerCallbackQuery($callbackId);

        switch (true) {
            case $callbackData === 'list_docs':
                $this->sendDocumentationList($chatId);
                break;

            case $callbackData === 'search_docs':
                $this->sendMessage($chatId,
                    "🔍 لطفاً نام تکنولوژی یا عبارت مورد نظر را ارسال کنید."
                );
                break;

            case $callbackData === 'help':
                $this->sendHelpMessage($chatId);
                break;

            case $callbackData === 'back_to_list':
                $this->sendDocumentationList($chatId);
                break;

            case str_starts_with($callbackData, 'doc_'):
                $docKey = str_replace('doc_', '', $callbackData);
                $this->sendDocumentDetails($chatId, $docKey);
                break;
        }

        // Delete the message with inline keyboard if needed
        if ($messageId && in_array($callbackData, ['list_docs', 'doc_', 'back_to_list'])) {
            // Optional: delete previous message
            // $this->deleteMessage($chatId, $messageId);
        }
    }

    /**
     * Get emoji for technology
     */
    private function getTechEmoji(string $key): string
    {
        return match($key) {
        'laravel' => '🔥',
            'php' => '🐘',
            'bale' => '💬',
            'vue' => '💚',
            'react' => '⚛️',
            default => '📘'
        };
    }

    /**
     * Handle errors
     */
    public function handleError(\Exception $e, int $chatId = null): void
    {
        Log::error('DocumentationBot Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        if ($chatId) {
            $this->sendMessage($chatId,
                "❌ متأسفانه خطایی رخ داد. لطفاً دوباره تلاش کنید."
            );
        }
    }
}
