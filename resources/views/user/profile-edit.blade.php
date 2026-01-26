<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - StrideSync</title>
    @vite('resources/css/app.css')
</head>
@php
    $embed = request()->boolean('embed');
@endphp
<body class="{{ $embed ? 'bg-transparent text-black min-h-0' : 'bg-gray-900 text-white min-h-screen' }} flex items-center justify-center">
    <div class="bg-white text-black rounded-xl shadow-xl p-8 w-full {{ $embed ? 'max-w-none' : 'max-w-xl' }}">
        <h1 class="text-2xl font-bold mb-4">Edit Profile</h1>

        @php
            $decodedLocation = $user->location ? json_decode($user->location, true) : [];
            $currentState = $decodedLocation['state'] ?? null;
            $currentLat = $decodedLocation['latitude'] ?? null;
            $currentLng = $decodedLocation['longitude'] ?? null;
        @endphp
        <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold mb-1" for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" for="avg_pace">Average Pace</label>
                <select id="avg_pace" name="avg_pace" class="w-full border rounded px-3 py-2">
                    <option value="">Select pace</option>
                    <option value="12:00/km - 11:00/km" {{ old('avg_pace', $user->avg_pace) == '12:00/km - 11:00/km' ? 'selected' : '' }}>12:00/km - 11:00/km</option>
                    <option value="11:00/km - 10:00/km" {{ old('avg_pace', $user->avg_pace) == '11:00/km - 10:00/km' ? 'selected' : '' }}>11:00/km - 10:00/km</option>
                    <option value="10:00/km - 9:00/km" {{ old('avg_pace', $user->avg_pace) == '10:00/km - 9:00/km' ? 'selected' : '' }}>10:00/km - 9:00/km</option>
                    <option value="9:00/km - 8:00/km" {{ old('avg_pace', $user->avg_pace) == '9:00/km - 8:00/km' ? 'selected' : '' }}>9:00/km - 8:00/km</option>
                    <option value="8:00/km - 7:00/km" {{ old('avg_pace', $user->avg_pace) == '8:00/km - 7:00/km' ? 'selected' : '' }}>8:00/km - 7:00/km</option>
                    <option value="7:00/km - 6:00/km" {{ old('avg_pace', $user->avg_pace) == '7:00/km - 6:00/km' ? 'selected' : '' }}>7:00/km - 6:00/km</option>
                    <option value="6:00/km - 5:00/km" {{ old('avg_pace', $user->avg_pace) == '6:00/km - 5:00/km' ? 'selected' : '' }}>6:00/km - 5:00/km</option>
                    <option value="5:00/km - 4:00/km" {{ old('avg_pace', $user->avg_pace) == '5:00/km - 4:00/km' ? 'selected' : '' }}>5:00/km - 4:00/km</option>
                    <option value="4:00/km - 3:00/km" {{ old('avg_pace', $user->avg_pace) == '4:00/km - 3:00/km' ? 'selected' : '' }}>4:00/km - 3:00/km</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" for="gender">Gender</label>
                <select id="gender" name="gender" class="w-full border rounded px-3 py-2">
                    <option value="">Select gender</option>
                    <option value="Male" {{ old('gender', $user->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $user->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" for="location">State (Malaysia)</label>
                <select id="location" name="state" class="w-full border rounded px-3 py-2">
                    <option value="">Select your state</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('state', $currentState) === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold mb-1" for="latitude">Latitude (optional)</label>
                    <input type="number" step="0.0000001" id="latitude" name="latitude" value="{{ old('latitude', $currentLat) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" for="longitude">Longitude (optional)</label>
                    <input type="number" step="0.0000001" id="longitude" name="longitude" value="{{ old('longitude', $currentLng) }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>
            <button type="button" id="useProfileLocation" class="px-3 py-1 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">Use my current location</button>
            <p id="profileGeoStatus" class="text-xs text-gray-600 mt-1"></p>

            <div>
                <label class="block text-sm font-semibold mb-1" for="photo">Profile Photo (optional)</label>
                <input type="file" id="photo" name="photo" accept="image/*" class="w-full border rounded px-3 py-2 bg-white">
                @if($user->photo_url)
                    <p class="text-xs text-gray-600 mt-1">Current photo:</p>
                    <img src="{{ $user->photo_url }}" alt="Profile photo" class="mt-2 h-20 w-20 object-cover rounded">
                @endif
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                @if(!$embed)
                    <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-gray-200 text-black rounded hover:bg-gray-300">Cancel</a>
                @endif
            </div>
        </form>
    </div>
</body>
</html>

<script>
    const statusEl = document.getElementById('profileGeoStatus');
    const btnGeo = document.getElementById('useProfileLocation');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    const setStatus = (msg, isError = false) => {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-700');
    };

    const fillFromGeo = (pos) => {
        const { latitude, longitude } = pos.coords;
        if (latInput) latInput.value = latitude.toFixed(7);
        if (lngInput) lngInput.value = longitude.toFixed(7);
        setStatus('Location captured.');
    };

    const geoError = (err) => setStatus(`Unable to fetch location: ${err.message}`, true);

    if (btnGeo) {
        btnGeo.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setStatus('Geolocation is not supported in this browser.', true);
                return;
            }
            setStatus('Requesting location...');
            navigator.geolocation.getCurrentPosition(fillFromGeo, geoError, { enableHighAccuracy: true, timeout: 10000 });
        });
    }
</script>
