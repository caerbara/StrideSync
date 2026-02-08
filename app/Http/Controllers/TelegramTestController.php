<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramTestController extends Controller
{
    /**
     * Simulate /start command for testing
     */
    public function testStart()
    {
        // Create a mock Telegram update for /start command
        $mockUpdate = [
            'update_id' => 123456789,
            'message' => [
                'message_id' => 1,
                'date' => now()->timestamp,
                'chat' => [
                    'id' => 987654321,
                    'first_name' => 'Test',
                    'type' => 'private'
                ],
                'from' => [
                    'id' => 987654321,
                    'is_bot' => false,
                    'first_name' => 'TestUser',
                    'username' => 'testuser'
                ],
                'text' => '/start'
            ]
        ];

        Log::info('Testing Telegram /start command', $mockUpdate);

        return $this->simulateWebhook($mockUpdate);
    }

    /**
     * Simulate name input
     */
    public function testNameInput($name = 'John Doe')
    {
        $mockUpdate = [
            'update_id' => 123456790,
            'message' => [
                'message_id' => 2,
                'date' => now()->timestamp,
                'chat' => ['id' => 987654321, 'type' => 'private'],
                'from' => [
                    'id' => 987654321,
                    'is_bot' => false,
                    'first_name' => 'TestUser',
                    'username' => 'testuser'
                ],
                'text' => $name
            ]
        ];

        Log::info('Testing Telegram name input', $mockUpdate);

        return $this->simulateWebhook($mockUpdate);
    }

    /**
     * Simulate location send
     */
    public function testLocation($latitude = 3.1357, $longitude = 101.6880)
    {
        $mockUpdate = [
            'update_id' => 123456791,
            'message' => [
                'message_id' => 3,
                'date' => now()->timestamp,
                'chat' => ['id' => 987654321, 'type' => 'private'],
                'from' => [
                    'id' => 987654321,
                    'is_bot' => false,
                    'first_name' => 'TestUser'
                ],
                'location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'horizontal_accuracy' => 20.5
                ]
            ]
        ];

        Log::info('Testing Telegram location', $mockUpdate);

        return $this->simulateWebhook($mockUpdate);
    }

    /**
     * Simulate pace input
     */
    public function testPaceInput($pace = '6:30/km')
    {
        $mockUpdate = [
            'update_id' => 123456792,
            'message' => [
                'message_id' => 4,
                'date' => now()->timestamp,
                'chat' => ['id' => 987654321, 'type' => 'private'],
                'from' => [
                    'id' => 987654321,
                    'is_bot' => false,
                    'first_name' => 'TestUser'
                ],
                'text' => $pace
            ]
        ];

        Log::info('Testing Telegram pace input', $mockUpdate);

        return $this->simulateWebhook($mockUpdate);
    }

    /**
     * Simulate gender callback
     */
    public function testGender($gender = 'male')
    {
        $mockUpdate = [
            'update_id' => 123456793,
            'callback_query' => [
                'id' => 'callback_id_123',
                'from' => [
                    'id' => 987654321,
                    'is_bot' => false,
                    'first_name' => 'TestUser'
                ],
                'message' => [
                    'message_id' => 2,
                    'date' => now()->timestamp,
                    'chat' => ['id' => 987654321, 'type' => 'private']
                ],
                'data' => 'gender_' . $gender
            ]
        ];

        Log::info('Testing Telegram gender selection', $mockUpdate);

        return $this->simulateWebhook($mockUpdate);
    }

    /**
     * Get current test user
     */
    public function getTestUser()
    {
        $user = User::where('telegram_id', 987654321)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Test user not found. Run /test/start first.'
            ]);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telegram_id' => $user->telegram_id,
                'gender' => $user->gender,
                'avg_pace' => $user->avg_pace,
                'location' => $user->location,
                'telegram_state' => $user->telegram_state,
                'strava_screenshot' => $user->strava_screenshot
            ]
        ]);
    }

    /**
     * Complete the test user profile (skip strava upload)
     */
    public function completeProfile()
    {
        $user = User::where('telegram_id', 987654321)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Test user not found. Run /test/start first.'
            ]);
        }

        $user->update([
            'strava_screenshot' => 'strava_screenshots/test_completed.jpg',
            'telegram_state' => 'profile_complete'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test user profile completed!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'telegram_state' => $user->telegram_state
            ]
        ]);
    }

    /**
     * Simulate a webhook update
     */
    private function simulateWebhook($update)
    {
        try {
            // Call the actual webhook handler
            $response = app('Illuminate\Http\Client\Factory')->post(
                route('api.telegram.webhook'),
                $update
            );

            return response()->json([
                'success' => true,
                'message' => 'Webhook simulated successfully',
                'update' => $update
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook simulation error: ' . $e->getMessage());

            // If HTTP request fails, try calling controller directly
            try {
                $controller = app(\App\Http\Controllers\TelegramWebhookController::class);
                
                $request = Request::create('/api/telegram/webhook', 'POST', $update);
                $response = $controller->handle($request);

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processed (direct call)',
                    'update' => $update
                ]);
            } catch (\Exception $e2) {
                return response()->json([
                    'error' => 'Failed to simulate webhook: ' . $e2->getMessage()
                ], 500);
            }
        }
    }
}
?>


