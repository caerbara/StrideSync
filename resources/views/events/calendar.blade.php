<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Calendar - StrideSync</title>
    @vite('resources/css/app.css')
    <style>
        body { font-family: "Poppins", system-ui, sans-serif; }
        .event-link { color: #1f5fbf; }
        .event-link:hover { text-decoration: underline; }
    </style>
</head>
<body class="min-h-screen bg-[#b9bcc0] text-gray-900">
    @php
        $states = [
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
    @endphp

        <div class="max-w-6xl mx-auto px-6 py-10">
            <div class="flex justify-end mb-4">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                    <span>Back to Home</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <div class="flex flex-col gap-4">
                <div class="max-w-2xl">
                    <p class="text-xs uppercase tracking-[0.35em] text-gray-500">The Fast Facts</p>
                    <h1 class="mt-3 text-3xl md:text-4xl font-semibold">Running Event Calendar 2026</h1>
                    <p class="mt-2 text-sm text-gray-600">Curated race dates across Malaysia. Tap any event to visit the registration page.</p>
                    @if(!empty($calendar['updated']))
                        <p class="text-xs text-gray-500 mt-2">{{ $calendar['updated'] }}</p>
                    @endif
                </div>
            </div>
            <div class="mt-6 rounded-xl border border-gray-200 bg-white/70 p-4">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <label for="eventSearch" class="text-xs uppercase tracking-[0.3em] text-gray-500">Search</label>
                        <input id="eventSearch" type="text" placeholder="Search event name or location..."
                               class="mt-2 w-full rounded-lg border border-gray-300 bg-white/70 px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-500 focus:outline-none">
                    </div>
                    <div class="w-full md:w-64">
                        <label for="stateFilter" class="text-xs uppercase tracking-[0.3em] text-gray-500">State</label>
                        <select id="stateFilter"
                                class="mt-2 w-full rounded-lg border border-gray-300 bg-white/70 px-4 py-2 text-sm text-gray-800 focus:border-gray-500 focus:outline-none">
                            <option value="">All states</option>
                            @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        @if(!$calendar['ok'])
            <div class="mt-8 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                <p class="font-semibold">Unable to load the event calendar right now.</p>
                <p class="text-sm mt-2">{{ $calendar['error'] ?? 'Please try again later.' }}</p>
            </div>
        @else
            <div class="mt-6">
                @forelse($calendar['months'] as $month)
                    <section class="mb-8">
                        <h2 class="text-sm font-semibold tracking-[0.3em] uppercase text-gray-700">{{ $month['label'] }}</h2>
                        <ul class="mt-3 space-y-2">
                            @foreach($month['events'] as $event)
                                @php
                                    $registerUrl = $event['url'] ?? null;
                                    $state = '';
                                    if (preg_match('/\(([^)]*)\)/', $event['title'], $m)) {
                                        $parts = array_map('trim', explode(',', $m[1]));
                                        $state = $parts ? end($parts) : '';
                                    }
                                @endphp
                                <li>
                                    @if($registerUrl)
                                        <a href="{{ $registerUrl }}" target="_blank" rel="noopener"
                                           class="event-link text-sm font-normal block"
                                           data-title="{{ strtolower($event['title']) }}"
                                           data-state="{{ strtolower($state) }}">
                                            <span class="text-gray-700">{{ $event['date'] }}</span>
                                            <span class="text-gray-700"> - </span>
                                            <span class="text-gray-800">{{ $event['title'] }}</span>
                                        </a>
                                    @else
                                        <span class="text-sm font-normal block"
                                              data-title="{{ strtolower($event['title']) }}"
                                              data-state="{{ strtolower($state) }}">
                                            <span class="text-gray-700">{{ $event['date'] }}</span>
                                            <span class="text-gray-700"> - </span>
                                            <span class="text-gray-800">{{ $event['title'] }}</span>
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @empty
                    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-yellow-700">
                        <p>No events found yet.</p>
                    </div>
                @endforelse
            </div>
        @endif

        <div class="mt-8 text-xs text-gray-500">
            <span>Source:</span>
            <a href="{{ $calendar['source'] }}" target="_blank" rel="noopener" class="underline decoration-gray-400 hover:text-gray-900">
                Pacemakers Malaysia (Blogspot)
            </a>
        </div>
        <div class="mt-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                <span>Back to Home</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>

    <script>
        (function () {
            var searchInput = document.getElementById('eventSearch');
            var stateFilter = document.getElementById('stateFilter');
            if (!searchInput || !stateFilter) return;

            function applyFilters() {
                var term = (searchInput.value || '').toLowerCase().trim();
                var state = (stateFilter.value || '').toLowerCase().trim();
                var cards = document.querySelectorAll('[data-title]');

                cards.forEach(function(card) {
                    var title = card.getAttribute('data-title') || '';
                    var cardState = card.getAttribute('data-state') || '';
                    var matchesTerm = !term || title.includes(term);
                    var matchesState = !state || cardState === state;
                    card.closest('a').style.display = (matchesTerm && matchesState) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', applyFilters);
            stateFilter.addEventListener('change', applyFilters);
        })();
    </script>
</body>
</html>
