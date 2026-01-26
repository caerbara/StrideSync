<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramAdminController extends Controller
{
    /**
     * Telegram bot token
     */
    private $token;
    private $apiUrl;

    public function __construct()
    {
        $this->token = config('app.telegram_bot_token') ?? env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Show Telegram settings page
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'telegram_linked' => User::whereNotNull('telegram_id')->count(),
            'telegram_percentage' => User::count() > 0 
                ? round((User::whereNotNull('telegram_id')->count() / User::count()) * 100, 2)
                : 0
        ];

        $webhookInfo = $this->getWebhookStatus();
        $botInfo = $this->getBotInfo();

        return view('admin.telegram.index', [
            'stats' => $stats,
            'webhookInfo' => $webhookInfo,
            'botInfo' => $botInfo
        ]);
    }

    /**
     * Show user reports from the Telegram bot.
     */
    public function reports()
    {
        $reports = UserReport::with(['reporter', 'target'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.telegram.reports', [
            'reports' => $reports,
        ]);
    }

    /**
     * Get webhook status
     */
    private function getWebhookStatus()
    {
        try {
            $response = Http::withoutVerifying()->get("{$this->apiUrl}/getWebhookInfo");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to get webhook info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get bot info
     */
    private function getBotInfo()
    {
        try {
            $response = Http::withoutVerifying()->get("{$this->apiUrl}/getMe");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to get bot info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Set webhook
     */
    public function setWebhook(Request $request)
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $webhookUrl = $baseUrl . '/api/telegram/webhook';

        try {
            $response = Http::withoutVerifying()->get("{$this->apiUrl}/setWebhook", [
                'url' => $webhookUrl,
            ]);

            $result = $response->json();

            if ($result['ok']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook set successfully!',
                    'url' => $webhookUrl
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['description'] ?? 'Failed to set webhook'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove webhook
     */
    public function removeWebhook(Request $request)
    {
        try {
            $response = Http::withoutVerifying()->get("{$this->apiUrl}/deleteWebhook");
            $result = $response->json();

            if ($result['ok']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook removed successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['description'] ?? 'Failed to remove webhook'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Broadcast message to all users
     */
    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:1|max:4096'
        ]);

        $message = $request->input('message');
        $users = User::whereNotNull('telegram_id')->get();

        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                    'chat_id' => $user->telegram_id,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);
                $sent++;
            } catch (\Exception $e) {
                Log::error("Failed to send message to user {$user->id}: " . $e->getMessage());
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Message sent to {$sent} users" . ($failed > 0 ? ", {$failed} failed" : '')
        ]);
    }

    /**
     * Send message to specific user
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|min:1|max:4096'
        ]);

        $user = User::find($request->input('user_id'));

        if (!$user->telegram_id) {
            return response()->json([
                'success' => false,
                'message' => "User doesn't have Telegram linked"
            ]);
        }

        try {
            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $user->telegram_id,
                'text' => $request->input('message'),
                'parse_mode' => 'HTML'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Message sent to {$user->name}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update bot description
     */
    public function updateDescription(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:512'
        ]);

        try {
            Http::withoutVerifying()->post("{$this->apiUrl}/setMyDescription", [
                'description' => $request->input('description')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bot description updated!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update bot short description
     */
    public function updateShortDescription(Request $request)
    {
        $request->validate([
            'short_description' => 'required|string|max:120'
        ]);

        try {
            Http::withoutVerifying()->post("{$this->apiUrl}/setMyShortDescription", [
                'short_description' => $request->input('short_description')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bot short description updated!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
?>
