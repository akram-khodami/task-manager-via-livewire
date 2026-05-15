<?php

namespace App\Services\Bale;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseBaleService
{
    protected string $baseUrl = 'https://tapi.bale.ai';
    protected string $token;
    protected ?int $chatId = null;

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
     * Send message to user/group
     */
    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
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
            'parse_mode' => 'HTML'
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
            'parse_mode' => 'HTML'
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
            'parse_mode' => 'HTML'
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
            'parse_mode' => 'HTML',
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
}
