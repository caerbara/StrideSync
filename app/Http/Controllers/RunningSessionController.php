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
        $geoService = app(\App\Services\GeocodingService::class);

        // Attach computed distance (km) to each session if both sides have coordinates
        $decorateSessions = function ($sessions) use ($userCoords, $geoService) {
            foreach ($sessions as $session) {
                $sessionCoords = $this->resolveSessionCoordinates($session, $geoService);
                $session->session_lat = $sessionCoords['lat'] ?? null;
                $session->session_lng = $sessionCoords['lng'] ?? null;
                $session->session_coords_source = $sessionCoords['source'] ?? null;

                if ($userCoords && $session->session_lat !== null && $session->session_lng !== null) {
                    $session->session_distance_km = $this->haversine(
                        $userCoords['lat'],
                        $userCoords['lon'],
                        (float) $session->session_lat,
                        (float) $session->session_lng
                    );
                    $session->distance_km = $session->session_distance_km;
                } else {
                    $session->session_distance_km = null;
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
            ->where(function ($q) use ($now) {
                $q->where('end_time', '>=', $now)
                  ->orWhereNull('end_time');
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

        $participatedSessions = RunningSession::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereHas('joinedUsers', fn ($qq) => $qq->where('user_id', $userId));
        })->get();

        $totalSessionsCreated = RunningSession::where('user_id', $userId)->count();
        $totalSessionsJoined = JoinedSession::where('user_id', $userId)->count();
        $upcomingSessionsCount = $weeklySessions->count();
        $completedSessionsCount = RunningSession::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereHas('joinedUsers', fn ($qq) => $qq->where('user_id', $userId));
        })->where(function ($q) use ($now) {
            $q->where('end_time', '<', $now)
                ->orWhereNotNull('completed_at');
        })->count();

        $paceSamples = [];
        $totalDistanceKm = 0.0;
        foreach ($participatedSessions as $session) {
            $pace = (string) ($session->average_pace ?? '');
            if ($pace !== '') {
                preg_match_all('/(\d{1,2}):(\d{2})/', $pace, $matches, PREG_SET_ORDER);
                if (!empty($matches)) {
                    $times = array_slice($matches, 0, 2);
                    $sum = 0.0;
                    foreach ($times as $t) {
                        $sum += ((int) $t[1]) + ((int) $t[2]) / 60;
                    }
                    $paceSamples[] = $sum / count($times);
                }
            }

            $activity = (string) ($session->activity ?? '');
            if (preg_match('/(\d+(?:\.\d+)?)\s*km/i', $activity, $m)) {
                $totalDistanceKm += (float) $m[1];
            }
        }

        $averagePace = null;
        if (count($paceSamples) > 0) {
            $avgMinutes = array_sum($paceSamples) / count($paceSamples);
            $mins = (int) floor($avgMinutes);
            $secs = (int) round(($avgMinutes - $mins) * 60);
            if ($secs === 60) {
                $mins += 1;
                $secs = 0;
            }
            $averagePace = sprintf('%d:%02d/km', $mins, $secs);
        }

        return view('user.dashboard', [
            'upcomingSessions' => $upcomingSessions,
            'pastSessions' => $pastSessions,
            'states' => self::MALAYSIA_REGIONS,
            'weeklySchedule' => $weeklySchedule,
            'fastFacts' => [
                'total_created' => $totalSessionsCreated,
                'total_joined' => $totalSessionsJoined,
                'upcoming_count' => $upcomingSessionsCount,
                'completed_count' => $completedSessionsCount,
                'average_pace' => $averagePace,
                'total_distance_km' => $totalDistanceKm,
            ],
            'user' => Auth::user(),
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

        $locationLat = $request->location_lat;
        $locationLng = $request->location_lng;
        $geo = app(\App\Services\GeocodingService::class)->geocodeLocationName($locationName);
        if ($geo) {
            $locationLat = $geo['lat'];
            $locationLng = $geo['lng'];
        }

        RunningSession::create([
            'user_id' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location_name' => $locationName,
            'average_pace' => $request->average_pace,
            'duration' => $request->duration,
            'activity' => $request->activity,
            'location_lat' => $locationLat,
            'location_lng' => $locationLng,
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
        $geoService = app(\App\Services\GeocodingService::class);
        $sessionCoords = $this->resolveSessionCoordinates($runningSession, $geoService);
        $runningSession->session_lat = $sessionCoords['lat'] ?? null;
        $runningSession->session_lng = $sessionCoords['lng'] ?? null;
        $runningSession->session_coords_source = $sessionCoords['source'] ?? null;

        if ($coords && $runningSession->session_lat !== null && $runningSession->session_lng !== null) {
            $runningSession->session_distance_km = $this->haversine(
                $coords['lat'],
                $coords['lon'],
                (float) $runningSession->session_lat,
                (float) $runningSession->session_lng
            );
            $runningSession->distance_km = $runningSession->session_distance_km;
        } else {
            $runningSession->session_distance_km = null;
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

        $locationLat = $data['location_lat'] ?? null;
        $locationLng = $data['location_lng'] ?? null;
        $geo = app(\App\Services\GeocodingService::class)->geocodeLocationName($locationName);
        if ($geo) {
            $locationLat = $geo['lat'];
            $locationLng = $geo['lng'];
        }

        $data['location_name'] = $locationName;
        $data['location_lat'] = $locationLat;
        $data['location_lng'] = $locationLng;
        unset($data['state']);

        $runningSession->update($data);

        return redirect()->route('user.dashboard')->with('success', 'Session updated.');
    }

    public function destroy(RunningSession $runningSession)
    {
        $this->authorizeOwner($runningSession);
        $runningSession->delete();
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Session deleted.');
        }

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

        $alreadyReviewed = SessionReview::where('running_session_id', $runningSession->session_id)
            ->where('user_id', $userId)
            ->exists();
        if ($alreadyReviewed) {
            return redirect()->route('user.dashboard')->with('error', 'You have already reviewed this session.');
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
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if (!$user->isAdmin() && $runningSession->user_id !== $user->id) {
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
        if (!$data) {
            return null;
        }

        $lat = $data['latitude'] ?? $data['lat'] ?? null;
        $lon = $data['longitude'] ?? $data['lng'] ?? $data['lon'] ?? null;

        if (!is_numeric($lat) || !is_numeric($lon)) {
            return null;
        }

        return [
            'lat' => (float) $lat,
            'lon' => (float) $lon,
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

    private function resolveSessionCoordinates($session, \App\Services\GeocodingService $geoService): array
    {
        $name = trim((string) ($session->location_name ?? ''));
        if ($name !== '') {
            $geo = $geoService->geocodeLocationName($name);
            if ($geo) {
                return [
                    'lat' => $geo['lat'],
                    'lng' => $geo['lng'],
                    'source' => 'geocode',
                ];
            }
        }

        if (!is_null($session->location_lat) && !is_null($session->location_lng)) {
            return [
                'lat' => (float) $session->location_lat,
                'lng' => (float) $session->location_lng,
                'source' => 'stored',
            ];
        }

        return [];
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








