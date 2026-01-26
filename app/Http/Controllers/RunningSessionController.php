<?php

namespace App\Http\Controllers;

use App\Models\JoinedSession;
use App\Models\RunningSession;
use App\Models\SessionReview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RunningSessionController extends Controller
{
    private const MALAYSIA_REGIONS = [
        'Johor',
        'Kedah',
        'Kelantan',
        'Melaka (Malacca)',
        'Negeri Sembilan',
        'Pahang',
        'Perak',
        'Perlis',
        'Pulau Pinang (Penang)',
        'Selangor',
        'Terengganu',
        'Sabah',
        'Sarawak',
        'Kuala Lumpur',
        'Putrajaya',
        'Labuan',
    ];

    public function dashboard()
    {
        $user = Auth::user();
        $userId = $user->id;
        $now = Carbon::now();

        // Auto-start and auto-complete sessions based on time.
        RunningSession::whereNull('started_at')
            ->whereNotNull('start_time')
            ->where('start_time', '<=', $now)
            ->update(['started_at' => DB::raw('start_time')]);

        RunningSession::whereNull('completed_at')
            ->whereNotNull('end_time')
            ->where('end_time', '<=', $now)
            ->update(['completed_at' => DB::raw('end_time')]);

        $upcomingSessions = RunningSession::with(['user', 'joinedUsers.user', 'reviews.user'])
            ->where('end_time', '>=', $now)
            ->whereNull('completed_at')
            ->orderBy('start_time', 'desc')
            ->get();

        $pastSessions = RunningSession::with(['user', 'joinedUsers.user', 'reviews.user'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('joinedUsers', fn ($qq) => $qq->where('user_id', $userId));
            })
            ->where(function ($q) use ($now) {
                $q->where('end_time', '<', $now)
                  ->orWhereNotNull('completed_at');
            })
            ->orderBy('start_time', 'desc')
            ->get();

        $userCoords = $this->extractCoordinates($user->location);

        // Attach computed distance (km) to each session if both sides have coordinates
        $decorateSessions = function ($sessions) use ($userCoords) {
            foreach ($sessions as $session) {
                if ($userCoords && !is_null($session->location_lat) && !is_null($session->location_lng)) {
                    $session->distance_km = $this->haversine(
                        $userCoords['lat'],
                        $userCoords['lon'],
                        (float) $session->location_lat,
                        (float) $session->location_lng
                    );
                } else {
                    $session->distance_km = null;
                }

            }
        };

        $decorateSessions($upcomingSessions);
        $decorateSessions($pastSessions);

        // Mark whether current user joined/owns each session for view logic
        $markUserJoined = function ($sessions) use ($userId) {
            foreach ($sessions as $session) {
                $session->user_joined = $session->user_id === $userId
                    || $session->joinedUsers->contains('user_id', $userId);
            }
        };

        $markUserJoined($upcomingSessions);
        $markUserJoined($pastSessions);

        $weeklySessions = RunningSession::with('user')
            ->withCount('joinedUsers')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('joinedUsers', fn ($qq) => $qq->where('user_id', $userId));
            })
            ->orderBy('start_time')
            ->get();

        $weeklySchedule = $weeklySessions->map(function ($session) use ($weeklySessions, $userId) {
            $hasConflict = $weeklySessions->contains(function ($other) use ($session) {
                if ($other->session_id === $session->session_id) {
                    return false;
                }
                return $session->start_time < $other->end_time && $session->end_time > $other->start_time;
            });

            return [
                'session' => $session,
                'role' => $session->user_id === $userId ? 'Organizer' : 'Participant',
                'conflict' => $hasConflict,
            ];
        });

        return view('user.dashboard', [
            'upcomingSessions' => $upcomingSessions,
            'pastSessions' => $pastSessions,
            'states' => self::MALAYSIA_REGIONS,
            'weeklySchedule' => $weeklySchedule,
        ]);
    }

    public function join($session_id)
    {
        $user_id = Auth::id();

        // Check if already joined
        $alreadyJoined = JoinedSession::where('session_id', $session_id)
            ->where('user_id', $user_id)
            ->exists();

        if (!$alreadyJoined) {
            JoinedSession::create([
                'user_id' => $user_id,
                'session_id' => $session_id,
            ]);
        }

        return redirect()->back()->with('success', 'You have joined the session!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after_or_equal:start_time',
            'location_name' => 'required|string',
            'state' => 'required|string|in:' . implode(',', self::MALAYSIA_REGIONS),
            'average_pace' => 'required|string',
            'duration' => 'required|string',
            'activity' => 'required|string|max:50',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
        ]);

        $locationName = $request->location_name;
        $state = $request->state;
        if ($state && stripos($locationName, $state) === false) {
            $locationName = rtrim($locationName, ', ') . ', ' . $state;
        }

        RunningSession::create([
            'user_id' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location_name' => $locationName,
            'average_pace' => $request->average_pace,
            'duration' => $request->duration,
            'activity' => $request->activity,
            'location_lat' => $request->location_lat,
            'location_lng' => $request->location_lng,
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Session created successfully!');
    }

    public function create()
    {
        $states = self::MALAYSIA_REGIONS;
        return view('user.session-create', compact('states'));
    }

    public function edit(RunningSession $runningSession)
    {
        $this->authorizeOwner($runningSession);
        $states = self::MALAYSIA_REGIONS;
        $currentState = null;
        foreach ($states as $state) {
            if (stripos($runningSession->location_name ?? '', $state) !== false) {
                $currentState = $state;
                break;
            }
        }
        return view('user.session-edit', compact('runningSession', 'states', 'currentState'));
    }

    public function show(RunningSession $runningSession)
    {
        $runningSession->load(['user', 'joinedUsers.user', 'reviews.user']);

        $user = Auth::user();
        $coords = $this->extractCoordinates($user->location);
        if ($coords && !is_null($runningSession->location_lat) && !is_null($runningSession->location_lng)) {
            $runningSession->distance_km = $this->haversine(
                $coords['lat'],
                $coords['lon'],
                (float) $runningSession->location_lat,
                (float) $runningSession->location_lng
            );
        } else {
            $runningSession->distance_km = null;
        }

        return view('user.session-show', compact('runningSession'));
    }

    public function update(Request $request, RunningSession $runningSession)
    {
        $this->authorizeOwner($runningSession);

        $data = $request->validate([
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after_or_equal:start_time',
            'location_name' => 'required|string',
            'state' => 'required|string|in:' . implode(',', self::MALAYSIA_REGIONS),
            'average_pace' => 'required|string',
            'duration' => 'required|string',
            'activity' => 'required|string|max:50',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
        ]);

        $locationName = $data['location_name'];
        $state = $data['state'];
        if ($state && stripos($locationName, $state) === false) {
            $locationName = rtrim($locationName, ', ') . ', ' . $state;
        }

        $data['location_name'] = $locationName;
        unset($data['state']);

        $runningSession->update($data);

        return redirect()->route('user.dashboard')->with('success', 'Session updated.');
    }

    public function destroy(RunningSession $runningSession)
    {
        $this->authorizeOwner($runningSession);
        $runningSession->delete();
        return redirect()->route('user.dashboard')->with('success', 'Session deleted.');
    }

    public function start(RunningSession $runningSession)
    {
        $this->authorizeOwner($runningSession);
        $runningSession->update(['started_at' => Carbon::now()]);

        $this->notifyJoinersSessionStarted($runningSession);

        return redirect()->route('user.dashboard')->with('success', 'Session started.');
    }

    public function stop(RunningSession $runningSession)
    {
        $this->authorizeOwner($runningSession);
        $runningSession->update(['completed_at' => Carbon::now()]);
        return redirect()->route('user.dashboard')->with('success', 'Session stopped.');
    }

    public function complete(RunningSession $runningSession)
    {
        $this->authorizeOwner($runningSession);
        $runningSession->update(['completed_at' => Carbon::now()]);

        return redirect()->route('user.dashboard')->with('success', 'Session marked as completed.');
    }

    public function storeReview(Request $request, RunningSession $runningSession)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $userId = Auth::id();
        $isOwner = $runningSession->user_id === $userId;
        $isJoined = $runningSession->joinedUsers()->where('user_id', $userId)->exists();
        if (!$isOwner && !$isJoined) {
            abort(403, 'You can only review sessions you joined or created.');
        }

        // Require session to be completed or ended
        $now = Carbon::now();
        if (is_null($runningSession->completed_at) && $runningSession->end_time >= $now) {
            return redirect()->route('user.dashboard')->with('error', 'You can review only after the session ends.');
        }

        SessionReview::create([
            'running_session_id' => $runningSession->session_id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Review submitted.');
    }

    private function authorizeOwner(RunningSession $runningSession): void
    {
        if ($runningSession->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Decode a stored location JSON string into coordinates.
     */
    private function extractCoordinates($location): ?array
    {
        if (!$location) {
            return null;
        }

        $data = is_array($location) ? $location : json_decode($location, true);
        if (!$data || !isset($data['latitude'], $data['longitude'])) {
            return null;
        }

        return [
            'lat' => (float) $data['latitude'],
            'lon' => (float) $data['longitude'],
        ];
    }

    /**
     * Calculate distance in kilometers using the Haversine formula.
     */
    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1); // one decimal place
    }

    /**
     * Notify joined users via Telegram when a session starts.
     */
    private function notifyJoinersSessionStarted(RunningSession $runningSession): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) {
            return;
        }

        $session = $runningSession->load(['user', 'joinedUsers.user']);
        $ownerName = $session->user->name ?? 'Someone';
        $start = Carbon::parse($session->start_time)->format('M d, h:i A');
        $location = $session->location_name ?? 'Unknown location';

        $text = "Session started!\n"
            . "Host: {$ownerName}\n"
            . "When: {$start}\n"
            . "Where: {$location}";

        $apiUrl = "https://api.telegram.org/bot{$token}/sendMessage";

        $telegramIds = collect();
        if ($session->user && $session->user->telegram_id) {
            $telegramIds->push($session->user->telegram_id);
        }

        $joinerIds = $session->joinedUsers
            ->filter(fn($j) => $j->user && $j->user->telegram_id)
            ->map(fn($j) => $j->user->telegram_id);

        $telegramIds = $telegramIds->merge($joinerIds)->unique()->values();

        foreach ($telegramIds as $chatId) {
            Http::withoutVerifying()->post($apiUrl, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
        }
    }
}






