<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $apiUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send a message to a user
     */
    public function sendMessage($chatId, $text, $parseMode = 'HTML')
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => $parseMode,
            ]);

            Log::info('Telegram message sent', ['chat_id' => $chatId, 'text' => substr($text, 0, 50)]);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram send error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        try {
            $response = Http::withoutVerifying()->get("{$this->apiUrl}/getWebhookInfo");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram webhook info error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set webhook
     */
    public function setWebhook($url)
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->apiUrl}/setWebhook", [
                'url' => $url,
                'allowed_updates' => ['message', 'callback_query'],
            ]);

            Log::info('Telegram webhook set', $response->json());
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram setWebhook error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a photo to a user
     */
    public function sendPhoto($chatId, $photoUrl, $caption = '', $parseMode = 'HTML')
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->apiUrl}/sendPhoto", [
                'chat_id' => $chatId,
                'photo' => $photoUrl,
                'caption' => $caption,
                'parse_mode' => $parseMode,
            ]);

            Log::info('Telegram photo sent', ['chat_id' => $chatId, 'photo' => substr($photoUrl, 0, 50)]);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram sendPhoto error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Answer callback query (button click notification)
     */
    public function answerCallbackQuery($callbackQueryId, $text = '', $showAlert = false)
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                'callback_query_id' => $callbackQueryId,
                'text' => $text,
                'show_alert' => $showAlert,
            ]);

            Log::info('Callback query answered', ['callback_query_id' => $callbackQueryId]);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram answerCallbackQuery error: ' . $e->getMessage());
            return false;
        }
    }
}
?>