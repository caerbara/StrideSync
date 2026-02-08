<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Session - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-white text-black rounded-xl shadow-xl p-8 w-full max-w-xl">
        <h1 class="text-2xl font-bold mb-4">Edit Session</h1>

        <form method="POST" action="{{ route('sessions.update', $runningSession->session_id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold mb-1" for="location_name">Location (location name, district)</label>
                <input type="text" id="location_name" name="location_name" value="{{ old('location_name', $runningSession->location_name) }}" class="w-full border rounded px-3 py-2" placeholder="e.g., Merlimau, Jasin" required>
                <button type="button" id="useEditLocation" class="mt-2 px-3 py-1 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">Use my location (auto-fill)</button>
                <p id="editGeoStatus" class="text-xs text-gray-600 mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" for="session_state">State (Malaysia)</label>
                <select id="session_state" name="state" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select your state</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('state', $currentState) === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" id="location_lat" name="location_lat" value="{{ old('location_lat', $runningSession->location_lat) }}">
            <input type="hidden" id="location_lng" name="location_lng" value="{{ old('location_lng', $runningSession->location_lng) }}">

            <div>
                <label class="block text-sm font-semibold mb-1" for="location_area">Area</label>
                <input type="text" id="location_area" name="location_area" value="{{ old('location_area', $runningSession->location_name) }}" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" for="start_time">Start Time</label>
                <input type="datetime-local" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($runningSession->start_time)->format('Y-m-d\TH:i')) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" for="end_time">End Time</label>
                <input type="datetime-local" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($runningSession->end_time)->format('Y-m-d\TH:i')) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" for="average_pace">Average Pace</label>
                <select id="average_pace" name="average_pace" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select pace</option>
                    <option value="12:00/km - 11:00/km" {{ old('average_pace', $runningSession->average_pace) == '12:00/km - 11:00/km' ? 'selected' : '' }}>12:00/km - 11:00/km</option>
                    <option value="11:00/km - 10:00/km" {{ old('average_pace', $runningSession->average_pace) == '11:00/km - 10:00/km' ? 'selected' : '' }}>11:00/km - 10:00/km</option>
                    <option value="10:00/km - 9:00/km" {{ old('average_pace', $runningSession->average_pace) == '10:00/km - 9:00/km' ? 'selected' : '' }}>10:00/km - 9:00/km</option>
                    <option value="9:00/km - 8:00/km" {{ old('average_pace', $runningSession->average_pace) == '9:00/km - 8:00/km' ? 'selected' : '' }}>9:00/km - 8:00/km</option>
                    <option value="8:00/km - 7:00/km" {{ old('average_pace', $runningSession->average_pace) == '8:00/km - 7:00/km' ? 'selected' : '' }}>8:00/km - 7:00/km</option>
                    <option value="7:00/km - 6:00/km" {{ old('average_pace', $runningSession->average_pace) == '7:00/km - 6:00/km' ? 'selected' : '' }}>7:00/km - 6:00/km</option>
                    <option value="6:00/km - 5:00/km" {{ old('average_pace', $runningSession->average_pace) == '6:00/km - 5:00/km' ? 'selected' : '' }}>6:00/km - 5:00/km</option>
                    <option value="5:00/km - 4:00/km" {{ old('average_pace', $runningSession->average_pace) == '5:00/km - 4:00/km' ? 'selected' : '' }}>5:00/km - 4:00/km</option>
                    <option value="4:00/km - 3:00/km" {{ old('average_pace', $runningSession->average_pace) == '4:00/km - 3:00/km' ? 'selected' : '' }}>4:00/km - 3:00/km</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" for="activity">Activity</label>
                <select id="activity" name="activity" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select activity</option>
                    <option value="5km" {{ old('activity', $runningSession->activity) == '5km' ? 'selected' : '' }}>5km</option>
                    <option value="10km" {{ old('activity', $runningSession->activity) == '10km' ? 'selected' : '' }}>10km</option>
                    <option value="Long Run" {{ old('activity', $runningSession->activity) == 'Long Run' ? 'selected' : '' }}>Long Run</option>
                    <option value="Interval" {{ old('activity', $runningSession->activity) == 'Interval' ? 'selected' : '' }}>Interval</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" for="duration">Duration</label>
                <input type="text" id="duration" name="duration" value="{{ old('duration', $runningSession->duration) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-gray-200 text-black rounded hover:bg-gray-300">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

<script>
    const editStatusEl = document.getElementById('editGeoStatus');
    const editBtnGeo = document.getElementById('useEditLocation');
    const editLatInput = document.getElementById('location_lat');
    const editLngInput = document.getElementById('location_lng');
    const editStartTime = document.getElementById('start_time');
    const editEndTime = document.getElementById('end_time');
    const editAreaInput = document.getElementById('location_area');
    const editNameInput = document.getElementById('location_name');
    const editGeocodeUrl = "{{ route('reverse.geocode') }}";

    const setEditStatus = (msg, isError = false) => {
        if (!editStatusEl) return;
        editStatusEl.textContent = msg;
        editStatusEl.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-700');
    };

    const formatEditCoord = (value) => {
        const num = Number(value);
        if (!Number.isFinite(num)) return '';
        return num.toFixed(5).replace(/\.?0+$/, '');
    };

    const updateEditArea = () => {
        if (!editAreaInput) return;
        const latText = formatEditCoord(editLatInput?.value);
        const lngText = formatEditCoord(editLngInput?.value);
        if (!latText || !lngText || (latText === '0' && lngText === '0')) {
            editAreaInput.value = '';
            return;
        }
        editAreaInput.value = `${latText}, ${lngText}`;
    };

    const resolveEditArea = async () => {
        const latValue = Number(editLatInput?.value);
        const lngValue = Number(editLngInput?.value);
        if (!Number.isFinite(latValue) || !Number.isFinite(lngValue)) return;

        try {
            setEditStatus('Resolving area...');
            const response = await fetch(`${editGeocodeUrl}?lat=${encodeURIComponent(latValue)}&lon=${encodeURIComponent(lngValue)}`);
            if (!response.ok) {
                setEditStatus('Unable to resolve area.', true);
                return;
            }
            const data = await response.json();
            if (data?.name && editAreaInput) {
                editAreaInput.value = data.name;
                setEditStatus('Area resolved.');
            }
        } catch (error) {
            setEditStatus('Unable to resolve area.', true);
        }
    };

    const fillEditFromGeo = (position) => {
        const { latitude, longitude } = position.coords;
        if (editLatInput) editLatInput.value = latitude.toFixed(7);
        if (editLngInput) editLngInput.value = longitude.toFixed(7);
        updateEditArea();
        resolveEditArea();
        setEditStatus('Location captured.');
    };

    const editGeoError = (err) => setEditStatus(`Unable to fetch location: ${err.message}`, true);

    if (editBtnGeo) {
        editBtnGeo.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setEditStatus('Geolocation is not supported in this browser.', true);
                return;
            }
            setEditStatus('Requesting location...');
            navigator.geolocation.getCurrentPosition(fillEditFromGeo, editGeoError, { enableHighAccuracy: true, timeout: 10000 });
        });
    }

    if (editLatInput) editLatInput.addEventListener('input', updateEditArea);
    if (editLngInput) editLngInput.addEventListener('input', updateEditArea);
    updateEditArea();

    if (editStartTime && editEndTime) {
        editStartTime.addEventListener('change', () => {
            if (!editStartTime.value) return;
            if (!editEndTime.value || editEndTime.value < editStartTime.value) {
                editEndTime.value = editStartTime.value;
            }
            editEndTime.min = editStartTime.value;
        });
        if (editStartTime.value) {
            editEndTime.min = editStartTime.value;
        }
    }
</script>


