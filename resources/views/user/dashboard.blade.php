<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white min-h-screen relative dashboard-shell">

<div class="dashboard-backdrop"></div>
<div class="dashboard-overlay"></div>

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
    :root {
        --slate-900: #0b0f14;
        --slate-700: #2c3545;
        --slate-500: #8a94a8;
        --card-bg: rgba(255, 255, 255, 0.96);
        --card-border: rgba(15, 23, 42, 0.08);
        --shadow-soft: 0 10px 24px rgba(10, 20, 40, 0.18);
        --radius-xl: 16px;
    }

    .dashboard-shell {
        font-family: "Segoe UI", system-ui, sans-serif;
        background: var(--slate-900);
    }

    .dashboard-backdrop {
        position: fixed;
        inset: 0;
        background: url('{{ asset('images/user-bg.jpg') }}') center/cover no-repeat;
        opacity: 0.25;
        z-index: 0;
    }

    .dashboard-overlay {
        position: fixed;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.5) 45%, rgba(0, 0, 0, 0.6));
        z-index: 1;
    }

    .dashboard-container {
        position: relative;
        z-index: 2;
        max-width: 1280px;
        margin: 0 auto;
        padding: 24px 24px 60px;
    }

    .dashboard-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: rgba(12, 16, 24, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 12px 16px;
        box-shadow: var(--shadow-soft);
    }

    .nav-left {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        font-size: 1rem;
    }

    .nav-center {
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #d5e1ef;
        font-size: 1rem;
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .nav-dropdown {
        position: relative;
    }

    .nav-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        background: rgba(12, 16, 24, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        min-width: 220px;
        padding: 8px;
        box-shadow: var(--shadow-soft);
        display: none;
        z-index: 60;
    }

    .nav-dropdown-menu a,
    .nav-dropdown-menu button {
        width: 100%;
        text-align: left;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .nav-dropdown-menu a:hover,
    .nav-dropdown-menu button:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .profile-chip {
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        font-size: 0.95rem;
    }

    .stats-row {
        margin-top: 16px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }


    .stat-item {
        display: flex;
        align-items: baseline;
        gap: 8px;
        color: #ffffff;
    }

    .stat-label {
        font-size: 0.8rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #cbd5e1;
    }

    .stat-value {
        font-size: 1.05rem;
        font-weight: 600;
    }

    .controls-row {
        margin-top: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .tab-group {
        display: flex;
        gap: 8px;
    }

    .dashboard-action {
        border-radius: 999px;
        padding: 8px 16px;
        font-weight: 600;
        letter-spacing: 0.01em;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.12);
        font-size: 0.95rem;
    }

    .dashboard-action.active {
        background: #16a34a;
        color: #ffffff;
        border-color: transparent;
    }

    .filters-row {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .dashboard-filter {
        background: rgba(255, 255, 255, 0.96);
        color: #101828;
        border: 1px solid rgba(16, 24, 40, 0.16);
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.95rem;
    }

    .dashboard-filter:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.25);
    }

    .dashboard-filter option {
        background: #ffffff;
        color: #101828;
    }

    .session-grid {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 16px;
    }

    @media (min-width: 768px) {
        .session-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1200px) {
        .session-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .session-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-soft);
        display: flex;
        flex-direction: column;
        min-height: 220px;
        padding: 18px 20px;
        color: #0f172a;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .session-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 26px rgba(12, 20, 36, 0.24);
    }

    .session-title {
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 4px;
    }

    .session-location {
        font-size: 0.95rem;
        color: #475569;
        margin-bottom: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .session-time {
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 600;
    }

    .session-meta {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        display: flex;
        flex-wrap: wrap;
        gap: 8px 12px;
        font-size: 1rem;
        color: #475569;
    }

    .session-actions {
        margin-top: auto;
        padding-top: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }


    .btn-ghost {
        border: 1px solid rgba(15, 23, 42, 0.2);
        background: transparent;
        color: #0f172a;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: transform 0.2s ease;
    }

    .dashboard-nav .btn-ghost {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }

    .btn-primary {
        background: #16a34a;
        color: #ffffff;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-outline {
        border: 1px solid rgba(15, 23, 42, 0.2);
        background: #ffffff;
        color: #0f172a;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: transform 0.2s ease;
    }

    .btn-link {
        background: transparent;
        color: #e2e8f0;
        font-size: 0.95rem;
        padding: 7px 0;
    }

    .btn-link-danger {
        background: transparent;
        color: #dc2626;
        font-size: 0.95rem;
        padding: 7px 0;
    }

    .btn-danger {
        border: 1px solid rgba(220, 38, 38, 0.3);
        color: #dc2626;
        background: transparent;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 0.95rem;
    }

    .btn-ghost:hover,
    .btn-outline:hover {
        transform: translateY(-1px);
    }

    .btn-primary:hover {
        transform: translateY(-1px) scale(1.03);
        box-shadow: 0 6px 14px rgba(22, 163, 74, 0.3);
    }

    .btn-start {
        animation: pulseOnce 1.6s ease 1;
    }

    .motivation-line {
        margin-top: 10px;
        font-size: 0.95rem;
        color: #e2e8f0;
    }

    .section-title {
        margin-top: 18px;
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #dbe7f3;
    }

    @keyframes pulseOnce {
        0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.35); }
        70% { box-shadow: 0 0 0 8px rgba(22, 163, 74, 0); }
        100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
    }

    @media (max-width: 640px) {
        .dashboard-container {
            padding: 16px 14px 40px;
        }

        .dashboard-nav {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .nav-left {
            justify-content: center;
            font-size: 0.95rem;
        }

        .nav-center {
            text-align: center;
            font-size: 0.9rem;
        }

        .nav-right {
            justify-content: center;
            gap: 8px;
        }

        .nav-right > * {
            flex: 1 1 100%;
        }

        .nav-right {
            flex-direction: column;
            align-items: stretch;
        }

        .nav-right .profile-chip { order: 1; }
        .nav-right .btn-outline { order: 2; }
        .nav-right .btn-link { order: 3; }
        .nav-right #buddyMatchButton,
        .nav-right .btn-primary,
        .nav-right .nav-dropdown {
            display: none;
        }

        .profile-chip {
            text-align: center;
        }

        .nav-right .btn-link {
            text-align: center;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 12px;
        }

        .stat-item {
            justify-content: center;
        }

        .controls-row {
            gap: 12px;
        }

        .tab-group {
            flex-wrap: wrap;
        }

        .dashboard-action {
            flex: 1 1 calc(50% - 6px);
            text-align: center;
        }

        .filters-row {
            flex-direction: column;
            align-items: stretch;
        }

        .dashboard-filter {
            width: 100%;
            min-height: 44px;
        }

        .session-card {
            padding: 16px;
        }

        .session-meta {
            flex-direction: column;
            gap: 6px;
        }

        .session-actions > * {
            flex: 1 1 100%;
            text-align: center;
            min-height: 44px;
        }

        .motivation-line {
            text-align: center;
        }

        .mobile-action-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
        }

        .mobile-action-stack .btn-primary,
        .mobile-action-stack .btn-ghost {
            width: 100%;
            min-height: 48px;
            text-align: center;
        }

        .mobile-action-stack .btn-ghost {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }

        .mobile-quick-menu {
            display: none;
            flex-direction: column;
            gap: 8px;
            margin-top: 8px;
            padding: 10px;
            border-radius: 12px;
            background: rgba(12, 16, 24, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .mobile-quick-menu.open {
            display: flex;
        }

        .mobile-quick-menu a,
        .mobile-quick-menu button {
            width: 100%;
            text-align: left;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            font-size: 0.95rem;
        }
    }
</style>

<div class="dashboard-container">
    <nav class="dashboard-nav">
        <div class="nav-left">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-12 object-contain">
            <span class="text-lg">STRIDESYNC</span>
        </div>
        <div class="nav-center">Dashboard</div>
        <div class="nav-right">
            <button id="buddyMatchButton" onclick="openBuddyMatch()" class="btn-ghost flex items-center gap-2">
                <span>Buddy Match</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9a3 3 0 106 0 3 3 0 00-6 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 20a6 6 0 0112 0v1H4v-1z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 21v-1a5 5 0 015-5h1v1a5 5 0 01-5 5h-1z" />
                </svg>
            </button>
            <div class="nav-dropdown">
                <button type="button" id="quickMenuButton" class="btn-ghost">More</button>
                <div id="quickMenuDropdown" class="nav-dropdown-menu">
                    <button type="button" onclick="openBuddyMatch()">
                        <span>Buddy Match</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9a3 3 0 106 0 3 3 0 00-6 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20a6 6 0 0112 0v1H4v-1z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 21v-1a5 5 0 015-5h1v1a5 5 0 01-5 5h-1z" />
                        </svg>
                    </button>
                    <button type="button" onclick="openModal('courseModal')">
                        <span>Explore Course Distance</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 17h18M5 7l2 10m10-10l-2 10" />
                        </svg>
                    </button>
                    <button type="button" onclick="openModal('eventCalendarModal')">
                        <span>Event Calendar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <a href="{{ route('register') }}">
                        <span>Registration Event</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11V3m0 8l-3-3m3 3l3-3M5 13h14a2 2 0 012 2v4H3v-4a2 2 0 012-2z" />
                        </svg>
                    </a>
                </div>
            </div>
            <button type="button" onclick="openModal('createSessionModal')" class="btn-primary">Create Session</button>
            <button type="button" onclick="openModal('profileModal')" class="btn-outline">Profile</button>
            <span class="profile-chip">Hello, {{ Auth::user()->name }}</span>
            <a href="{{ url('/logout') }}" class="btn-link">Logout</a>
        </div>
    </nav>

    <h3 class="section-title">Stats</h3>
    <div class="stats-row">
        <div class="stat-item">
            <span class="stat-label">Sessions Created</span>
            <span class="stat-value">{{ $fastFacts['total_created'] ?? 0 }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Sessions Joined</span>
            <span class="stat-value">{{ $fastFacts['total_joined'] ?? 0 }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Upcoming</span>
            <span class="stat-value">{{ $fastFacts['upcoming_count'] ?? 0 }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Completed</span>
            <span class="stat-value">{{ $fastFacts['completed_count'] ?? 0 }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Avg Pace</span>
            <span class="stat-value">{{ $fastFacts['average_pace'] ?? '-' }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Total</span>
            <span class="stat-value">{{ number_format($fastFacts['total_distance_km'] ?? 0, 1) }} km</span>
        </div>
    </div>
    <div class="motivation-line">
        Keep moving — your next run is waiting.
    </div>

    <div class="mobile-action-stack">
        <button type="button" onclick="openModal('createSessionModal')" class="btn-primary">Create Session</button>
        <button type="button" onclick="openBuddyMatch()" class="btn-ghost">Buddy Match</button>
        <button type="button" onclick="toggleQuickMenu()" class="btn-ghost">More</button>
        <div id="mobileQuickMenu" class="mobile-quick-menu">
            <button type="button" onclick="openModal('courseModal')">Explore Course Distance</button>
            <button type="button" onclick="openModal('eventCalendarModal')">Event Calendar</button>
            <a href="{{ route('register') }}">Registration Event</a>
        </div>
    </div>

    <div class="controls-row">
        <div class="tab-group">
            <button id="tab-upcoming" type="button" onclick="showUpcoming()" class="dashboard-action active">Upcoming</button>
            <button id="tab-past" type="button" onclick="showPast()" class="dashboard-action">History</button>
            <button type="button" data-weekly-schedule-btn onclick="openModal('weeklyScheduleModal')" class="dashboard-action">My Schedule</button>
        </div>
        <div class="filters-row text-black">
            <select id="sortSelect" class="dashboard-filter">
                <option value="all">Sort: All</option>
                <option value="time">Sort: Soonest</option>
                <option value="distance">Sort: Nearest</option>
            </select>
            <select id="paceFilter" class="dashboard-filter">
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
            <select id="searchLocation" class="dashboard-filter">
                <option value="">All locations</option>
                @foreach($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </select>
        </div>
    </div>

<!-- Cards Carousel (Upcoming) -->
    <div id="upcomingSection" class="w-full" style="display:block;">
        <div id="carouselWrapper" class="overflow-x-hidden overflow-y-visible w-full py-4">
        <div id="carouselInner" class="flex transition-transform duration-500 ease-in-out">
            @if($upcomingSessions->isEmpty())
                <div class="min-w-full px-4">
                    <p class="text-center text-gray-300">No upcoming sessions.</p>
                </div>
            @else
                <div class="session-grid min-w-full">
                    @foreach ($upcomingSessions as $session)
                        <div class="session-card"
                            data-session-distance="{{ $session->session_distance_km ?? '' }}"
                            data-session-lat="{{ $session->session_lat ?? '' }}"
                             data-session-lng="{{ $session->session_lng ?? '' }}"
                             data-session-source="{{ $session->session_coords_source ?? '' }}"
                             data-location-name="{{ $session->location_name }}"
                             data-start="{{ \Carbon\Carbon::parse($session->start_time)->timestamp }}"
                             data-location="{{ strtolower($session->location_name) }}"
                             data-pace="{{ $session->average_pace }}">
                            @php
                                $locName = $session->location_name;
                                $looksLikeCoords = preg_match('/^Lat\\s*-?\\d+(?:\\.\\d+)?[,\\s]*Lng\\s*-?\\d+(?:\\.\\d+)?$/i', $locName);
                                $displayLocation = $looksLikeCoords ? 'Location not set (add district, state)' : $locName;
                            @endphp
                            <div class="session-title">{{ $session->activity }}</div>
                            <div class="session-location">{{ $displayLocation }}</div>
                            <div class="text-sm font-semibold text-gray-700">Organizer: {{ $session->user->name ?? 'Unknown' }}</div>
                            <div class="session-time">{{ \Carbon\Carbon::parse($session->start_time)->format('M d • h:i A') }}</div>

                            <div class="session-meta">
                                <span>Pace: {{ $session->average_pace }}</span>
                                <span>Duration: {{ $session->duration }}</span>
                                <span class="session-distance-text" data-distance-format="card">
                                    Distance: calculating...
                                </span>
                            </div>

                            <div class="session-actions">
                                <button
                                    type="button"
                                    onclick="openModal('details-{{ $session->session_id }}')"
                                    class="btn-ghost">
                                    View
                                </button>
                                @if($session->user_id === Auth::id())
                                    <button
                                        type="button"
                                        onclick="openModal('edit-{{ $session->session_id }}')"
                                        class="btn-outline">
                                        Edit
                                    </button>
                                @endif
                                @if($session->user_id !== Auth::id())
                                    @if($session->user_joined)
                                        <form action="{{ route('sessions.leave', ['session_id' => $session->session_id]) }}" method="POST" onsubmit="return confirm('Leave this session?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-link-danger">
                                                Unjoin
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('sessions.join', ['session_id' => $session->session_id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-primary">
                                                Join
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                @if($session->user_id === Auth::id())
                                    @if(is_null($session->started_at))
                                        <form action="{{ route('sessions.start', $session->session_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-primary btn-start">
                                                Start
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('sessions.stop', $session->session_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-danger">
                                                Stop
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST" onsubmit="return confirm('Delete this session?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Next button -->
    <div id="carouselNav" class="mt-4 flex justify-center gap-2">
        <button id="prevButton" class="w-9 h-9 rounded-full bg-white text-black text-lg font-semibold shadow hover:scale-105 transition">
            &lt;
        </button>
        <button id="nextButton" class="w-9 h-9 rounded-full bg-white text-black text-lg font-semibold shadow hover:scale-105 transition">
            &gt;
        </button>
    </div>
</div>

<!-- Modals for Upcoming Sessions -->
@foreach ($upcomingSessions as $session)
    <div id="details-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-60 hidden"
         data-session-lat="{{ $session->session_lat ?? '' }}"
         data-session-lng="{{ $session->session_lng ?? '' }}"
         data-session-source="{{ $session->session_coords_source ?? '' }}"
         data-location-name="{{ $session->location_name }}">
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
                    <p class="text-sm text-gray-700 session-distance-text" data-distance-format="detail">
                        @if(!is_null($session->session_distance_km))
                            ~{{ $session->session_distance_km }} km from you
                        @else
                            Distance: unavailable
                        @endif
                    </p>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($session->location_name) }}"
                        target="_blank"
                        rel="noopener"
                        class="session-map-link inline-block mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-semibold">
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
                    <p class="text-base font-semibold">{{ $session->average_pace }}</p>
                </div>
                <div>
                    <p class="font-semibold">Duration</p>
                    <p class="text-base font-semibold">{{ $session->duration }}</p>
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

    <div id="modal-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden"
         data-session-lat="{{ $session->session_lat ?? '' }}"
         data-session-lng="{{ $session->session_lng ?? '' }}"
         data-session-source="{{ $session->session_coords_source ?? '' }}"
         data-location-name="{{ $session->location_name }}">
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
                <span class="session-distance-text" data-distance-format="inline">
                    • Distance: calculating...
                </span>
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
                   class="session-map-link inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200 text-sm">
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
<div id="pastSection" class="fixed inset-0 z-50 hidden" style="display:none;">
    <div class="absolute inset-0 bg-black/60" onclick="showUpcoming()"></div>
    <div class="relative bg-black/80 backdrop-blur border border-white/20 w-full max-w-5xl max-h-[82vh] overflow-y-auto rounded-2xl px-8 py-8 mx-auto my-24">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xl font-semibold">Session History</h2>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-300">Sessions you created or joined</span>
                <button type="button" onclick="showUpcoming()" class="px-4 py-2 bg-white text-black rounded text-sm font-semibold hover:bg-gray-200">Close</button>
            </div>
        </div>
        @if($pastSessions->isEmpty())
            <p class="text-sm text-gray-300">No past sessions yet.</p>
        @else
            <div class="space-y-3" id="historyList">
                @foreach($pastSessions as $session)
                    <div class="bg-white text-black rounded-xl p-3 shadow flex flex-col md:flex-row md:items-center md:justify-between gap-2 history-item"
                        data-session-distance="{{ $session->session_distance_km ?? '' }}"
                         data-session-lat="{{ $session->session_lat ?? '' }}"
                         data-session-lng="{{ $session->session_lng ?? '' }}"
                         data-session-source="{{ $session->session_coords_source ?? '' }}"
                         data-location-name="{{ $session->location_name }}"
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
                            <p class="text-base text-gray-700">Pace: {{ $session->average_pace }} &bull; Duration: {{ $session->duration }}</p>
                            <p class="text-xs text-gray-600">
                                Role: {{ $session->user_id === Auth::id() ? 'Organizer' : 'Participant' }}
                            </p>
                            <p class="text-xs text-gray-600 session-distance-text" data-distance-format="list">
                                Distance: calculating...
                            </p>
                            @if($session->reviews->isNotEmpty())
                                <p class="text-xs text-gray-700 mt-1">Reviews:</p>
                                <ul class="text-xs text-gray-800 list-disc ml-4">
                                    @foreach($session->reviews as $review)
                                        <li>{{ $review->rating }}/5 - {{ $review->comment }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2 min-w-[220px]">
                            @php
                                $now = \Carbon\Carbon::now();
                                $canReview = $session->user_joined && ($session->end_time < $now || !is_null($session->completed_at));
                                $hasReviewed = $session->reviews->contains('user_id', Auth::id());
                            @endphp
                            @if($session->user_id === Auth::id() && is_null($session->completed_at))
                                <form action="{{ route('sessions.complete', $session->session_id) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 bg-green-700 text-white rounded text-xs">Mark completed</button>
                                </form>
                            @endif
                            @if($canReview && !$hasReviewed)
                                <form action="{{ route('sessions.review', $session->session_id) }}" method="POST" class="flex flex-col items-end gap-2">
                                    @csrf
                                    <input name="comment" type="text" placeholder="Add review" class="border rounded px-3 py-2 text-sm w-64">
                                    <div class="flex items-center gap-1" data-star-rating>
                                        @for($i=1; $i<=5; $i++)
                                            <button type="button" class="star-btn text-yellow-500 text-2xl" data-value="{{ $i }}">&star;</button>
                                        @endfor
                                        <input type="hidden" name="rating" value="1" class="star-input">
                                    </div>
                                    <button class="px-4 py-2 bg-blue-700 text-white rounded text-sm min-w-[90px] text-center">Submit</button>
                                </form>
                            @elseif($canReview && $hasReviewed)
                                <span class="text-xs text-green-700 font-semibold text-right">You already reviewed this session.</span>
                            @endif
                            @if($session->user_id === Auth::id())
                                <form action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST" onsubmit="return confirm('Delete this session?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-4 py-2 bg-red-700 text-white rounded text-sm min-w-[90px] text-center">Delete</button>
                                </form>
                            @else
                                <span class="text-xs text-transparent select-none">No actions</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
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
            <button type="button" onclick="linkTelegram()" class="mt-3 w-full px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800 transition">
                Open StrideSyncBot
            </button>
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
        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            @include('user.partials.profile-content', ['embed' => true, 'showTitle' => false, 'user' => $user])
        </div>
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
        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            @include('user.partials.profile-edit-form', ['embed' => true, 'showTitle' => false, 'user' => $user, 'states' => $states])
        </div>
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
                                  <p class="font-semibold">{{ $start->format('D, M d') }} &bull; {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}</p>
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
                          <div class="text-sm text-gray-600 mt-2">
                              Pace: {{ $session->average_pace ?? 'N/A' }} &bull; Duration: {{ $session->duration ?? 'N/A' }}
                          </div>
                          <div class="mt-3 flex flex-wrap gap-2">
                              <button type="button" onclick="openModal('details-{{ $session->session_id }}')" class="px-3 py-1 bg-gray-800 text-white rounded text-xs">View</button>
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

<!-- Course Modal -->
<div id="courseModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('courseModal')"></div>
    <div class="relative bg-white text-black rounded-xl shadow-2xl max-w-5xl w-full h-[80vh] overflow-hidden z-60">
        <button type="button" onclick="closeModal('courseModal')" class="absolute top-2 right-2 text-xl font-bold text-gray-700 hover:text-red-600">&times;</button>
        <iframe src="{{ route('course') }}" class="w-full h-full border-0" title="Explore Course Distance"></iframe>
    </div>
</div>

<!-- Event Calendar Modal -->
<div id="eventCalendarModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('eventCalendarModal')"></div>
    <div class="relative bg-white text-black rounded-xl shadow-2xl max-w-6xl w-full h-[85vh] overflow-hidden z-60">
        <button type="button" onclick="closeModal('eventCalendarModal')" class="absolute top-2 right-2 text-xl font-bold text-gray-700 hover:text-red-600">&times;</button>
        <iframe src="{{ route('event.calendar') }}" class="w-full h-full border-0" title="Event Calendar"></iframe>
    </div>
</div>

  @php
      $upcomingSessionIds = $upcomingSessions->pluck('session_id')->all();
      $weeklySessionsForModals = collect($weeklySchedule)
          ->pluck('session')
          ->filter()
          ->unique('session_id')
          ->filter(function ($session) use ($upcomingSessionIds) {
              return !in_array($session->session_id, $upcomingSessionIds, true);
          })
          ->values();
  @endphp

  @foreach($weeklySessionsForModals as $session)
      <div id="details-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-60 hidden"
           data-session-lat="{{ $session->session_lat ?? '' }}"
           data-session-lng="{{ $session->session_lng ?? '' }}"
           data-session-source="{{ $session->session_coords_source ?? '' }}"
           data-location-name="{{ $session->location_name }}">
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
                      <p class="text-sm text-gray-700 session-distance-text" data-distance-format="detail">
                          @if(!is_null($session->session_distance_km))
                              ~{{ $session->session_distance_km }} km from you
                          @else
                              Distance: unavailable
                          @endif
                      </p>
                      <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($session->location_name) }}"
                          target="_blank"
                          rel="noopener"
                          class="session-map-link inline-block mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-semibold">
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
                      <p class="text-base font-semibold">{{ $session->average_pace }}</p>
                  </div>
                  <div>
                      <p class="font-semibold">Duration</p>
                      <p class="text-base font-semibold">{{ $session->duration }}</p>
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
  @endforeach

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
                    var dist = parseFloat(node.dataset.sessionDistance || '9999');
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
                        var da = parseFloat(a.dataset.sessionDistance || '9999');
                        var db = parseFloat(b.dataset.sessionDistance || '9999');
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

        var quickMenuButton = document.getElementById('quickMenuButton');
        var quickMenuDropdown = document.getElementById('quickMenuDropdown');
        var mobileQuickMenu = document.getElementById('mobileQuickMenu');

        function toggleQuickMenu() {
            if (quickMenuDropdown) {
                var isOpen = quickMenuDropdown.style.display === 'block';
                quickMenuDropdown.style.display = isOpen ? 'none' : 'block';
            }
            if (mobileQuickMenu) {
                mobileQuickMenu.classList.toggle('open');
                if (mobileQuickMenu.classList.contains('open')) {
                    mobileQuickMenu.scrollIntoView({ block: 'nearest' });
                }
            }
        }

        if (quickMenuButton) {
            quickMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleQuickMenu();
            });
        }

        if (quickMenuDropdown) {
            quickMenuDropdown.addEventListener('click', function() {
                quickMenuDropdown.style.display = 'none';
            });
        }

        document.addEventListener('click', function(e) {
            if (!quickMenuDropdown) return;
            if (quickMenuDropdown.style.display !== 'block') return;
            var dropdownParent = quickMenuDropdown.parentElement;
            if (dropdownParent && dropdownParent.contains(e.target)) return;
            quickMenuDropdown.style.display = 'none';
        });

        function setupStarRatings() {
            var groups = document.querySelectorAll('[data-star-rating]');
            groups.forEach(function(group) {
                var buttons = group.querySelectorAll('.star-btn');
                var input = group.querySelector('.star-input');
                if (!input || buttons.length === 0) return;

                function setStars(value) {
                    buttons.forEach(function(btn) {
                        var starValue = parseInt(btn.getAttribute('data-value') || '0', 10);
                        btn.textContent = starValue <= value ? '\u2605' : '\u2606';
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
                function updateEditDuration() {
                    var sessionId = input.getAttribute('data-edit-session');
                    var endInput = findEditField(sessionId, 'end_time');
                    var durationInput = document.getElementById('duration_' + sessionId);
                    if (!endInput || !durationInput || !input.value || !endInput.value) return;

                    var start = new Date(input.value);
                    var end = new Date(endInput.value);
                    if (isNaN(start.getTime()) || isNaN(end.getTime())) return;

                    var diffMs = end.getTime() - start.getTime();
                    if (diffMs <= 0) {
                        durationInput.value = '';
                        return;
                    }

                    var totalMinutes = Math.round(diffMs / 60000);
                    var hours = Math.floor(totalMinutes / 60);
                    var minutes = totalMinutes % 60;
                    var parts = [];
                    if (hours > 0) parts.push(hours + ' hour' + (hours > 1 ? 's' : ''));
                    if (minutes > 0) parts.push(minutes + ' minute' + (minutes > 1 ? 's' : ''));
                    durationInput.value = parts.join(' ');
                }

                function syncEndMin() {
                    var sessionId = input.getAttribute('data-edit-session');
                    var endInput = findEditField(sessionId, 'end_time');
                    if (!endInput || !input.value) return;
                    if (!endInput.value || endInput.value < input.value) {
                        endInput.value = input.value;
                    }
                    endInput.min = input.value;
                    updateEditDuration();
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
                input.addEventListener('change', updateEditDuration);
                applyEditMin();
                updateEditDuration();
            });

            var endInputs = document.querySelectorAll('[data-edit-field="end_time"]');
            endInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    var sessionId = input.getAttribute('data-edit-session');
                    var startInput = findEditField(sessionId, 'start_time');
                    var durationInput = document.getElementById('duration_' + sessionId);
                    if (!startInput || !durationInput || !startInput.value || !input.value) return;

                    var start = new Date(startInput.value);
                    var end = new Date(input.value);
                    if (isNaN(start.getTime()) || isNaN(end.getTime())) return;

                    var diffMs = end.getTime() - start.getTime();
                    if (diffMs <= 0) {
                        durationInput.value = '';
                        return;
                    }

                    var totalMinutes = Math.round(diffMs / 60000);
                    var hours = Math.floor(totalMinutes / 60);
                    var minutes = totalMinutes % 60;
                    var parts = [];
                    if (hours > 0) parts.push(hours + ' hour' + (hours > 1 ? 's' : ''));
                    if (minutes > 0) parts.push(minutes + ' minute' + (minutes > 1 ? 's' : ''));
                    durationInput.value = parts.join(' ');
                });
            });
        }

        function openModal(id) {
            var el = document.getElementById(id);
            if (!el) return;
            if (quickMenuDropdown) {
                quickMenuDropdown.style.display = 'none';
            }
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

        function openBuddyMatch() {
            loadBuddies();
            openModal('buddyMatchModal');
        }
        window.openBuddyMatch = openBuddyMatch;

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

        var routeDistanceUrl = "{{ route('route.distance') }}";
        var fallbackLocationText = @json(optional(Auth::user())->formatLocationText(''));

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
            if (format === 'card') return 'Distance: ' + kmText + ' km away';
            if (format === 'detail') return '~' + kmText + ' km from you';
            if (format === 'inline') return '• ~' + kmText + ' km from you';
            if (format === 'list') return '~' + kmText + ' km away';
            return kmText + ' km away';
        }

        function applyDistanceToElements(container, km) {
            if (!isFinite(km)) return;
            container.dataset.sessionDistance = km.toFixed(1);
            var elems = container.querySelectorAll('.session-distance-text');
            elems.forEach(function(el) {
                var format = el.dataset.distanceFormat || '';
                el.textContent = formatDistanceText(format, km);
            });
        }

        function applyDistanceUnavailable(container) {
            var elems = container.querySelectorAll('.session-distance-text');
            elems.forEach(function(el) {
                var format = el.dataset.distanceFormat || '';
                if (format === 'inline') {
                    el.textContent = '• Distance: unavailable';
                } else {
                    el.textContent = 'Distance: unavailable';
                }
            });
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

        function updateSessionDistancesFromGeo(userLat, userLon) {
            var containers = document.querySelectorAll('[data-session-lat][data-session-lng]');
            containers.forEach(function(container) {
                var lat = parseFloat(container.dataset.sessionLat || '');
                var lon = parseFloat(container.dataset.sessionLng || '');
                var source = (container.dataset.sessionSource || '').toLowerCase();
                var locationName = container.dataset.locationName || '';
                var needsGeocode = (!isFinite(lat) || !isFinite(lon)) || source !== 'geocode';

                function applyDistance(latVal, lonVal) {
                    var mapLinks = container.querySelectorAll('.session-map-link');
                    mapLinks.forEach(function(link) {
                        var origin = userLat.toFixed(6) + ',' + userLon.toFixed(6);
                        var destination = locationName ? locationName : (latVal.toFixed(6) + ',' + lonVal.toFixed(6));
                        link.href = 'https://www.google.com/maps/dir/?api=1&origin=' + encodeURIComponent(origin) + '&destination=' + encodeURIComponent(destination);
                    });

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

                if (needsGeocode && locationName) {
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
            });

            if (typeof applyFilters === 'function') {
                applyFilters();
            }
        }

        function requestBrowserDistance() {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(function(pos) {
                updateSessionDistancesFromGeo(pos.coords.latitude, pos.coords.longitude);
            }, function() {
                if (!fallbackLocationText) return;
                fetch('/geocode-location?query=' + encodeURIComponent(fallbackLocationText))
                    .then(function(resp) { return resp.ok ? resp.json() : null; })
                    .then(function(data) {
                        if (!data || !isFinite(data.lat) || !isFinite(data.lng)) return;
                        updateSessionDistancesFromGeo(parseFloat(data.lat), parseFloat(data.lng));
                    })
                    .catch(function() {});
            }, { enableHighAccuracy: true, timeout: 10000 });
        }

        // Initialize
        showUpcoming();
        applyFilters();
        requestBrowserDistance();
    })();
</script>

</body>
</html>








