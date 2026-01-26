<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Session - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-white text-black rounded-xl shadow-xl p-8 w-full max-w-xl">
        <h1 class="text-2xl font-bold mb-4">Create New Session</h1>

        <form action="{{ route('sessions.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="location_name" class="block text-sm font-semibold mb-1">Location (location name, district)</label>
                <input type="text" name="location_name" id="location_name" class="w-full p-2 border rounded" placeholder="e.g., Merlimau, Jasin" required>
                <button type="button" id="useMyLocation" class="mt-2 px-3 py-1 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">Use my location (auto-fill)</button>
                <p id="geoStatus" class="text-xs text-gray-600 mt-1"></p>
            </div>

            <div>
                <label for="session_state" class="block text-sm font-semibold mb-1">State (Malaysia)</label>
                <select id="session_state" name="state" class="w-full p-2 border rounded" required>
                    <option value="">Select your state</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="location_lat" id="location_lat" value="{{ old('location_lat') }}">
            <input type="hidden" name="location_lng" id="location_lng" value="{{ old('location_lng') }}">

            <div>
                <label for="location_area" class="block text-sm font-semibold mb-1">Area</label>
                <input type="text" name="location_area" id="location_area" value="{{ old('location_area') }}" class="w-full p-2 border rounded bg-gray-100" placeholder="Use my location to auto-fill" readonly>
            </div>

            <div>
                <label for="start_time" class="block text-sm font-semibold mb-1">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label for="end_time" class="block text-sm font-semibold mb-1">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label for="average_pace" class="block text-sm font-semibold mb-1">Average Pace</label>
                <select name="average_pace" id="average_pace" class="w-full p-2 border rounded" required>
                    <option value="">Select pace</option>
                    <option value="12:00/km - 11:00/km" {{ old('average_pace') == '12:00/km - 11:00/km' ? 'selected' : '' }}>12:00/km - 11:00/km</option>
                    <option value="11:00/km - 10:00/km" {{ old('average_pace') == '11:00/km - 10:00/km' ? 'selected' : '' }}>11:00/km - 10:00/km</option>
                    <option value="10:00/km - 9:00/km" {{ old('average_pace') == '10:00/km - 9:00/km' ? 'selected' : '' }}>10:00/km - 9:00/km</option>
                    <option value="9:00/km - 8:00/km" {{ old('average_pace') == '9:00/km - 8:00/km' ? 'selected' : '' }}>9:00/km - 8:00/km</option>
                    <option value="8:00/km - 7:00/km" {{ old('average_pace') == '8:00/km - 7:00/km' ? 'selected' : '' }}>8:00/km - 7:00/km</option>
                    <option value="7:00/km - 6:00/km" {{ old('average_pace') == '7:00/km - 6:00/km' ? 'selected' : '' }}>7:00/km - 6:00/km</option>
                    <option value="6:00/km - 5:00/km" {{ old('average_pace') == '6:00/km - 5:00/km' ? 'selected' : '' }}>6:00/km - 5:00/km</option>
                    <option value="5:00/km - 4:00/km" {{ old('average_pace') == '5:00/km - 4:00/km' ? 'selected' : '' }}>5:00/km - 4:00/km</option>
                    <option value="4:00/km - 3:00/km" {{ old('average_pace') == '4:00/km - 3:00/km' ? 'selected' : '' }}>4:00/km - 3:00/km</option>
                </select>
            </div>

            <div>
                <label for="activity" class="block text-sm font-semibold mb-1">Activity</label>
                <select name="activity" id="activity" class="w-full p-2 border rounded" required>
                    <option value="">Select activity</option>
                    <option value="5km" {{ old('activity') == '5km' ? 'selected' : '' }}>5km</option>
                    <option value="10km" {{ old('activity') == '10km' ? 'selected' : '' }}>10km</option>
                    <option value="Long Run" {{ old('activity') == 'Long Run' ? 'selected' : '' }}>Long Run</option>
                    <option value="Interval" {{ old('activity') == 'Interval' ? 'selected' : '' }}>Interval</option>
                </select>
            </div>

            <div>
                <label for="duration" class="block text-sm font-semibold mb-1">Duration</label>
                <input type="text" name="duration" id="duration" placeholder="e.g., 45 minutes" class="w-full p-2 border rounded" required>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Create</button>
                <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-gray-200 text-black rounded hover:bg-gray-300">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>

<script>
    const statusEl = document.getElementById('geoStatus');
    const btnGeo = document.getElementById('useMyLocation');
    const latInput = document.getElementById('location_lat');
    const lngInput = document.getElementById('location_lng');
    const nameInput = document.getElementById('location_name');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const areaInput = document.getElementById('location_area');
    const geocodeUrl = "{{ route('reverse.geocode') }}";

    const setStatus = (msg, isError = false) => {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-700');
    };

    const formatCoord = (value) => {
        const num = Number(value);
        if (!Number.isFinite(num)) return '';
        return num.toFixed(5).replace(/\.?0+$/, '');
    };

    const updateArea = () => {
        if (!areaInput) return;
        const latText = formatCoord(latInput?.value);
        const lngText = formatCoord(lngInput?.value);
        if (!latText || !lngText || (latText === '0' && lngText === '0')) {
            areaInput.value = '';
            return;
        }
        areaInput.value = `${latText}, ${lngText}`;
    };

    const resolveArea = async () => {
        const latValue = Number(latInput?.value);
        const lngValue = Number(lngInput?.value);
        if (!Number.isFinite(latValue) || !Number.isFinite(lngValue)) return;

        try {
            setStatus('Resolving area...');
            const response = await fetch(`${geocodeUrl}?lat=${encodeURIComponent(latValue)}&lon=${encodeURIComponent(lngValue)}`);
            if (!response.ok) {
                setStatus('Unable to resolve area.', true);
                return;
            }
            const data = await response.json();
            if (data?.name && areaInput) {
                areaInput.value = data.name;
                setStatus('Area resolved.');
            }
        } catch (error) {
            setStatus('Unable to resolve area.', true);
        }
    };

    const fillFromGeo = (position) => {
        const { latitude, longitude } = position.coords;
        latInput.value = latitude.toFixed(7);
        lngInput.value = longitude.toFixed(7);
        updateArea();
        resolveArea();
        setStatus('Location captured.');
    };

    const geoError = (err) => {
        setStatus(`Unable to fetch location: ${err.message}`, true);
    };

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

    if (latInput) latInput.addEventListener('input', updateArea);
    if (lngInput) lngInput.addEventListener('input', updateArea);
    updateArea();

    if (startTimeInput && endTimeInput) {
        startTimeInput.addEventListener('change', () => {
            if (!startTimeInput.value) return;
            if (!endTimeInput.value || endTimeInput.value < startTimeInput.value) {
                endTimeInput.value = startTimeInput.value;
            }
            endTimeInput.min = startTimeInput.value;
        });
        if (startTimeInput.value) {
            endTimeInput.min = startTimeInput.value;
        }
    }
</script>
