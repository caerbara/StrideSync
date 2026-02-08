<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CloudinaryService;
use App\Services\EmailTacService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
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

    public function show()
    {
        $states = self::MALAYSIA_REGIONS;
        return view('auth.register', compact('states'));
    }

    public function register(Request $request)
    {
        Log::info('Register form submitted:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'password' => 'required|string|min:6|confirmed',
            'tac_code' => 'required|string',
            'avg_pace' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'state' => 'required|string|in:' . implode(',', self::MALAYSIA_REGIONS),
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:3072',
        ]);

        $tacService = app(EmailTacService::class);
        $tacResult = $tacService->verifyTac($validated['email'], 'register', $validated['tac_code']);
        if (!$tacResult['ok']) {
            return back()->withErrors(['tac_code' => $tacResult['message']])->withInput();
        }

        Log::info('Validation passed');

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'web_' . uniqid('', true) . '.' . $file->getClientOriginalExtension();
            try {
                $cloudinary = app(CloudinaryService::class);
                $cloudinaryUrl = $cloudinary->uploadBytes(
                    file_get_contents($file->getRealPath()),
                    $filename,
                    'stridesync/profile_photos'
                );
                $photoPath = $cloudinaryUrl ?: $file->store('profile_photos', 'public');
            } catch (\Throwable $e) {
                $photoPath = $file->store('profile_photos', 'public');
            }
        }

        $area = $validated['area'] ?? null;
        if (!$area && isset($validated['latitude'], $validated['longitude'])) {
            $latText = rtrim(rtrim(number_format((float) $validated['latitude'], 5, '.', ''), '0'), '.');
            $lonText = rtrim(rtrim(number_format((float) $validated['longitude'], 5, '.', ''), '0'), '.');
            $area = "{$latText}, {$lonText}";
        }

        $locationPayload = json_encode([
            'state' => $validated['state'],
            'area' => $area,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => bcrypt($validated['password']),
            'avg_pace' => $validated['avg_pace'] ?? null,
            'location' => $locationPayload,
            'strava_screenshot' => $photoPath,
        ]);

        Log::info('User created', ['id' => $user->id]);

        auth()->login($user);
        Log::info('Logged in and redirecting to dashboard');

        return redirect()->route('user.dashboard')->with('success', 'Successfully registered! Welcome to StrideSync.');
    }

    public function reverseGeocode(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ]);

        $url = 'https://nominatim.openstreetmap.org/reverse';
        $userAgent = config('services.nominatim.user_agent', 'StrideSync/1.0 (admin@stridesync.local)');
        $sslVerify = (bool) config('services.nominatim.ssl_verify', true);

        $query = [
            'format' => 'jsonv2',
            'lat' => $validated['lat'],
            'lon' => $validated['lon'],
            'zoom' => 10,
            'addressdetails' => 1,
        ];

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => $userAgent,
                ])
                ->withOptions(['verify' => $sslVerify])
                ->get($url, $query);

            if (!$response->successful()) {
                return response()->json(['name' => null]);
            }

            $data = $response->json();
            $address = is_array($data['address'] ?? null) ? $data['address'] : [];
            $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? $address['hamlet'] ?? null;
            $state = $address['state'] ?? $address['region'] ?? null;

            $parts = array_filter([$city, $state], fn($value) => is_string($value) && trim($value) !== '');
            $name = $parts ? implode(', ', $parts) : ($data['display_name'] ?? null);

            return response()->json(['name' => $name]);
        } catch (\Throwable $e) {
            Log::warning('Reverse geocode failed', ['error' => $e->getMessage()]);
            return response()->json(['name' => null]);
        }
    }
}


