<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    /**
     * Handle incoming Telegram messages via webhook
     */
    public function handleWebhook(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        return response('ok');
    }

    /**
     * Set webhook URL for Telegram bot
     */
    public function setWebhook()
    {
        $url = url('/api/telegram/webhook');
        $response = Telegram::setWebhook(['url' => $url]);
        
        return response()->json([
            'message' => 'Webhook set successfully',
            'url' => $url,
            'response' => $response
        ]);
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        $response = Telegram::getWebhookInfo();
        return response()->json($response);
    }

    /**
     * Send a message to a user via Telegram
     */
    public static function sendMessage($chatId, $message)
    {
        try {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send Telegram message: ' . $e->getMessage());
        }
    }

    /**
     * Send message to multiple users
     */
    public static function sendBroadcast($message)
    {
        $users = User::whereNotNull('telegram_id')->get();
        
        foreach ($users as $user) {
            self::sendMessage($user->telegram_id, $message);
        }
    }
}
?>
