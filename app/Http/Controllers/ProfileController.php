<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CloudinaryService;
use App\Services\GeocodingService;

class ProfileController extends Controller
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
        $user = Auth::user();
        $states = self::MALAYSIA_REGIONS;
        return view('user.profile', compact('user', 'states'));
    }

    public function edit()
    {
        $user = Auth::user();
        $states = self::MALAYSIA_REGIONS;
        return view('user.profile-edit', compact('user', 'states'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avg_pace' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:50',
            'state' => 'nullable|string|in:' . implode(',', self::MALAYSIA_REGIONS),
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:3072',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $cloudinary = app(CloudinaryService::class);
            $cloudinaryUrl = $cloudinary->uploadUploadedFile($file, 'stridesync/profile_photos');

            if ($cloudinaryUrl) {
                $data['strava_screenshot'] = $cloudinaryUrl;
            } else {
                $path = $file->store('profile_photos', 'public');
                $data['strava_screenshot'] = $path; // reuse existing column for profile photo path
            }
        }

        // Store location as JSON {city, state, latitude, longitude}
        $state = $data['state'] ?? null;
        $lat = $data['latitude'] ?? null;
        $lng = $data['longitude'] ?? null;
        $city = null;

        if ($lat !== null && $lng !== null) {
            $geo = app(GeocodingService::class);
            $resolved = $geo->reverseGeocodeCityState((float) $lat, (float) $lng);
            if (!$state && !empty($resolved['state'])) {
                $state = $resolved['state'];
            }
            if (!empty($resolved['city'])) {
                $city = $resolved['city'];
            }
        }

        $locationPayload = null;
        if ($state || $lat || $lng) {
            $locationPayload = json_encode([
                'city' => $city,
                'state' => $state,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'avg_pace' => $data['avg_pace'] ?? null,
            'gender' => $data['gender'] ?? null,
            'location' => $locationPayload,
            'strava_screenshot' => $data['strava_screenshot'] ?? $user->strava_screenshot,
        ]);

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }

    public function destroy()
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Profile deleted.');
    }

    public function unlinkTelegram(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'telegram_id' => null,
            'telegram_state' => 'unlinked',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Telegram account unlinked.',
        ]);
    }
}
