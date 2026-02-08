<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BuddyMatchController extends Controller
{
    private $token;
    private $apiUrl;

    public function __construct()
    {
        $this->token = config('app.telegram_bot_token') ?? env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Get buddy matches for the authenticated user
     */
    public function index()
    {
        $user = auth()->user();

        // Find buddies with similar pace who have completed profiles
        $buddies = User::where('id', '!=', $user->id)
            ->where('telegram_state', 'profile_complete')
            ->whereNotNull('avg_pace')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'buddies' => $buddies,
            'count' => $buddies->count()
        ]);
    }

    /**
     * Send a buddy request via Telegram
     */
    public function sendBuddyRequest(Request $request, $userId)
    {
        $user = auth()->user();
        $targetUser = User::find($userId);

        if (!$targetUser || !$targetUser->telegram_id) {
            return response()->json([
                'success' => false,
                'message' => 'User not found or Telegram not linked'
            ], 404);
        }

        // Check if user has completed their profile
        if ($user->telegram_state !== 'profile_complete') {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your Telegram profile first (/profile in Telegram)'
            ], 403);
        }

        // Send message to target user via Telegram
        $message = "<b>New Buddy Request!</b>\n\n";
        $message .= "<b>" . $user->name . "</b> wants to run with you!\n\n";
        $message .= "Gender: " . ($user->gender ?? 'Not set') . "\n";
        $message .= "Pace: " . ($user->avg_pace ?? 'Not set') . "\n";
        $message .= "Email: " . $user->email . "\n";
        $message .= "Location: Nearby\n\n";
        $message .= "Reply to connect and plan your next run together!";

        if (!$this->token) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram bot token is not configured.'
            ], 500);
        }

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $targetUser->telegram_id,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            if ($response->json()['ok']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Buddy request sent to ' . $targetUser->name . ' via Telegram!'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to send request via Telegram.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get buddy matches for frontend
     */
    public function getMatches()
    {
        $user = auth()->user();
        $userCoords = $this->extractCoordinates($user->location);
        $userLocationData = $user->location ? json_decode($user->location, true) : null;
        $userState = is_array($userLocationData) ? ($userLocationData['state'] ?? null) : null;
        $userState = $userState ? strtolower(trim($userState)) : null;

        if (!$userCoords) {
            return response()->json([
                'success' => false,
                'message' => 'Please set your location in your profile to find nearby buddies.'
            ], 422);
        }
        if (!$userState) {
            return response()->json([
                'success' => false,
                'message' => 'Please set your state in your profile to find buddies in the same state.'
            ], 422);
        }

        // Only show buddies whose profiles are complete
        $buddies = User::where('id', '!=', $user->id)
            ->where('telegram_state', 'profile_complete')
            ->whereNotNull('avg_pace')
            ->whereNotNull('location')
            ->limit(10)
            ->get()
            ->map(function ($buddy) use ($userCoords) {
                $location = null;
                $distanceKm = null;
                $buddyState = null;
                if ($buddy->location) {
                    $locationData = json_decode($buddy->location, true);
                    $lat = $locationData['latitude'] ?? null;
                    $lon = $locationData['longitude'] ?? null;
                    $buddyState = $locationData['state'] ?? null;
                    $location = [
                        'latitude' => $lat,
                        'longitude' => $lon
                    ];

                    if (is_numeric($lat) && is_numeric($lon)) {
                        $distanceKm = $this->haversine(
                            $userCoords['lat'],
                            $userCoords['lon'],
                            (float) $lat,
                            (float) $lon
                        );
                    }
                }

                return [
                    'id' => $buddy->id,
                    'name' => $buddy->name,
                    'email' => $buddy->email,
                    'photo_url' => $buddy->photo_url,
                    'gender' => $buddy->gender ?? 'Not set',
                    'pace' => $buddy->avg_pace ?? 'Not set',
                    'location' => $location,
                    'distance_km' => $distanceKm,
                    'state' => $buddyState,
                    'telegram_connected' => !is_null($buddy->telegram_id),
                    'profile_complete' => $buddy->telegram_state === 'profile_complete'
                ];
            })
            ->filter(function ($buddy) use ($userState) {
                if (is_null($buddy['distance_km'])) {
                    return false;
                }

                $buddyState = $buddy['state'] ? strtolower(trim($buddy['state'])) : null;
                return $buddyState === $userState;
            })
            ->sortBy('distance_km')
            ->values();

        return response()->json([
            'success' => true,
            'buddies' => $buddies,
            'count' => $buddies->count()
        ]);
    }

    private function extractCoordinates($location): ?array
    {
        if (!$location) {
            return null;
        }

        $data = is_array($location) ? $location : json_decode($location, true);
        if (!$data || !isset($data['latitude'], $data['longitude'])) {
            return null;
        }

        if (!is_numeric($data['latitude']) || !is_numeric($data['longitude'])) {
            return null;
        }

        return [
            'lat' => (float) $data['latitude'],
            'lon' => (float) $data['longitude'],
        ];
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }
}
?>


