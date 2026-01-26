<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white h-screen relative overflow-hidden">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('{{ asset('images/user-bg.jpg') }}');"></div>

<!-- Success Notification -->
@if (session('success'))
    <div id="notification" class="fixed top-10 right-10 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 animate-slideIn">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <div>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
        <button onclick="closeNotification()" class="ml-4 text-white hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
</div>
@endif

<style>
    .dashboard-filter {
        background: rgba(255, 255, 255, 0.98);
        color: #000000;
        border: 1px solid rgba(0, 0, 0, 0.2);
    }
    .dashboard-filter option {
        background: #ffffff;
        color: #000000;
    }
    .dashboard-filter::placeholder {
        color: rgba(0, 0, 0, 0.6);
    }
</style>

<div class="absolute top-10 left-10 z-30 text-left">
    <p class="text-2xl font-semibold text-white mb-1">Hello,</p>
    <p class="text-3xl font-bold text-white">
        {{ Auth::user()->name }}
    </p>
    <div class="mt-4">
        <button type="button" onclick="openModal('profileModal')" class="inline-block px-4 py-2 bg-white text-black rounded shadow hover:scale-105 transition text-sm">View profile</button>
    </div>
    <div class="mt-3 flex items-center gap-2">
        <button id="tab-upcoming" type="button" onclick="showUpcoming()" class="px-3 py-1 rounded bg-white text-black font-semibold shadow">Upcoming</button>
        <button id="tab-past" type="button" onclick="showPast()" class="px-3 py-1 rounded bg-gray-800 text-white border border-white/20">History</button>
    </div>
</div>

<div class="absolute top-10 right-10 z-30 flex items-center space-x-3">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
    <h1 class="text-5xl poppins-title tracking-tighter" style="color: #a1e8c5;">STRIDESYNC</h1>
</div>

<!-- Cards Carousel (Upcoming) -->
<div class="absolute top-36 left-10 right-10 bottom-28 z-20 flex flex-col items-center">
    <div class="w-full max-w-[1680px] flex flex-wrap gap-3 items-center mb-2 px-2 justify-end">
        <div class="flex flex-wrap gap-2 text-black">
            <select id="sortSelect" class="dashboard-filter px-3 py-1 rounded shadow-sm">
                <option value="all">Sort: All</option>
                <option value="time">Sort: Soonest</option>
                <option value="distance">Sort: Nearest</option>
            </select>
            <select id="paceFilter" class="dashboard-filter px-3 py-1 rounded shadow-sm">
                <option value="all">Pace: All</option>
                <option value="12:00/km - 11:00/km">12:00/km - 11:00/km</option>
                <option value="11:00/km - 10:00/km">11:00/km - 10:00/km</option>
                <option value="10:00/km - 9:00/km">10:00/km - 9:00/km</option>
                <option value="9:00/km - 8:00/km">9:00/km - 8:00/km</option>
                <option value="8:00/km - 7:00/km">8:00/km - 7:00/km</option>
                <option value="7:00/km - 6:00/km">7:00/km - 6:00/km</option>
                <option value="6:00/km - 5:00/km">6:00/km - 5:00/km</option>
                <option value="5:00/km - 4:00/km">5:00/km - 4:00/km</option>
                <option value="4:00/km - 3:00/km">4:00/km - 3:00/km</option>
            </select>
            <select id="searchLocation" class="dashboard-filter px-3 py-1 rounded w-48 shadow-sm">
                <option value="">All locations</option>
                @foreach($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </select>
        </div>
    </div>

<div id="upcomingSection" class="w-full max-w-[1680px] pt-4" style="display:block;">
        <div id="carouselWrapper" class="overflow-x-hidden overflow-y-visible w-full max-w-[1680px] py-5">
        <div id="carouselInner" class="flex transition-transform duration-500 ease-in-out">
            @if($upcomingSessions->isEmpty())
                <div class="min-w-full px-4">
                    <p class="text-center text-gray-300">No upcoming sessions.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 min-w-full px-4">
                    @foreach ($upcomingSessions as $session)
                        <div class="session-card bg-white rounded-3xl border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)] overflow-hidden hover:scale-105 hover:z-20 transition-transform duration-300"
                             data-distance="{{ $session->distance_km ?? '' }}"
                             data-start="{{ \Carbon\Carbon::parse($session->start_time)->timestamp }}"
                             data-location="{{ strtolower($session->location_name) }}"
                             data-pace="{{ $session->average_pace }}">

                            <div class="h-[240px] flex">

                                <!-- Left section -->
                                <div class="w-1/3 bg-white text-black flex flex-col items-center justify-center p-3">
                                    <p class="text-sm font-semibold">{{ \Carbon\Carbon::parse($session->start_time)->format('M d') }}</p>
                                    <p class="text-sm">{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}</p>
                                </div>

                                <!-- Right section -->
                                <div class="w-2/3 bg-[#779286] text-white p-3 flex flex-col justify-center">
                                    <p class="text-left text-sm mb-0.5">Activity: {{ $session->activity }}</p>
                                    <p class="text-left text-sm mb-0.5">Organiser: {{ $session->user->name ?? 'Unknown' }}</p>
                                    @php
                                        $locName = $session->location_name;
                                        $looksLikeCoords = preg_match('/^Lat\\s*-?\\d+(?:\\.\\d+)?[,\\s]*Lng\\s*-?\\d+(?:\\.\\d+)?$/i', $locName);
                                        $displayLocation = $looksLikeCoords ? 'Location not set (add district, state)' : $locName;
                                    @endphp
                                    <p class="text-left text-sm mb-0.5">Location: {{ $displayLocation }}</p>
                                    @if(!is_null($session->distance_km))
                                        <p class="text-left text-sm mb-0.5">Distance: {{ $session->distance_km }} km away</p>
                                    @else
                                        <p class="text-left text-sm mb-0.5 text-gray-200/80">Distance: unavailable</p>
                                    @endif
                                    <p class="text-left text-sm mb-0.5">Pace: {{ $session->average_pace }}</p>
                                    <p class="text-left text-sm mb-1">Duration: {{ $session->duration }}</p>

                                    <div class="flex flex-wrap justify-center gap-2 mt-1">
                                        <button
                                            type="button"
                                            onclick="openModal('details-{{ $session->session_id }}')"
                                            class="bg-gray-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[50px] text-center flex items-center justify-center">
                                            View
                                        </button>
                                        @if($session->user_id === Auth::id())
                                            <button
                                                type="button"
                                                onclick="openModal('edit-{{ $session->session_id }}')"
                                                class="bg-gray-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[50px] text-center inline-flex items-center justify-center">
                                                Edit
                                            </button>
                                        @endif
                                        @if($session->user_id !== Auth::id())
                                            @if($session->user_joined)
                                                <form action="{{ route('sessions.leave', ['session_id' => $session->session_id]) }}" method="POST" onsubmit="return confirm('Leave this session?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[70px]">
                                                        Unjoin
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('sessions.join', ['session_id' => $session->session_id]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-gray-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[50px]">
                                                        Join
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @if($session->user_id === Auth::id())
                                            @if(is_null($session->started_at))
                                                <form action="{{ route('sessions.start', $session->session_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-green-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[70px]">
                                                        Start
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('sessions.stop', $session->session_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-red-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[70px]">
                                                        Stop
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST" onsubmit="return confirm('Delete this session?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[70px]">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Next button -->
    <div id="carouselNav" class="absolute left-1/2 -translate-x-1/2">
        <button id="prevButton" class="w-10 h-10 rounded-full bg-white text-black text-xl font-bold shadow hover:scale-110 transition">
            &lt;
        </button>
        <button id="nextButton" class="w-10 h-10 rounded-full bg-white text-black text-xl font-bold shadow hover:scale-110 transition">
            &gt;
        </button>
    </div>
</div>

<!-- Modals for Upcoming Sessions -->
@foreach ($upcomingSessions as $session)
    <div id="details-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('details-{{ $session->session_id }}')"></div>

        <div class="relative bg-white text-black rounded-xl p-6 w-full max-w-2xl z-10">
            <div class="flex justify-between items-start gap-4">
                <div>
                    <h2 class="text-xl font-bold mb-1">Session Details</h2>
                    <p class="text-sm text-gray-700">Owner: {{ $session->user->name ?? 'Unknown' }}</p>
                </div>
                <button type="button" onclick="closeModal('details-{{ $session->session_id }}')" class="px-3 py-1 bg-gray-200 text-black rounded hover:bg-gray-300 text-sm">Close</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                <div>
                    <p class="font-semibold">Location</p>
                    <p>{{ $session->location_name }}</p>
                    @if(!is_null($session->distance_km))
                        <p class="text-sm text-gray-700">~{{ $session->distance_km }} km from you</p>
                    @endif
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($session->location_name) }}"
                       target="_blank"
                       rel="noopener"
                       class="inline-block mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-semibold">
                        View on Map
                    </a>
                </div>
                <div>
                    <p class="font-semibold">Timing</p>
                    <p>Start: {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y h:i A') }}</p>
                    <p>End: {{ \Carbon\Carbon::parse($session->end_time)->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="font-semibold">Pace</p>
                    <p>{{ $session->average_pace }}</p>
                </div>
                <div>
                    <p class="font-semibold">Duration</p>
                    <p>{{ $session->duration }}</p>
                </div>
            </div>

            <div class="mt-4">
                <p class="font-semibold mb-2">Joined Users</p>
                @if($session->joinedUsers->isEmpty())
                    <p class="text-sm text-gray-700">No one has joined this session yet.</p>
                @else
                    <ul class="list-disc ml-5 space-y-1">
                        @foreach($session->joinedUsers as $joined)
                            <li>{{ $joined->user->name ?? 'Unknown' }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="mt-4">
                <p class="font-semibold mb-2">Reviews</p>
                @if($session->reviews->isEmpty())
                    <p class="text-sm text-gray-700">No reviews yet.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($session->reviews as $review)
                            <li class="border rounded px-3 py-2">
                                <p class="font-semibold">{{ $review->rating }}/5</p>
                                <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    @if($session->user_id === Auth::id())
        <div id="edit-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden">
            <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('edit-{{ $session->session_id }}')"></div>

            <div class="relative bg-white text-black rounded-xl shadow-xl p-6 w-full max-w-xl z-10">
                <div class="flex justify-between items-start gap-4">
                    <h2 class="text-xl font-bold">Edit Session</h2>
                    <button type="button" onclick="closeModal('edit-{{ $session->session_id }}')" class="px-3 py-1 bg-gray-200 text-black rounded hover:bg-gray-300 text-sm">Close</button>
                </div>

                <form method="POST" action="{{ route('sessions.update', $session->session_id) }}" class="space-y-4 mt-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="location_name_{{ $session->session_id }}">Location (location name, district)</label>
                        <input type="text" id="location_name_{{ $session->session_id }}" name="location_name" value="{{ old('location_name', $session->location_name) }}" class="w-full border rounded px-3 py-2" placeholder="e.g., Merlimau, Jasin" required>
                        <button type="button" data-edit-geo="{{ $session->session_id }}" class="mt-2 px-3 py-1 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">Use my location (auto-fill)</button>
                        <p data-edit-session="{{ $session->session_id }}" data-edit-field="status" class="text-xs text-gray-600 mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="session_state_{{ $session->session_id }}">State (Malaysia)</label>
                        <select id="session_state_{{ $session->session_id }}" name="state" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select your state</option>
                            @php
                                $currentState = null;
                                foreach ($states as $state) {
                                    if (stripos($session->location_name ?? '', $state) !== false) {
                                        $currentState = $state;
                                        break;
                                    }
                                }
                            @endphp
                            @foreach($states as $state)
                                <option value="{{ $state }}" {{ old('state', $currentState) === $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" id="location_lat_{{ $session->session_id }}" name="location_lat" value="{{ old('location_lat', $session->location_lat) }}" data-edit-session="{{ $session->session_id }}" data-edit-field="lat">
                    <input type="hidden" id="location_lng_{{ $session->session_id }}" name="location_lng" value="{{ old('location_lng', $session->location_lng) }}" data-edit-session="{{ $session->session_id }}" data-edit-field="lng">

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="location_area_{{ $session->session_id }}">Area</label>
                        <input type="text" id="location_area_{{ $session->session_id }}" name="location_area" value="{{ old('location_area', $session->location_name) }}" data-edit-session="{{ $session->session_id }}" data-edit-field="area" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="start_time_{{ $session->session_id }}">Start Time</label>
                        <input type="datetime-local" id="start_time_{{ $session->session_id }}" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($session->start_time)->format('Y-m-d\\TH:i')) }}" data-edit-session="{{ $session->session_id }}" data-edit-field="start_time" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="end_time_{{ $session->session_id }}">End Time</label>
                        <input type="datetime-local" id="end_time_{{ $session->session_id }}" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($session->end_time)->format('Y-m-d\\TH:i')) }}" data-edit-session="{{ $session->session_id }}" data-edit-field="end_time" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="average_pace_{{ $session->session_id }}">Average Pace</label>
                        <select id="average_pace_{{ $session->session_id }}" name="average_pace" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select pace</option>
                            <option value="12:00/km - 11:00/km" {{ old('average_pace', $session->average_pace) == '12:00/km - 11:00/km' ? 'selected' : '' }}>12:00/km - 11:00/km</option>
                            <option value="11:00/km - 10:00/km" {{ old('average_pace', $session->average_pace) == '11:00/km - 10:00/km' ? 'selected' : '' }}>11:00/km - 10:00/km</option>
                            <option value="10:00/km - 9:00/km" {{ old('average_pace', $session->average_pace) == '10:00/km - 9:00/km' ? 'selected' : '' }}>10:00/km - 9:00/km</option>
                            <option value="9:00/km - 8:00/km" {{ old('average_pace', $session->average_pace) == '9:00/km - 8:00/km' ? 'selected' : '' }}>9:00/km - 8:00/km</option>
                            <option value="8:00/km - 7:00/km" {{ old('average_pace', $session->average_pace) == '8:00/km - 7:00/km' ? 'selected' : '' }}>8:00/km - 7:00/km</option>
                            <option value="7:00/km - 6:00/km" {{ old('average_pace', $session->average_pace) == '7:00/km - 6:00/km' ? 'selected' : '' }}>7:00/km - 6:00/km</option>
                            <option value="6:00/km - 5:00/km" {{ old('average_pace', $session->average_pace) == '6:00/km - 5:00/km' ? 'selected' : '' }}>6:00/km - 5:00/km</option>
                            <option value="5:00/km - 4:00/km" {{ old('average_pace', $session->average_pace) == '5:00/km - 4:00/km' ? 'selected' : '' }}>5:00/km - 4:00/km</option>
                            <option value="4:00/km - 3:00/km" {{ old('average_pace', $session->average_pace) == '4:00/km - 3:00/km' ? 'selected' : '' }}>4:00/km - 3:00/km</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="activity_{{ $session->session_id }}">Activity</label>
                        <select id="activity_{{ $session->session_id }}" name="activity" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select activity</option>
                            <option value="5km" {{ old('activity', $session->activity) == '5km' ? 'selected' : '' }}>5km</option>
                            <option value="10km" {{ old('activity', $session->activity) == '10km' ? 'selected' : '' }}>10km</option>
                            <option value="Long Run" {{ old('activity', $session->activity) == 'Long Run' ? 'selected' : '' }}>Long Run</option>
                            <option value="Interval" {{ old('activity', $session->activity) == 'Interval' ? 'selected' : '' }}>Interval</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1" for="duration_{{ $session->session_id }}">Duration</label>
                        <input type="text" id="duration_{{ $session->session_id }}" name="duration" value="{{ old('duration', $session->duration) }}" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                        <button type="button" onclick="closeModal('edit-{{ $session->session_id }}')" class="px-4 py-2 bg-gray-200 text-black rounded hover:bg-gray-300">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div id="modal-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black opacity-60"></div>

        <div class="relative bg-white text-black rounded-xl p-6 w-96 z-10">
            <h2 class="text-lg font-bold mb-3">Joined Users</h2>
            @php
                $locName = $session->location_name;
                $looksLikeCoords = preg_match('/^Lat\\s*-?\\d+(?:\\.\\d+)?[,\\s]*Lng\\s*-?\\d+(?:\\.\\d+)?$/i', $locName);
                $displayLocation = $looksLikeCoords ? 'Location not set (add district, state)' : $locName;
            @endphp
            <p class="text-sm text-gray-700 mb-2">
                Location: {{ $displayLocation }}
                @if(!is_null($session->distance_km))
                    • ~{{ $session->distance_km }} km from you
                @endif
            </p>

            @if ($session->joinedUsers->isEmpty())
                <p>No one has joined this session yet.</p>
            @else
                <ul class="list-disc pl-5 mb-4">
                    @foreach ($session->joinedUsers as $joined)
                        <li>{{ $joined->user->name ?? 'Unknown' }}</li>
                    @endforeach
                </ul>
            @endif

            @php $locationQuery = urlencode($session->location_name); @endphp
            <div class="mb-4">
                <a href="https://www.google.com/maps/search/?api=1&query={{ $locationQuery }}"
                   target="_blank"
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200 text-sm">
                    View on Map
                </a>
            </div>

            <button
                onclick="closeModal('modal-{{ $session->session_id }}')"
                class="absolute top-2 right-2 text-black hover:text-red-600 font-bold text-lg">&times;
            </button>
        </div>
    </div>
@endforeach

<!-- Past Sessions -->
<div id="pastSection" class="absolute top-36 left-10 right-10 bottom-10 z-40 bg-black/70 backdrop-blur border border-white/20 px-4 py-4 overflow-y-auto hidden rounded-xl" style="display:none;">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Session History</h2>
        <span class="text-sm text-gray-300">Sessions you created or joined</span>
    </div>
    @if($pastSessions->isEmpty())
        <p class="text-sm text-gray-300">No past sessions yet.</p>
    @else
        <div class="space-y-3" id="historyList">
            @foreach($pastSessions as $session)
                <div class="bg-white text-black rounded-xl p-3 shadow flex flex-col md:flex-row md:items-center md:justify-between gap-2 history-item"
                     data-distance="{{ $session->distance_km ?? '' }}"
                     data-start="{{ \Carbon\Carbon::parse($session->start_time)->timestamp }}"
                     data-location="{{ strtolower($session->location_name) }}"
                     data-pace="{{ $session->average_pace }}">
                    <div>
                        @php
                            $locName = $session->location_name;
                            $looksLikeCoords = preg_match('/^Lat\\s*-?\\d+(?:\\.\\d+)?[,\\s]*Lng\\s*-?\\d+(?:\\.\\d+)?$/i', $locName);
                            $displayLocation = $looksLikeCoords ? 'Location not set (add district, state)' : $locName;
                        @endphp
                        <p class="font-semibold">Activity: {{ $session->activity }}</p>
                        <p class="font-semibold">{{ $displayLocation }} ({{ \Carbon\Carbon::parse($session->start_time)->format('M d, h:i A') }})</p>
                        <p class="text-sm text-gray-700">Pace: {{ $session->average_pace }} • Duration: {{ $session->duration }}</p>
                        @if($session->distance_km)
                            <p class="text-xs text-gray-600">~{{ $session->distance_km }} km away</p>
                        @endif
                        @if($session->reviews->isNotEmpty())
                            <p class="text-xs text-gray-700 mt-1">Reviews:</p>
                            <ul class="text-xs text-gray-800 list-disc ml-4">
                                @foreach($session->reviews as $review)
                                    <li>{{ $review->rating }}/5 - {{ $review->comment }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2 items-center">
                        @php
                            $now = \Carbon\Carbon::now();
                            $canReview = $session->user_joined && ($session->end_time < $now || !is_null($session->completed_at));
                        @endphp
                        @if($session->user_id === Auth::id() && is_null($session->completed_at))
                            <form action="{{ route('sessions.complete', $session->session_id) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-green-700 text-white rounded text-xs">Mark completed</button>
                            </form>
                        @endif
                        @if($canReview)
                            <form action="{{ route('sessions.review', $session->session_id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <div class="flex items-center gap-1" data-star-rating>
                                    @for($i=1; $i<=5; $i++)
                                        <button type="button" class="star-btn text-yellow-500 text-lg" data-value="{{ $i }}">☆</button>
                                    @endfor
                                    <input type="hidden" name="rating" value="1" class="star-input">
                                </div>
                                <input name="comment" type="text" placeholder="Add review" class="border rounded px-3 py-2 text-sm w-64">
                                <button class="px-4 py-2 bg-blue-700 text-white rounded text-sm min-w-[90px] text-center">Submit</button>
                            </form>
                        @endif
                        @if($session->user_id === Auth::id())
                            <form action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST" onsubmit="return confirm('Delete this session?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-2 bg-red-700 text-white rounded text-sm min-w-[90px] text-center">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Create Session Modal -->
<div id="createSessionModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('createSessionModal')"></div>

    <div class="relative bg-white text-black rounded-xl p-6 w-[400px] z-60">
        <button type="button" onclick="closeModal('createSessionModal')" class="absolute top-2 right-2 text-xl font-bold text-gray-700 hover:text-red-600">&times;</button>

        <h2 class="text-lg font-bold mb-4">Create New Session</h2>

        <form action="{{ route('sessions.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="location_name" class="block text-sm font-semibold mb-1">Location (location name, district)</label>
                <input type="text" name="location_name" id="location_name" class="w-full p-2 border rounded" placeholder="e.g., Merlimau, Jasin" required>
                <button type="button" id="modalUseMyLocation" class="mt-2 px-3 py-1 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">Use my location (auto-fill)</button>
                <p id="modalGeoStatus" class="text-xs text-gray-700 mt-1"></p>
            </div>

            <div class="mb-3">
                <label for="session_state" class="block text-sm font-semibold mb-1">State (Malaysia)</label>
                <select id="session_state" name="state" class="w-full p-2 border rounded" required>
                    <option value="">Select your state</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
            </div>

                <input type="hidden" name="location_lat" id="location_lat" value="{{ old('location_lat') }}">
                <input type="hidden" name="location_lng" id="location_lng" value="{{ old('location_lng') }}">

                <div>
                    <label for="location_area" class="block text-sm font-semibold mb-1">Area</label>
                    <input type="text" name="location_area" id="location_area" value="{{ old('location_area') }}" class="w-full p-2 border rounded bg-gray-100" placeholder="Use my location to auto-fill" readonly>
                </div>

            <div class="mb-3">
                <label for="start_time" class="block text-sm font-semibold mb-1">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" class="w-full p-2 border rounded" required>
            </div>

            <div class="mb-3">
                <label for="end_time" class="block text-sm font-semibold mb-1">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="w-full p-2 border rounded" required>
            </div>

            <div class="mb-3">
                <label for="average_pace" class="block text-sm font-semibold mb-1">Average Pace</label>
                <select name="average_pace" id="average_pace" class="w-full p-2 border rounded" required>
                    <option value="">Select pace</option>
                    <option value="12:00/km - 11:00/km">12:00/km - 11:00/km</option>
                    <option value="11:00/km - 10:00/km">11:00/km - 10:00/km</option>
                    <option value="10:00/km - 9:00/km">10:00/km - 9:00/km</option>
                    <option value="9:00/km - 8:00/km">9:00/km - 8:00/km</option>
                    <option value="8:00/km - 7:00/km">8:00/km - 7:00/km</option>
                    <option value="7:00/km - 6:00/km">7:00/km - 6:00/km</option>
                    <option value="6:00/km - 5:00/km">6:00/km - 5:00/km</option>
                    <option value="5:00/km - 4:00/km">5:00/km - 4:00/km</option>
                    <option value="4:00/km - 3:00/km">4:00/km - 3:00/km</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="activity" class="block text-sm font-semibold mb-1">Activity</label>
                <select name="activity" id="activity" class="w-full p-2 border rounded" required>
                    <option value="">Select activity</option>
                    <option value="5km">5km</option>
                    <option value="10km">10km</option>
                    <option value="Long Run">Long Run</option>
                    <option value="Interval">Interval</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Duration</label>
                <p id="durationDisplay" class="text-sm text-gray-700">-</p>
                <input type="text" name="duration" id="duration" class="hidden" readonly required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('createSessionModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-black">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 rounded hover:bg-green-700 text-white">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Buddy Match + Create Session Actions -->
<div class="absolute right-10 bottom-10 z-30 flex flex-col items-end gap-3">
    <button id="buddyMatchButton" onclick="openModal('buddyMatchModal')" class="group flex items-center hover:scale-110 transition-transform">
        <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center">
            <span class="text-white text-sm font-semibold">Buddy Match</span>
        </div>
        <div
            class="w-[60px] h-[60px] -ml-[30px] bg-gray-900 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-800 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M22 2 11 13" />
                <path d="M22 2 15 22 11 13 2 9z" />
            </svg>
        </div>
    </button>

    <button type="button" data-weekly-schedule-btn onclick="openModal('weeklyScheduleModal')" class="group flex items-center hover:scale-110 transition-transform">
        <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center">
            <span class="text-white text-sm font-semibold">Weekly Schedule</span>
        </div>
        <div
            class="w-[60px] h-[60px] -ml-[30px] bg-gray-800 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-700 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 19h14a2 2 0 002-2v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2z" />
            </svg>
        </div>
    </button>

    <button type="button" onclick="openModal('createSessionModal')" class="group flex items-center">
        <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center">
            <span class="text-white text-sm font-semibold">Create Session</span>
        </div>
        <div
            class="w-[60px] h-[60px] -ml-[30px] bg-gray-800 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-700 transition duration-200">
            <span class="text-white text-3xl font-bold leading-none">+</span>
        </div>
    </button>
</div>

<!-- Logout Button -->
<div class="absolute bottom-10 left-10 z-30">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="relative px-6 py-2 rounded-lg overflow-hidden shadow-lg transition-all duration-300 group">
            <span class="absolute inset-0 bg-gradient-to-r from-black to-white opacity-50 group-hover:opacity-30 rounded-lg"></span>
            <span class="relative z-10 text-white font-semibold tracking-wide">Logout</span>
        </button>
    </form>
</div>

<!-- Buddy Match Modal -->
<div id="buddyMatchModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('buddyMatchModal')"></div>

    <div class="relative bg-white text-black rounded-xl p-6 w-[500px] max-h-[600px] overflow-y-auto z-60">
        <button type="button" onclick="closeModal('buddyMatchModal')" class="absolute top-2 right-2 text-xl font-bold text-gray-700 hover:text-red-600">&times;</button>

        <h2 class="text-2xl font-bold mb-4">Find Running Buddies</h2>
        <p class="text-gray-600 mb-4">Connect with runners who share your pace and goals!</p>

        <div id="buddyList" class="space-y-3">
            <p class="text-center text-gray-500">Loading buddies...</p>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-800">Tip: Connect via Telegram to get instant buddy requests!</p>
            @if(Auth::user()->telegram_id)
                <button type="button" onclick="unlinkTelegram()" class="mt-2 w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                    Unlink Telegram Account
                </button>
            @else
                <button type="button" onclick="linkTelegram()" class="mt-2 w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Link Telegram Account
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('profileModal')"></div>
    <div class="relative bg-white text-black rounded-xl shadow-2xl max-w-4xl w-full overflow-hidden z-60">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Your Profile</h3>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('profileModal'); openModal('profileEditModal')" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                    Edit Profile
                </button>
                <button onclick="closeModal('profileModal')" class="text-slate-500 hover:text-black text-xl">&times;</button>
            </div>
        </div>
        <iframe
            src="{{ route('user.profile', ['embed' => 1]) }}"
            title="Profile"
            class="w-full h-[360px] border-0 bg-white"
        ></iframe>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="profileEditModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('profileEditModal')"></div>
    <div class="relative bg-white text-black rounded-xl shadow-2xl max-w-4xl w-full overflow-hidden z-60">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Edit Profile</h3>
            <button onclick="closeModal('profileEditModal')" class="text-slate-500 hover:text-black text-xl">&times;</button>
        </div>
        <iframe
            src="{{ route('user.profile.edit', ['embed' => 1]) }}"
            title="Edit Profile"
            class="w-full h-[70vh] border-0 bg-white"
        ></iframe>
    </div>
</div>

<!-- Weekly Schedule Modal -->
<div id="weeklyScheduleModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('weeklyScheduleModal')"></div>

    <div class="relative bg-white text-black rounded-xl p-6 w-[560px] max-h-[620px] overflow-y-auto z-60">
        <button type="button" onclick="closeModal('weeklyScheduleModal')" class="absolute top-2 right-2 text-xl font-bold text-gray-700 hover:text-red-600">&times;</button>

        <h2 class="text-2xl font-bold mb-2">Your Schedule</h2>

        @if($weeklySchedule->isEmpty())
            <p class="text-center text-gray-500">No sessions scheduled this week.</p>
        @else
            <div class="space-y-3">
                @foreach($weeklySchedule as $item)
                    @php
                        $session = $item['session'];
                        $start = is_object($session->start_time) ? $session->start_time : \Carbon\Carbon::parse($session->start_time);
                        $end = is_object($session->end_time) ? $session->end_time : \Carbon\Carbon::parse($session->end_time);
                        $hasConflict = $item['conflict'];
                        $status = $session->started_at && is_null($session->completed_at) ? 'Ongoing' : 'Not started yet';
                        $participants = ($session->joined_users_count ?? 0) + 1;
                    @endphp
                    <div class="border-2 rounded-lg p-3 {{ $hasConflict ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $start->format('D, M d') }} · {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}</p>
                                <p class="text-sm text-gray-700">Activity: {{ $session->activity ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-700">Location: {{ $session->location_name ?? 'Not set' }}</p>
                                <p class="text-sm text-gray-700">Role: {{ $item['role'] }}</p>
                                <p class="text-sm text-gray-700">Status: {{ $status }}</p>
                                <p class="text-sm text-gray-700">Participants: {{ $participants }}</p>
                            </div>
                            @if($hasConflict)
                                <span class="text-xs font-semibold text-red-700 bg-red-200 px-2 py-1 rounded">OVERLAP</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-600 mt-2">
                            Pace: {{ $session->average_pace ?? 'N/A' }} · Duration: {{ $session->duration ?? 'N/A' }}
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('sessions.show', $session->session_id) }}" class="px-3 py-1 bg-gray-800 text-white rounded text-xs">View</a>
                            @if(strtolower($item['role']) === 'participant')
                                <form action="{{ route('sessions.leave', ['session_id' => $session->session_id]) }}" method="POST" onsubmit="return confirm('Leave this session?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-700 text-white rounded text-xs">Unjoin</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    (function() {
        const nextButton = document.getElementById('nextButton');
        const prevButton = document.getElementById('prevButton');
        const carouselInner = document.getElementById('carouselInner');
        const upcomingSection = document.getElementById('upcomingSection');
        const pastSection = document.getElementById('pastSection');
        const tabUpcoming = document.getElementById('tab-upcoming');
        const tabPast = document.getElementById('tab-past');
        const sortSelect = document.getElementById('sortSelect');
        const paceFilter = document.getElementById('paceFilter');
        const searchLocation = document.getElementById('searchLocation');
        const historyList = document.getElementById('historyList');

        let currentIndex = 0;
        const totalPages = carouselInner ? carouselInner.children.length : 0;

        function updateCarousel(step) {
            if (!carouselInner || totalPages === 0) return;
            currentIndex = (currentIndex + step + totalPages) % totalPages;
            const offset = currentIndex * carouselInner.clientWidth;
            carouselInner.style.transform = 'translateX(-' + offset + 'px)';
        }

        if (nextButton) nextButton.addEventListener('click', function() { updateCarousel(1); });
        if (prevButton) prevButton.addEventListener('click', function() { updateCarousel(-1); });
        window.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') updateCarousel(1);
            if (e.key === 'ArrowLeft') updateCarousel(-1);
        });

        function applyFilters() {
            var paceRange = paceFilter ? paceFilter.value : 'all';
            var query = searchLocation ? (searchLocation.value || '').toLowerCase().trim() : '';
            var sortBy = sortSelect ? sortSelect.value : 'time';
            var now = new Date();
            var startOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            var nowTs = Math.floor(now.getTime() / 1000);
            var startOfDayTs = Math.floor(startOfDay.getTime() / 1000);
            var fiveDaysAhead = startOfDayTs + (5 * 24 * 60 * 60);

            function paceMatches(paceValue, rangeValue) {
                if (rangeValue === 'all') return true;
                if (!paceValue) return false;
                return String(paceValue).trim() === String(rangeValue).trim();
            }

            function filterNodes(nodeList) {
                nodeList.forEach(function(node) {
                    var dist = parseFloat(node.dataset.distance || '9999');
                    var loc = node.dataset.location || '';
                    var startTs = parseInt(node.dataset.start || '0', 10);
                    var isUpcomingCard = node.classList.contains('session-card');
                    var withinDistance = true;
                    var matchesSearch = !query || loc.indexOf(query) !== -1;
                    var matchesPace = paceMatches(node.dataset.pace, paceRange);
                    var withinSoonestWindow = true;
                    if (sortBy === 'time' && isUpcomingCard) {
                        withinSoonestWindow = startTs >= startOfDayTs && startTs <= fiveDaysAhead;
                    }
                    var withinNearestDistance = true;
                    if (sortBy === 'distance' && isUpcomingCard) {
                        withinNearestDistance = dist <= 30;
                    }
                    node.style.display = withinDistance && matchesSearch && matchesPace && withinSoonestWindow && withinNearestDistance ? '' : 'none';
                });
            }

            filterNodes(document.querySelectorAll('.session-card'));
            filterNodes(document.querySelectorAll('.history-item'));

            function sortNodes(nodeList) {
                var items = Array.from(nodeList);
                items.sort(function(a, b) {
                    if (sortBy === 'all') return 0;
                    if (sortBy === 'distance') {
                        var da = parseFloat(a.dataset.distance || '9999');
                        var db = parseFloat(b.dataset.distance || '9999');
                        return da - db;
                    }
                    var ta = parseInt(a.dataset.start || '0', 10);
                    var tb = parseInt(b.dataset.start || '0', 10);
                    return ta - tb;
                });
                items.forEach(function(node) { node.parentNode.appendChild(node); });
            }

            if (sortBy !== 'all') {
                sortNodes(document.querySelectorAll('.session-card'));
                if (historyList) sortNodes(historyList.querySelectorAll('.history-item'));
            }
        }

        if (paceFilter) paceFilter.addEventListener('change', applyFilters);
        if (sortSelect) sortSelect.addEventListener('change', applyFilters);
        if (searchLocation) {
            var isSelect = searchLocation.tagName === 'SELECT';
            var handler = function() {
                clearTimeout(searchLocation._t);
                searchLocation._t = setTimeout(applyFilters, 150);
            };
            searchLocation.addEventListener(isSelect ? 'change' : 'input', handler);
        }

        function showUpcoming() {
            if (!upcomingSection || !pastSection) return;
            upcomingSection.style.display = 'block';
            pastSection.style.display = 'none';
            upcomingSection.classList.remove('hidden');
            pastSection.classList.add('hidden');
            if (tabUpcoming) { tabUpcoming.classList.add('bg-white', 'text-black'); tabUpcoming.classList.remove('bg-gray-800', 'text-white'); }
            if (tabPast) { tabPast.classList.add('bg-gray-800', 'text-white'); tabPast.classList.remove('bg-white', 'text-black'); }
            applyFilters();
            alignCarouselNav();
        }

        function showPast() {
            if (!upcomingSection || !pastSection) return;
            upcomingSection.style.display = 'none';
            pastSection.style.display = 'block';
            upcomingSection.classList.add('hidden');
            pastSection.classList.remove('hidden');
            if (tabPast) { tabPast.classList.add('bg-white', 'text-black'); tabPast.classList.remove('bg-gray-800', 'text-white'); }
            if (tabUpcoming) { tabUpcoming.classList.add('bg-gray-800', 'text-white'); tabUpcoming.classList.remove('bg-white', 'text-black'); }
            applyFilters();
        }

        if (tabUpcoming) tabUpcoming.addEventListener('click', function(e) { e.preventDefault(); showUpcoming(); });
        if (tabPast) tabPast.addEventListener('click', function(e) { e.preventDefault(); showPast(); });
        function alignCarouselNav() {
            if (!upcomingSection || !upcomingSection.classList.contains('hidden')) {
                const nav = document.getElementById('carouselNav');
                const btn = document.querySelector('[data-weekly-schedule-btn]');
                if (!nav || !btn || !upcomingSection) return;
                const btnRect = btn.getBoundingClientRect();
                const sectionRect = upcomingSection.getBoundingClientRect();
                const targetTop = btnRect.top - sectionRect.top;
                nav.style.top = targetTop + 'px';
            }
        }

        function setupStarRatings() {
            var groups = document.querySelectorAll('[data-star-rating]');
            groups.forEach(function(group) {
                var buttons = group.querySelectorAll('.star-btn');
                var input = group.querySelector('.star-input');
                if (!input || buttons.length === 0) return;

                function setStars(value) {
                    buttons.forEach(function(btn) {
                        var starValue = parseInt(btn.getAttribute('data-value') || '0', 10);
                        btn.textContent = starValue <= value ? '★' : '☆';
                    });
                    input.value = String(value);
                }

                buttons.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var value = parseInt(btn.getAttribute('data-value') || '1', 10);
                        setStars(value);
                    });
                });

                setStars(parseInt(input.value || '1', 10));
            });
        }

        window.showUpcoming = showUpcoming;
        window.showPast = showPast;
        window.addEventListener('resize', alignCarouselNav);

        function findEditField(sessionId, field) {
            return document.querySelector('[data-edit-session="' + sessionId + '"][data-edit-field="' + field + '"]');
        }

        var editGeocodeUrl = "{{ route('reverse.geocode') }}";

        function setupEditModals() {
            var geoButtons = document.querySelectorAll('[data-edit-geo]');
            geoButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var sessionId = btn.getAttribute('data-edit-geo');
                    var statusEl = findEditField(sessionId, 'status');
                    var latInput = findEditField(sessionId, 'lat');
                    var lngInput = findEditField(sessionId, 'lng');
                    var areaInput = findEditField(sessionId, 'area');

                    function setStatus(msg, isError) {
                        if (!statusEl) return;
                        statusEl.textContent = msg;
                        statusEl.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-700');
                    }

                    function formatCoord(value) {
                        var num = Number(value);
                        if (!isFinite(num)) return '';
                        return num.toFixed(5).replace(/\.?0+$/, '');
                    }

                    function updateArea() {
                        if (!areaInput) return;
                        var latText = formatCoord(latInput && latInput.value);
                        var lngText = formatCoord(lngInput && lngInput.value);
                        if (!latText || !lngText || (latText === '0' && lngText === '0')) {
                            areaInput.value = '';
                            return;
                        }
                        areaInput.value = latText + ', ' + lngText;
                    }

                    function resolveArea() {
                        var latValue = Number(latInput && latInput.value);
                        var lngValue = Number(lngInput && lngInput.value);
                        if (!isFinite(latValue) || !isFinite(lngValue)) return;
                        setStatus('Resolving area...', false);
                        fetch(editGeocodeUrl + '?lat=' + encodeURIComponent(latValue) + '&lon=' + encodeURIComponent(lngValue))
                            .then(function(response) {
                                if (!response.ok) throw new Error('Bad response');
                                return response.json();
                            })
                            .then(function(data) {
                                if (data && data.name && areaInput) {
                                    areaInput.value = data.name;
                                    setStatus('Area resolved.', false);
                                }
                            })
                            .catch(function() {
                                setStatus('Unable to resolve area.', true);
                            });
                    }

                    if (!navigator.geolocation) {
                        setStatus('Geolocation is not supported in this browser.', true);
                        return;
                    }

                    setStatus('Requesting location...', false);
                    navigator.geolocation.getCurrentPosition(function(pos) {
                        var latitude = pos.coords.latitude;
                        var longitude = pos.coords.longitude;
                        if (latInput) latInput.value = latitude.toFixed(7);
                        if (lngInput) lngInput.value = longitude.toFixed(7);
                        updateArea();
                        resolveArea();
                        setStatus('Location captured.', false);
                    }, function(err) {
                        setStatus('Unable to fetch location: ' + err.message, true);
                    }, { enableHighAccuracy: true, timeout: 10000 });
                });
            });

            var areaInputs = document.querySelectorAll('[data-edit-field="area"]');
            areaInputs.forEach(function(areaInput) {
                var sessionId = areaInput.getAttribute('data-edit-session');
                var latInput = findEditField(sessionId, 'lat');
                var lngInput = findEditField(sessionId, 'lng');

                function formatCoord(value) {
                    var num = Number(value);
                    if (!isFinite(num)) return '';
                    return num.toFixed(5).replace(/\.?0+$/, '');
                }

                function updateArea() {
                    var latText = formatCoord(latInput && latInput.value);
                    var lngText = formatCoord(lngInput && lngInput.value);
                    if (!latText || !lngText || (latText === '0' && lngText === '0')) return;
                    if (!areaInput.value.trim()) {
                        areaInput.value = latText + ', ' + lngText;
                    }
                }

                if (latInput) latInput.addEventListener('input', updateArea);
                if (lngInput) lngInput.addEventListener('input', updateArea);
                updateArea();
            });

            var startInputs = document.querySelectorAll('[data-edit-field="start_time"]');
            startInputs.forEach(function(input) {
                function syncEndMin() {
                    var sessionId = input.getAttribute('data-edit-session');
                    var endInput = findEditField(sessionId, 'end_time');
                    if (!endInput || !input.value) return;
                    if (!endInput.value || endInput.value < input.value) {
                        endInput.value = input.value;
                    }
                    endInput.min = input.value;
                }

                function applyEditMin() {
                    var nowValue = getLocalDateTimeValue(new Date());
                    input.min = nowValue;
                    if (input.value && input.value < nowValue) {
                        input.value = nowValue;
                    }
                    syncEndMin();
                }

                input.addEventListener('change', syncEndMin);
                applyEditMin();
            });
        }

        function openModal(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('hidden');
            el.style.display = 'flex';
        }
        function closeModal(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hidden');
            el.style.display = 'none';
        }
        window.openModal = openModal;
        window.closeModal = closeModal;

        // Notification
        function closeNotification() {
            var notification = document.getElementById('notification');
            if (notification) {
                notification.classList.add('animate-slideOut');
                setTimeout(function() { notification.remove(); }, 500);
            }
        }

        window.addEventListener('load', function() {
            var notification = document.getElementById('notification');
            if (notification) setTimeout(closeNotification, 5000);
            alignCarouselNav();
            setupEditModals();
            setupStarRatings();
        });

        // Buddy Match
        function loadBuddies() {
            fetch('/buddy-match')
                .then(function(response) {
                    if (!response.ok) throw new Error('HTTP error! status: ' + response.status);
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        displayBuddies(data.buddies);
                    } else {
                        document.getElementById('buddyList').innerHTML = '<p class="text-center text-red-500">Error: ' + (data.error || data.message) + '</p>';
                    }
                })
                .catch(function(error) {
                    document.getElementById('buddyList').innerHTML = '<p class="text-center text-red-500">Error: ' + error.message + '. Make sure you are logged in.</p>';
                });
        }

        function displayBuddies(buddies) {
            var buddyList = document.getElementById('buddyList');
            if (!buddyList) return;
            if (!buddies || buddies.length === 0) {
                buddyList.innerHTML = '<p class="text-center text-gray-500">No running buddies available yet.</p>';
                return;
            }
            buddyList.innerHTML = buddies.map(function(buddy) {
                var locText = 'Not set';
                var mapUrl = '';
                if (buddy.location) {
                    try {
                        var loc = typeof buddy.location === 'string' ? JSON.parse(buddy.location) : buddy.location;
                        var lat = loc.latitude != null ? loc.latitude : loc.lat;
                        var lon = loc.longitude != null ? loc.longitude : loc.lon;
                        if (isFinite(parseFloat(lat)) && isFinite(parseFloat(lon))) {
                            locText = parseFloat(lat).toFixed(4) + ', ' + parseFloat(lon).toFixed(4);
                            mapUrl = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(lat + ',' + lon);
                        }
                    } catch(e) { locText = 'Location data'; }
                }
            if (buddy.distance_km != null && isFinite(parseFloat(buddy.distance_km))) {
                locText = parseFloat(buddy.distance_km).toFixed(1) + ' km away';
            }
            var mapButton = mapUrl
                ? `<a href="${mapUrl}" target="_blank" rel="noopener" class="inline-block mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-semibold">View on Map</a>`
                : '';
            var photoHtml = buddy.photo_url
                ? `<img src="${buddy.photo_url}" alt="${buddy.name}" class="h-12 w-12 rounded-full object-cover border border-gray-300">`
                : `<div class="h-12 w-12 rounded-full bg-gray-200 text-gray-500 text-[10px] flex items-center justify-center">No photo</div>`;
            return `
                    <div class="p-4 border-2 border-gray-300 rounded-lg hover:border-green-500 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                ${photoHtml}
                                <div>
                                    <h3 class="font-bold text-lg">${buddy.name}</h3>
                                    <p class="text-sm text-gray-600">${buddy.email}</p>
                                </div>
                            </div>
                            <button onclick="sendBuddyRequest(${buddy.id})" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm font-semibold">
                                Connect
                            </button>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200 text-sm text-gray-700">
                            <p>Gender: ${buddy.gender || 'Not set'}</p>
                            <p>Pace: ${buddy.pace || 'Not set'}</p>
                            <p>Location: ${locText}</p>
                            <p>Profile: ${buddy.profile_complete ? 'Complete' : 'Incomplete'}</p>
                            ${mapButton}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function sendBuddyRequest(userId) {
            fetch('/buddy-match/send-request/' + userId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name=\"csrf-token\"]') || {}).content || '',
                    'Content-Type': 'application/json'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                alert(data.success ? 'Buddy request sent via Telegram!' : (data.message || 'Unable to send request.'));
            })
            .catch(function() {
                alert('Error sending request. Please try again.');
            });
        }
        window.sendBuddyRequest = sendBuddyRequest;

        function linkTelegram() {
            alert('Open your Telegram and search for: @StrideSyncBot\\n\\nType /start to link your account!');
            window.open('https://t.me/StrideSyncBot', '_blank');
        }
        window.linkTelegram = linkTelegram;

        function unlinkTelegram() {
            if (!confirm('Unlink your Telegram account from StrideSync?')) return;
            fetch('{{ route('user.telegram.unlink') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name=\"csrf-token\"]') || {}).content || '',
                    'Content-Type': 'application/json'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                alert(data.message || 'Telegram account unlinked.');
                if (data.success) location.reload();
            })
            .catch(function() {
                alert('Unable to unlink Telegram account.');
            });
        }
        window.unlinkTelegram = unlinkTelegram;

        var buddyMatchButton = document.getElementById('buddyMatchButton');
        if (buddyMatchButton) {
            buddyMatchButton.addEventListener('click', function() {
                loadBuddies();
                openModal('buddyMatchModal');
            });
        }

        // Geolocation for create-session modal
        var modalLat = document.getElementById('location_lat');
        var modalLng = document.getElementById('location_lng');
        var modalArea = document.getElementById('location_area');
        var modalGeocodeUrl = "{{ route('reverse.geocode') }}";
        var modalStartTime = document.getElementById('start_time');
        var modalEndTime = document.getElementById('end_time');
        var modalDuration = document.getElementById('duration');
        var modalDurationDisplay = document.getElementById('durationDisplay');
        var modalStatus = document.getElementById('modalGeoStatus');
        var modalUseMyLocation = document.getElementById('modalUseMyLocation');

        function setModalStatus(msg, isError) {
            if (!modalStatus) return;
            modalStatus.textContent = msg;
            modalStatus.className = 'text-xs mt-1 ' + (isError ? 'text-red-400' : 'text-gray-200');
        }

        function formatModalCoord(value) {
            var num = Number(value);
            if (!isFinite(num)) return '';
            return num.toFixed(5).replace(/\.?0+$/, '');
        }

        function updateModalArea() {
            if (!modalArea) return;
            var latText = formatModalCoord(modalLat && modalLat.value);
            var lngText = formatModalCoord(modalLng && modalLng.value);
            if (!latText || !lngText || (latText === '0' && lngText === '0')) {
                modalArea.value = '';
                return;
            }
            modalArea.value = latText + ', ' + lngText;
        }

        function resolveModalArea() {
            var latValue = Number(modalLat && modalLat.value);
            var lngValue = Number(modalLng && modalLng.value);
            if (!isFinite(latValue) || !isFinite(lngValue)) return;

            setModalStatus('Resolving area...', false);
            fetch(modalGeocodeUrl + '?lat=' + encodeURIComponent(latValue) + '&lon=' + encodeURIComponent(lngValue))
                .then(function(response) {
                    if (!response.ok) throw new Error('Bad response');
                    return response.json();
                })
                .then(function(data) {
                    if (data && data.name && modalArea) {
                        modalArea.value = data.name;
                        setModalStatus('Area resolved.', false);
                    }
                })
                .catch(function() {
                    setModalStatus('Unable to resolve area.', true);
                });
        }

        function fillModalFromGeo(pos) {
            var latitude = pos.coords.latitude;
            var longitude = pos.coords.longitude;
            if (modalLat) modalLat.value = latitude.toFixed(7);
            if (modalLng) modalLng.value = longitude.toFixed(7);
            updateModalArea();
            resolveModalArea();
            setModalStatus('Location captured.', false);
        }

        function modalGeoError(err) {
            setModalStatus('Unable to fetch location: ' + err.message, true);
        }

        function getLocalDateTimeValue(date) {
            function pad(value) {
                return String(value).padStart(2, '0');
            }
            return date.getFullYear()
                + '-' + pad(date.getMonth() + 1)
                + '-' + pad(date.getDate())
                + 'T' + pad(date.getHours())
                + ':' + pad(date.getMinutes());
        }

        function applyStartTimeMin() {
            if (!modalStartTime || !modalEndTime) return;
            var now = new Date();
            var minValue = getLocalDateTimeValue(now);
            modalStartTime.min = minValue;
            if (!modalStartTime.value || modalStartTime.value < minValue) {
                modalStartTime.value = minValue;
            }
            if (!modalEndTime.value || modalEndTime.value < modalStartTime.value) {
                modalEndTime.value = modalStartTime.value;
            }
            modalEndTime.min = modalStartTime.value;
            updateDuration();
        }

        function updateDuration() {
            if (!modalStartTime || !modalEndTime || !modalDuration) return;
            if (!modalStartTime.value || !modalEndTime.value) return;
            var start = new Date(modalStartTime.value);
            var end = new Date(modalEndTime.value);
            if (isNaN(start.getTime()) || isNaN(end.getTime())) return;
            var diffMs = end.getTime() - start.getTime();
            if (diffMs <= 0) {
                modalDuration.value = '';
                if (modalDurationDisplay) modalDurationDisplay.textContent = '-';
                return;
            }

            var totalMinutes = Math.round(diffMs / 60000);
            var hours = Math.floor(totalMinutes / 60);
            var minutes = totalMinutes % 60;
            var parts = [];
            if (hours > 0) parts.push(hours + ' hour' + (hours > 1 ? 's' : ''));
            if (minutes > 0) parts.push(minutes + ' minute' + (minutes > 1 ? 's' : ''));
            var text = parts.join(' ');
            modalDuration.value = text;
            if (modalDurationDisplay) modalDurationDisplay.textContent = text;
        }

        if (modalUseMyLocation) {
            modalUseMyLocation.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    setModalStatus('Geolocation not supported.', true);
                    return;
                }
                setModalStatus('Requesting location...', false);
                navigator.geolocation.getCurrentPosition(fillModalFromGeo, modalGeoError, { enableHighAccuracy: true, timeout: 10000 });
            });
        }

        if (modalLat) modalLat.addEventListener('input', updateModalArea);
        if (modalLng) modalLng.addEventListener('input', updateModalArea);
        updateModalArea();

        if (modalStartTime && modalEndTime) {
            applyStartTimeMin();
            modalStartTime.addEventListener('change', function() {
                if (!modalStartTime.value) return;
                if (!modalEndTime.value || modalEndTime.value < modalStartTime.value) {
                    modalEndTime.value = modalStartTime.value;
                }
                modalEndTime.min = modalStartTime.value;
                updateDuration();
            });
            modalEndTime.addEventListener('change', updateDuration);
        }

        // Initialize
        showUpcoming();
        applyFilters();
    })();
</script>

</body>
</html>





