<?php

namespace App\Services\Bale;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseBaleService
{
    protected string $baseUrl = 'https://tapi.bale.ai';
    protected string $token;
    protected ?int $chatId = null;

    protected string $parseMode = 'Markdown';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Set chat ID for the current session
     */
    public function setChatId(?int $chatId): self
    {
        $this->chatId = $chatId;
        return $this;
    }

    /**
     * Set parse mode
     */
    public function setParseMode(string $mode): self
    {
        $this->parseMode = $mode;
        return $this;
    }

    /**
     * Send request to Bale API
     */
    protected function sendRequest(string $method, array $params = []): array
    {
        $url = "{$this->baseUrl}/bot{$this->token}/{$method}";

        try {
            $response = Http::timeout(30)
                ->post($url, $params);

            if ($response->successful()) {
                $result = $response->json();

                if (($result['ok'] ?? false) === false) {
                    Log::error('Bale API Error', [
                        'method' => $method,
                        'error' => $result['description'] ?? 'Unknown error',
                        'params' => $params
                    ]);
                }

                return $result;
            }

            Log::error('Bale HTTP Error', [
                'method' => $method,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['ok' => false, 'description' => 'HTTP request failed'];

        } catch (\Exception $e) {
            Log::error('Bale Request Exception', [
                'method' => $method,
                'error' => $e->getMessage()
            ]);

            return ['ok' => false, 'description' => $e->getMessage()];
        }
    }

    /**
     * Escape Markdown special characters
     */
    protected function escapeMarkdown(string $text): string
    {
        $chars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        $escaped = [];

        foreach ($chars as $char) {
            $escaped[] = '\\' . $char;
        }

        return str_replace($chars, $escaped, $text);
    }

    /**
     * Format bold text in Markdown
     */
    protected function bold(string $text): string
    {
        return '*' . $text . '*';
    }

    /**
     * Format italic text in Markdown
     */
    protected function italic(string $text): string
    {
        return '_' . $text . '_';
    }

    /**
     * Format inline code
     */
    protected function code(string $text): string
    {
        return '`' . $text . '`';
    }

    /**
     * Format code block
     */
    protected function codeBlock(string $text, string $language = ''): string
    {
        return "```{$language}\n{$text}\n```";
    }

    /**
     * Format link in Markdown
     */
    protected function link(string $text, string $url): string
    {
        return '[' . $text . '](' . $url . ')';
    }

    /**
     * Send message to user/group
     */
    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $this->parseMode
        ], $options);

        return $this->sendRequest('sendMessage', $params);
    }

/**
 * Send photo
 */
public function sendPhoto(int|string $chatId, string $photo, string $caption = '', array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => $this->parseMode
        ], $options);

        return $this->sendRequest('sendPhoto', $params);
    }

    /**
     * Send document
     */
    public function sendDocument(int|string $chatId, string $document, string $caption = '', array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'document' => $document,
            'caption' => $caption,
            'parse_mode' => $this->parseMode
        ], $options);

        return $this->sendRequest('sendDocument', $params);
    }

    /**
     * Edit message
     */
    public function editMessageText(int|string $chatId, int $messageId, string $text, array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $this->parseMode
        ], $options);

        return $this->sendRequest('editMessageText', $params);
    }

    /**
     * Delete message
     */
    public function deleteMessage(int|string $chatId, int $messageId): array
    {
        return $this->sendRequest('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId
        ]);
    }

    /**
     * Send inline keyboard
     */
    public function sendInlineKeyboard(int|string $chatId, string $text, array $keyboard, array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $this->parseMode,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ], $options);

        return $this->sendRequest('sendMessage', $params);
    }

    /**
     * Answer callback query
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): array
{
    return $this->sendRequest('answerCallbackQuery', [
        'callback_query_id' => $callbackQueryId,
        'text' => $text,
        'show_alert' => $showAlert
    ]);
}

    /**
     * Get file info
     */
    public function getFile(string $fileId): array
{
    return $this->sendRequest('getFile', [
        'file_id' => $fileId
    ]);
}

    /**
     * Set webhook
     */
    public function setWebhook(string $url): array
{
    return $this->sendRequest('setWebhook', [
        'url' => $url
    ]);
}

    /**
     * Delete webhook
     */
    public function deleteWebhook(): array
{
    return $this->sendRequest('deleteWebhook');
}

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): array
{
    return $this->sendRequest('getWebhookInfo');
}

    /**
     * Get bot info
     */
    public function getMe(): array
{
    return $this->sendRequest('getMe');
}

    /**
     * Process incoming update (abstract - each bot implements differently)
     */
    abstract public function processUpdate(array $update): void;


/**
 * ارسال فاکتور پرداخت به یک کاربر
 */
public function sendInvoice(int|string $chatId, array $invoiceData): array
{
    $params = array_merge(['chat_id' => $chatId], $invoiceData);

    // اطمینان از اینکه قیمت‌ها به فرمت صحیح هستند
    // Prices should be an array of ['label' => '...', 'amount' => 1000]
    if (isset($params['prices'])) {
        $params['prices'] = json_encode($params['prices']);
    }

    // پردازش reply_markup اگر وجود داشته باشد
    if (isset($params['reply_markup'])) {
        $params['reply_markup'] = json_encode($params['reply_markup']);
    }

    return $this->sendRequest('sendInvoice', $params);
}

/**
 * پاسخ به یک درخواست پرداخت موفق (pre_checkout_query)
 * این متد برای تایید یا رد یک پرداخت قبل از کسر وجه استفاده می‌شود.
 */
public function answerPreCheckoutQuery(string $preCheckoutQueryId, bool $ok, string $errorMessage = ''): array
{
    $params = [
        'pre_checkout_query_id' => $preCheckoutQueryId,
        'ok' => $ok
    ];
    if (!$ok) {
        $params['error_message'] = $errorMessage;
    }
    return $this->sendRequest('answerPreCheckoutQuery', $params);
}
}
