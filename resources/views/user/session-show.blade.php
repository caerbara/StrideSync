<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Details - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-white text-black rounded-xl shadow-xl p-8 w-full max-w-2xl space-y-4"
         data-session-lat="{{ $runningSession->session_lat ?? '' }}"
         data-session-lng="{{ $runningSession->session_lng ?? '' }}"
         data-session-source="{{ $runningSession->session_coords_source ?? '' }}"
         data-location-name="{{ $runningSession->location_name }}">
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold mb-1">Session Details</h1>
                <p class="text-sm text-gray-700">Owner: {{ $runningSession->user->name ?? 'Unknown' }}</p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="px-3 py-1 bg-gray-200 text-black rounded hover:bg-gray-300 text-sm">Back</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <p class="font-semibold">Location</p>
                <p>{{ $runningSession->location_name }}</p>
                <p class="text-sm text-gray-700 session-distance-text" data-distance-format="detail">
                    Distance: calculating...
                </p>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($runningSession->location_name) }}"
                   target="_blank"
                   rel="noopener"
                   class="session-map-link inline-block mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-semibold">
                    View on Map
                </a>
            </div>
            <div>
                <p class="font-semibold">Timing</p>
                <p>Start: {{ \Carbon\Carbon::parse($runningSession->start_time)->format('M d, Y h:i A') }}</p>
                <p>End: {{ \Carbon\Carbon::parse($runningSession->end_time)->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <p class="font-semibold">Pace</p>
                <p>{{ $runningSession->average_pace }}</p>
            </div>
            <div>
                <p class="font-semibold">Duration</p>
                <p>{{ $runningSession->duration }}</p>
            </div>
        </div>

        <div>
            <p class="font-semibold mb-2">Joined Users</p>
            @if($runningSession->joinedUsers->isEmpty())
                <p class="text-sm text-gray-700">No one has joined this session yet.</p>
            @else
                <ul class="list-disc ml-5 space-y-1">
                    @foreach($runningSession->joinedUsers as $joined)
                        <li>{{ $joined->user->name ?? 'Unknown' }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <p class="font-semibold mb-2">Reviews</p>
            @if($runningSession->reviews->isEmpty())
                <p class="text-sm text-gray-700">No reviews yet.</p>
            @else
                <ul class="space-y-2">
                    @foreach($runningSession->reviews as $review)
                        <li class="border rounded px-3 py-2">
                            <p class="font-semibold">{{ $review->rating }}/5</p>
                            <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
<script>
    (function () {
        var routeDistanceUrl = "{{ route('route.distance') }}";

        function haversineKm(lat1, lon1, lat2, lon2) {
            var R = 6371;
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function formatDistanceText(format, km) {
            var kmText = km.toFixed(1);
            if (format === 'detail') return '~' + kmText + ' km from you';
            return kmText + ' km away';
        }

        function applyDistanceToElements(container, km) {
            if (!isFinite(km)) return;
            var el = container.querySelector('.session-distance-text');
            if (!el) return;
            el.textContent = formatDistanceText(el.dataset.distanceFormat || '', km);
        }

        function applyDistanceUnavailable(container) {
            var el = container.querySelector('.session-distance-text');
            if (!el) return;
            el.textContent = 'Distance: unavailable';
        }

        function getRouteCacheKey(originLat, originLon, destinationKey) {
            return 'route:' + originLat.toFixed(4) + ',' + originLon.toFixed(4) + '|' + destinationKey;
        }

        function readRouteCache(key) {
            var raw = localStorage.getItem(key);
            if (!raw) return null;
            try {
                var payload = JSON.parse(raw);
                if (!payload || !isFinite(payload.km)) return null;
                var source = String(payload.source || '').toLowerCase();
                if (source !== 'serpapi' && source !== 'osrm') return null;
                if (payload.ts && (Date.now() - payload.ts > 1000 * 60 * 60 * 6)) return null;
                return payload;
            } catch (e) {
                return null;
            }
        }

        function writeRouteCache(key, km, source) {
            try {
                localStorage.setItem(key, JSON.stringify({ km: km, source: source || 'unknown', ts: Date.now() }));
            } catch (e) {}
        }

        function requestRouteDistance(container, userLat, userLon, locationName, latVal, lonVal) {
            if (!routeDistanceUrl) return;
            var destinationKey = locationName
                ? locationName.toLowerCase()
                : (isFinite(latVal) && isFinite(lonVal) ? latVal.toFixed(5) + ',' + lonVal.toFixed(5) : '');
            if (!destinationKey) return;

            var cacheKey = getRouteCacheKey(userLat, userLon, destinationKey);
            var cached = readRouteCache(cacheKey);
            if (cached && isFinite(cached.km)) {
                applyDistanceToElements(container, cached.km);
                return Promise.resolve(cached.km);
            }

            var params = new URLSearchParams();
            params.set('origin_lat', userLat.toFixed(6));
            params.set('origin_lng', userLon.toFixed(6));
            if (locationName) {
                params.set('destination', locationName);
            } else if (isFinite(latVal) && isFinite(lonVal)) {
                params.set('destination_lat', latVal.toFixed(6));
                params.set('destination_lng', lonVal.toFixed(6));
            } else {
                return;
            }

            return fetch(routeDistanceUrl + '?' + params.toString())
                .then(function(resp) { return resp.ok ? resp.json() : null; })
                .then(function(data) {
                    if (!data || !isFinite(data.distance_km)) return null;
                    var km = parseFloat(data.distance_km);
                    applyDistanceToElements(container, km);
                    writeRouteCache(cacheKey, km, data.source);
                    return km;
                })
                .catch(function() { return null; });
        }

        function updateDistance(userLat, userLon) {
            var container = document.querySelector('[data-session-lat][data-session-lng]');
            if (!container) return;
            var lat = parseFloat(container.dataset.sessionLat || '');
            var lon = parseFloat(container.dataset.sessionLng || '');
            var source = (container.dataset.sessionSource || '').toLowerCase();
            var locationName = container.dataset.locationName || '';

            function applyDistance(latVal, lonVal) {
                var mapLink = container.querySelector('.session-map-link');
                if (mapLink) {
                    var origin = userLat.toFixed(6) + ',' + userLon.toFixed(6);
                    var destination = locationName
                        ? locationName
                        : latVal.toFixed(6) + ',' + lonVal.toFixed(6);
                    mapLink.href = 'https://www.google.com/maps/dir/?api=1&origin=' + encodeURIComponent(origin) + '&destination=' + encodeURIComponent(destination);
                }

                requestRouteDistance(container, userLat, userLon, locationName, latVal, lonVal)
                    .then(function(km) {
                        if (!isFinite(km)) {
                            applyDistanceUnavailable(container);
                        }
                    });
            }

            if (isFinite(lat) && isFinite(lon)) {
                applyDistance(lat, lon);
            }

            if ((!isFinite(lat) || !isFinite(lon) || source !== 'geocode') && locationName) {
                var cacheKey = 'geo:' + locationName.toLowerCase();
                var cached = localStorage.getItem(cacheKey);
                if (cached) {
                    try {
                        var cachedObj = JSON.parse(cached);
                        if (isFinite(cachedObj.lat) && isFinite(cachedObj.lng)) {
                            container.dataset.sessionLat = cachedObj.lat;
                            container.dataset.sessionLng = cachedObj.lng;
                            container.dataset.sessionSource = 'geocode';
                            applyDistance(cachedObj.lat, cachedObj.lng);
                            return;
                        }
                    } catch (e) {}
                }

                fetch('/geocode-location?query=' + encodeURIComponent(locationName))
                    .then(function(resp) { return resp.ok ? resp.json() : null; })
                    .then(function(data) {
                        if (!data || !isFinite(data.lat) || !isFinite(data.lng)) return;
                        container.dataset.sessionLat = data.lat;
                        container.dataset.sessionLng = data.lng;
                        container.dataset.sessionSource = 'geocode';
                        localStorage.setItem(cacheKey, JSON.stringify({ lat: data.lat, lng: data.lng }));
                        applyDistance(data.lat, data.lng);
                    })
                    .catch(function() {});
            }
        }

        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(function(pos) {
            updateDistance(pos.coords.latitude, pos.coords.longitude);
        }, function() {
            // keep server distance if user denies
        }, { enableHighAccuracy: true, timeout: 10000 });
    })();
</script>
</body>
</html>


