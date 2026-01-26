<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\UserLocation;
use App\Models\RunningSession;
use App\Models\JoinedSession;

class TelegramController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        // log update for debugging
        \Log::info('TG Update Received', ['data' => $data]);

        // handle callback_query (inline button presses)
        if (isset($data['callback_query'])) {
            return $this->handleCallback($data['callback_query']);
        }

        // message handling
        $message = $data['message'] ?? $data['edited_message'] ?? null;
        if (!$message) {
            \Log::warning('No message in webhook data');
            return response('OK', 200);
        }

        $chat_id = $message['chat']['id'];
        $from_id = $message['from']['id'];
        $text = $message['text'] ?? null;

        \Log::info('Message received', ['chat_id' => $chat_id, 'from_id' => $from_id, 'text' => $text]);

        // /start
        if ($text === '/start') {
            return $this->sendMainMenu($chat_id);
        }

        // reply keyboards (Find Buddy, Check Invitation, Create Session)
        if ($text === 'Find Buddy') {
            return $this->askLocation($chat_id);
        }

        if ($text === 'Check Invitation') {
            return $this->showNearbySessions($chat_id, $from_id);
        }

        if ($text === 'Create Session') {
            // start a simple create flow state in cache for the user
            Cache::put("create_session_{$from_id}", ['step' => 'await_location', 'payload' => []], now()->addMinutes(30));
            return $this->sendMessage($chat_id, "Alright - let's create a session.\nPlease share the session location (send location or type a known location name).");
        }

        // handle location message
        if (isset($message['location'])) {
            $lat = $message['location']['latitude'];
            $lng = $message['location']['longitude'];
            UserLocation::updateOrCreate(
                ['telegram_user_id' => $from_id],
                ['lat' => $lat, 'lng' => $lng]
            );

            // also check if user is in create-session flow
            $flow = Cache::get("create_session_{$from_id}");
            if ($flow && $flow['step'] === 'await_location') {
                $flow['step'] = 'await_time';
                $flow['payload'] = array_merge($flow['payload'] ?? [], ['lat' => $lat, 'lng' => $lng, 'location_name' => $flow['location_name'] ?? null]);
                Cache::put("create_session_{$from_id}", $flow, now()->addMinutes(30));
                $this->sendMessage($chat_id, "Location saved. Now send start time (YYYY-MM-DD HH:MM)");
                return response('OK', 200);
            }

            $this->sendMessage($chat_id, "Location received. You can now use *Check Invitation* to find nearby sessions.", ['parse_mode' => 'Markdown']);
            return response('OK', 200);
        }

        // handle create session flow by simple text responses
        $flow = Cache::get("create_session_{$from_id}");
        if ($flow) {
            return $this->handleCreateFlowText($chat_id, $from_id, $text, $flow);
        }

        // fallback
        $this->sendMessage($chat_id, "I didn't understand. Use the menu.", [
            'reply_markup' => json_encode([
                'keyboard' => [["Find Buddy"], ["Check Invitation"], ["Create Session"]],
                'resize_keyboard' => true
            ])
        ]);
        return response('OK', 200);
    }

    protected function sendMainMenu($chat_id)
    {
        $this->sendMessage($chat_id, "Welcome! Choose an option:", [
            'reply_markup' => json_encode([
                'keyboard' => [["Find Buddy"], ["Check Invitation"], ["Create Session"]],
                'resize_keyboard' => true
            ])
        ]);
        return response('OK', 200);
    }

    protected function askLocation($chat_id)
    {
        // request location via keyboard button
        $reply_markup = [
            'keyboard' => [[['text' => "Share Location", 'request_location' => true]], ["Cancel"]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        $this->sendMessage($chat_id, "Please share your current location.", ['reply_markup' => json_encode($reply_markup)]);
        return response('OK', 200);
    }

    protected function showNearbySessions($chat_id, $from_id)
    {
        $user = UserLocation::where('telegram_user_id', $from_id)->first();
        if (!$user) {
            $this->sendMessage($chat_id, "I don't have your location yet. Tap *Find Buddy* and send your location first.", ['parse_mode' => 'Markdown']);
            return response('OK', 200);
        }

        // We'll use a helper to find sessions near the user (Haversine with mapping)
        $nearby = $this->getNearbySessions($user->lat, $user->lng, 10000); // radius meters

        if (empty($nearby)) {
            $this->sendMessage($chat_id, "No sessions found near you.");
            return response('OK', 200);
        }

        // send brief info with inline Join button per session
        foreach ($nearby as $s) {
            $text = "*Session at {$s->location_name}*\n" .
                "Time: " . date('Y-m-d H:i', strtotime($s->start_time)) . " to " . date('H:i', strtotime($s->end_time)) . "\n" .
                "Pace: {$s->average_pace} | Duration: {$s->duration}\n" .
                sprintf("Distance: *%.2f km* away", $s->distance_km ?? 0);

            $inline = [
                'inline_keyboard' => [
                    [
                        ['text' => "Join Session", 'callback_data' => "join_{$s->session_id}"],
                        ['text' => "Details", 'callback_data' => "detail_{$s->session_id}"]
                    ]
                ]
            ];

            $this->sendMessage($chat_id, $text, ['parse_mode' => 'Markdown', 'reply_markup' => json_encode($inline)]);
        }

        return response('OK', 200);
    }

    protected function getNearbySessions($lat, $lng, $radiusMeters = 10000)
    {
        /**
         * You have sessions with a location_name string (Park 1, Park 2...). We need lat/lng for each location.
         * Define a map here. Replace coordinates with correct ones for your locations.
         */
        $locationMap = [
            'Park 1' => ['lat' => 3.072, 'lng' => 101.487],
            'Park 2' => ['lat' => 3.068, 'lng' => 101.492],
            'Park 3' => ['lat' => 3.065, 'lng' => 101.480],
            'Padang kawad Uitm Jasin' => ['lat' => 2.365, 'lng' => 102.180],
            'Dataran Feldajaya' => ['lat' => 3.000, 'lng' => 101.500],
            // add more mappings
        ];

        $sessions = RunningSession::all();
        $result = [];
        foreach ($sessions as $s) {
            $locName = $s->location_name;
            if (!isset($locationMap[$locName])) {
                // skip sessions with unknown mapping (or you can geocode them)
                continue;
            }
            $sx = $locationMap[$locName]['lat'];
            $sy = $locationMap[$locName]['lng'];

            // calculate distance (meters)
            $distanceMeters = $this->haversineGreatCircleDistance($lat, $lng, $sx, $sy) * 1000;
            if ($distanceMeters <= $radiusMeters) {
                $s->distance_m = $distanceMeters;
                $s->distance_km = $distanceMeters / 1000;
                $result[] = $s;
            }
        }

        // sort by distance
        usort($result, function ($a, $b) {
            return $a->distance_m <=> $b->distance_m;
        });
        return $result;
    }

    // haversine (returns km)
    protected function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    protected function handleCallback($cb)
    {
        $data_id = $cb['data'];
        $from = $cb['from'];
        $chat_id = $cb['message']['chat']['id'] ?? $from['id'];
        $message_id = $cb['message']['message_id'] ?? null;

        if (str_starts_with($data_id, 'join_')) {
            $session_id = intval(substr($data_id, 5));
            // check exist
            $session = RunningSession::find($session_id);
            if (!$session) {
                $this->answerCallback($cb['id'], "Session not found.");
                return response('OK', 200);
            }
            // insert into joined_sessions (avoid duplicates)
            JoinedSession::firstOrCreate(['session_id' => $session_id, 'user_id' => $from['id']]);
            $this->answerCallback($cb['id'], "You joined the session.");
            $this->sendMessage($chat_id, "You have joined session at {$session->location_name} on " . date('Y-m-d H:i', strtotime($session->start_time)));
            return response('OK', 200);
        }

        if (str_starts_with($data_id, 'detail_')) {
            $session_id = intval(substr($data_id, 7));
            $session = RunningSession::find($session_id);
            if (!$session) {
                $this->answerCallback($cb['id'], "Session not found.");
                return response('OK', 200);
            }
            $text = "*Details*\nPlace: {$session->location_name}\nTime: " . date('Y-m-d H:i', strtotime($session->start_time)) . "\nPace: {$session->average_pace}\nDuration: {$session->duration}";
            $this->answerCallback($cb['id'], "Opening details...");
            $this->sendMessage($chat_id, $text, ['parse_mode' => 'Markdown']);
            return response('OK', 200);
        }

        $this->answerCallback($cb['id'], "Unknown action.");
        return response('OK', 200);
    }

    protected function answerCallback($callback_query_id, $text)
    {
        $url = "https://api.telegram.org/bot{$this->token}/answerCallbackQuery";
        Http::post($url, ['callback_query_id' => $callback_query_id, 'text' => $text, 'show_alert' => false]);
    }

    protected function handleCreateFlowText($chat_id, $from_id, $text, $flow)
    {
        // simple step machine
        if ($flow['step'] === 'await_location') {
            // expect location name
            $flow['payload']['location_name'] = $text;
            $flow['step'] = 'await_time';
            Cache::put("create_session_{$from_id}", $flow, now()->addMinutes(30));
            $this->sendMessage($chat_id, "Got location name: {$text}. Now send start time (YYYY-MM-DD HH:MM).");
            return response('OK', 200);
        }
        if ($flow['step'] === 'await_time') {
            // expect start_time in 'YYYY-MM-DD HH:mm' (basic)
            $flow['payload']['start_time'] = $text;
            $flow['step'] = 'await_end_time';
            Cache::put("create_session_{$from_id}", $flow, now()->addMinutes(30));
            $this->sendMessage($chat_id, "Got it. Now send end time (YYYY-MM-DD HH:MM).");
            return response('OK', 200);
        }
        if ($flow['step'] === 'await_end_time') {
            $flow['payload']['end_time'] = $text;
            $flow['step'] = 'await_pace';
            Cache::put("create_session_{$from_id}", $flow, now()->addMinutes(30));
            $this->sendMessage($chat_id, "Pace (e.g. 6:00 /km):");
            return response('OK', 200);
        }
        if ($flow['step'] === 'await_pace') {
            $flow['payload']['average_pace'] = $text;
            $flow['step'] = 'await_duration';
            Cache::put("create_session_{$from_id}", $flow, now()->addMinutes(30));
            $this->sendMessage($chat_id, "Duration (e.g. 01:00:00):");
            return response('OK', 200);
        }
        if ($flow['step'] === 'await_duration') {
            $flow['payload']['duration'] = $text;
            // save to DB
            $payload = $flow['payload'];
            $session = RunningSession::create([
                'user_id' => $from_id,
                'start_time' => $payload['start_time'],
                'end_time' => $payload['end_time'],
                'average_pace' => $payload['average_pace'],
                'duration' => $payload['duration'],
                'location_name' => $payload['location_name'] ?? 'Unknown'
            ]);
            Cache::forget("create_session_{$from_id}");
            $this->sendMessage($chat_id, "Session created.\nSession ID: {$session->session_id}");
            return response('OK', 200);
        }

        // fallthrough
        $this->sendMessage($chat_id, "In create flow. Please follow prompts.");
        return response('OK', 200);
    }

    protected function sendMessage($chat_id, $text, $options = [])
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $payload = array_merge([
            'chat_id' => $chat_id,
            'text' => $text,
        ], $options);

        Http::post($url, $payload);
    }
}
?>
