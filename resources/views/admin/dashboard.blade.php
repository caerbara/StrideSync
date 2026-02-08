<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StrideSync</title>
    @vite('resources/css/app.css')
    <style>
        :root {
            --slate-900: #0b0f14;
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

        .profile-chip {
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 0.95rem;
        }

        .btn-ghost {
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .btn-link {
            background: transparent;
            color: #e2e8f0;
            font-size: 0.95rem;
            padding: 7px 0;
        }
        .admin-tools {
            position: relative;
        }

        .admin-tools-button {
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-tools-menu {
            position: absolute;
            top: 120%;
            right: 0;
            min-width: 220px;
            background: rgba(14, 18, 26, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
            display: none;
            z-index: 50;
        }

        .admin-tools-menu.open {
            display: block;
        }

        .admin-tools-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: #f8fafc;
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.06);
        }

        .admin-tools-item + .admin-tools-item {
            margin-top: 8px;
        }

        .admin-tools-item:hover {
            background: rgba(255, 255, 255, 0.12);
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

        .session-grid {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 16px;
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
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .nav-right > * {
                width: 100%;
            }

            .profile-chip {
                text-align: center;
            }

            .admin-tools-button,
            .btn-link {
                width: 100%;
                text-align: center;
            }

            .admin-tools-menu {
                right: auto;
                left: 0;
                min-width: 100%;
            }

            .stats-row {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px 12px;
            }

            .stat-item {
                justify-content: center;
                text-align: center;
                flex-direction: column;
                gap: 4px;
            }

            .stat-label {
                font-size: 0.7rem;
            }

            .stat-value {
                font-size: 1.1rem;
            }
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
        }

        .session-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .session-subtitle {
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 8px;
        }

        .session-meta {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
            display: flex;
            flex-wrap: wrap;
            gap: 8px 12px;
            font-size: 0.9rem;
            color: #475569;
        }

        .session-actions {
            margin-top: auto;
            padding-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn-outline {
            border: 1px solid rgba(15, 23, 42, 0.2);
            background: #ffffff;
            color: #0f172a;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .btn-danger {
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #dc2626;
            background: transparent;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen relative dashboard-shell">

@php
    $sessionCards = collect($activeSessions)->merge($upcomingSessions)->merge($pastSessions)->unique('session_id');
    $userList = \App\Models\User::where('is_admin', false)->orderBy('created_at', 'desc')->get();
    $newUsersThisWeek = \App\Models\User::where('is_admin', false)
        ->where('created_at', '>=', \Carbon\Carbon::now()->startOfWeek())
        ->count();
    $totalKmCreated = 0.0;
    foreach (\App\Models\RunningSession::query()->get(['activity']) as $session) {
        $activity = (string) ($session->activity ?? '');
        if (preg_match('/(\d+(?:\.\d+)?)\s*km/i', $activity, $m)) {
            $totalKmCreated += (float) $m[1];
        }
    }
@endphp

<div class="dashboard-backdrop"></div>
<div class="dashboard-overlay"></div>

<div class="dashboard-container">
    <nav class="dashboard-nav">
        <div class="nav-left">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-12 object-contain">
            <span class="text-lg">STRIDESYNC</span>
        </div>
        <div class="nav-center">Admin Dashboard</div>
        <div class="nav-right">
            <span class="profile-chip">Hello, {{ Auth::user()->name ?? 'Administrator' }}</span>
            <div class="admin-tools">
                <button type="button" class="admin-tools-button" onclick="toggleAdminTools()">
                    Admin Tools
                    <span aria-hidden="true">▾</span>
                </button>
                <div id="adminToolsMenu" class="admin-tools-menu">
                    <a href="{{ route('admin.telegram.index') }}" class="admin-tools-item">
                        <span>Admin Telegram</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M22 2 11 13" />
                            <path d="M22 2 15 22 11 13 2 9z" />
                        </svg>
                    </a>
                    <button type="button" class="admin-tools-item w-full" onclick="toggleModal('sessionHistoryModal')">
                        <span>Session History</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2 2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 11a9 9 0 111.7 4.7" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h4" />
                        </svg>
                    </button>
                    <button type="button" class="admin-tools-item w-full" onclick="toggleModal('reviewHistoryModal')">
                        <span>User Reviews</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h6m-2 8l-4-4H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </button>
                    <button type="button" class="admin-tools-item w-full" onclick="toggleModal('usersModal')">
                        <span>User Management</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20H4v-2a3 3 0 015.356-1.857" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a4 4 0 10-8 0 4 4 0 008 0z" />
                        </svg>
                    </button>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="btn-link">Logout</a>
        </div>
    </nav>

    <div class="stats-row">
        <div class="stat-item">
            <span class="stat-label">New Users (This Week)</span>
            <span class="stat-value">{{ $newUsersThisWeek }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Upcoming</span>
            <span class="stat-value">{{ count($upcomingSessions ?? []) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Total KM Created</span>
            <span class="stat-value">{{ number_format($totalKmCreated, 1) }}</span>
        </div>
    </div>

    @if($sessionCards->isEmpty())
        <div class="text-slate-200 mt-10">No sessions yet.</div>
    @else
        <div class="flex items-center justify-between mt-4">
            <button type="button" class="btn-ghost" data-bulk-toggle="sessions">Select</button>
            <div class="flex items-center gap-4 hidden" data-bulk-ui="sessions">
                <label class="flex items-center gap-2 text-sm text-slate-200">
                    <input type="checkbox" data-bulk-select-all="sessions" class="accent-white">
                    Select all
                </label>
                <button type="button" class="btn-danger" onclick="bulkDeleteSelected('sessions')">Delete selected</button>
            </div>
        </div>
        <div class="session-grid">
            @foreach ($sessionCards as $session)
                @php
                    $start = is_object($session->start_time) ? $session->start_time : \Carbon\Carbon::parse($session->start_time);
                    $end = is_object($session->end_time) ? $session->end_time : \Carbon\Carbon::parse($session->end_time);
                    $organizerName = $session->user->name ?? 'Unknown';
                    $status = $session->completed_at
                        ? 'COMPLETED'
                        : ($session->started_at ? 'IN PROGRESS' : ($start->isFuture() ? 'UPCOMING' : 'SCHEDULED'));
                    $participants = collect($session->joinedUsers ?? [])
                        ->map(function ($joined) {
                            return [
                                'name' => $joined->user->name ?? 'Unknown',
                                'role' => 'Participant',
                            ];
                        })
                        ->prepend([
                            'name' => $organizerName,
                            'role' => 'Organizer',
                        ])
                        ->values();
                    $sessionPayload = [
                        'organizer' => $organizerName,
                        'location' => $session->location_name ?? 'Not set',
                        'pace' => $session->average_pace ?? 'N/A',
                        'duration' => $session->duration ?? 'N/A',
                        'start' => $start->format('M d, Y @ h:i A'),
                        'end' => $end->format('M d, Y @ h:i A'),
                        'status' => $status,
                        'participants' => $participants,
                    ];
                @endphp
                <div class="session-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="session-title">{{ $session->activity ?? 'Session' }}</div>
                            <div class="session-subtitle">{{ $session->location_name ?? 'Not set' }}</div>
                        </div>
                        <label class="flex items-center gap-2 text-xs text-slate-500 hidden" data-bulk-ui="sessions">
                            <input type="checkbox" data-bulk="sessions" data-form="delete-session-{{ $session->session_id }}" class="accent-slate-600">
                            Select
                        </label>
                    </div>
                    <div class="session-meta">
                        <span>Host: {{ $organizerName }}</span>
                        <span>{{ $start->format('M d • h:i A') }}</span>
                        <span>Status: {{ $status }}</span>
                    </div>
                    <div class="session-actions">
                        <button type="button" data-session='@json($sessionPayload)' class="js-session-view btn-outline">View</button>
                        <form id="delete-session-{{ $session->session_id }}" action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this session?')" class="btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Users Modal -->
<div id="usersModal" class="fixed inset-0 bg-black/70 hidden z-50 items-center justify-center p-4 admin-modal">
    <div class="bg-white text-slate-900 rounded-xl shadow-2xl max-w-6xl w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <div>
                <h3 class="text-lg font-semibold">User Management</h3>
                <p class="text-xs text-slate-500">Manage all registered users</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="toggleModal('usersModal')" class="text-slate-500 hover:text-black text-xl">&times;</button>
            </div>
        </div>
        <div class="bg-slate-50 flex flex-col">
            <iframe
                src="{{ route('users.index', ['embed' => 1]) }}"
                title="User Management"
                class="w-full h-[60vh] border-0"
            ></iframe>
            <div class="px-6 py-4 bg-white border-t flex justify-end">
                <button onclick="toggleModal('usersModal')" class="px-4 py-2 bg-slate-800 text-white rounded hover:bg-slate-900">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div id="userCreateModal" class="fixed inset-0 bg-white/90 hidden z-50 items-center justify-center p-4 admin-modal">
    <div class="bg-white text-slate-900 rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <div>
                <h3 class="text-lg font-semibold">Create New User</h3>
                <p class="text-xs text-slate-500">Add a new user to the system</p>
            </div>
            <button onclick="toggleModal('userCreateModal')" class="text-slate-500 hover:text-black text-xl">&times;</button>
        </div>
        <div class="p-6 bg-white">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <p class="font-semibold mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ route('admin.dashboard') }}">

                <div>
                    <label for="admin_create_name" class="block text-sm font-semibold mb-1">Name <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="admin_create_name"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="John Doe"
                        required>
                </div>

                <div>
                    <label for="admin_create_email" class="block text-sm font-semibold mb-1">Email <span class="text-red-500">*</span></label>
                    <input
                        type="email"
                        id="admin_create_email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="john@example.com"
                        required>
                </div>

                <div>
                    <label for="admin_create_phone" class="block text-sm font-semibold mb-1">Phone Number</label>
                    <input
                        type="text"
                        id="admin_create_phone"
                        name="phone_number"
                        value="{{ old('phone_number') }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="+60 12-345 6789">
                </div>

                <div>
                    <label for="admin_create_password" class="block text-sm font-semibold mb-1">Password <span class="text-red-500">*</span></label>
                    <input
                        type="password"
                        id="admin_create_password"
                        name="password"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="Minimum 6 characters"
                        required>
                </div>

                <div>
                    <label for="admin_create_password_confirmation" class="block text-sm font-semibold mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input
                        type="password"
                        id="admin_create_password_confirmation"
                        name="password_confirmation"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="Confirm password"
                        required>
                </div>

                <div>
                    <label for="admin_create_gender" class="block text-sm font-semibold mb-1">Gender</label>
                    <select
                        id="admin_create_gender"
                        name="gender"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300">
                        <option value="">Select gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="admin_create_avg_pace" class="block text-sm font-semibold mb-1">Average Pace</label>
                    <input
                        type="text"
                        id="admin_create_avg_pace"
                        name="avg_pace"
                        value="{{ old('avg_pace') }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="e.g., 8:30 min/km">
                </div>

                <div>
                    <label for="admin_create_location" class="block text-sm font-semibold mb-1">Location</label>
                    <input
                        type="text"
                        id="admin_create_location"
                        name="location"
                        value="{{ old('location') }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="e.g., Central Park">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        Create User
                    </button>
                    <button type="button" onclick="toggleModal('userCreateModal')" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-900 px-4 py-2 rounded-lg font-semibold transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Session History Modal -->
<div id="sessionHistoryModal" class="fixed inset-0 bg-black/70 hidden z-50 items-center justify-center p-4 admin-modal">
    <div class="bg-white text-slate-900 rounded-xl shadow-2xl max-w-5xl w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Session History</h3>
            <button onclick="toggleModal('sessionHistoryModal')" class="text-slate-500 hover:text-black text-xl">&times;</button>
        </div>
        <div class="flex items-center justify-between px-6 py-3 border-b bg-slate-50">
            <button type="button" class="px-3 py-1 bg-slate-800 text-white rounded text-xs hover:bg-slate-900" data-bulk-toggle="sessionHistory">
                Select
            </button>
            <div class="flex items-center gap-4 hidden" data-bulk-ui="sessionHistory">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" data-bulk-select-all="sessionHistory" class="accent-slate-600">
                    Select all
                </label>
                <button type="button" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700" onclick="bulkDeleteSelected('sessionHistory')">
                    Delete selected
                </button>
            </div>
        </div>
          <div class="overflow-x-auto">
              <table class="w-full text-sm text-slate-900">
                  <thead class="bg-slate-100 text-slate-700">
                      <tr class="text-left">
                        <th class="px-4 py-3 w-10 hidden" data-bulk-ui="sessionHistory"></th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Pace</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                        @forelse($pastSessions as $session)
                        @php
                            $end = is_object($session->end_time) ? $session->end_time : \Carbon\Carbon::parse($session->end_time);
                            $start = is_object($session->start_time) ? $session->start_time : \Carbon\Carbon::parse($session->start_time);
                            $organizerName = $session->user->name ?? 'Unknown';
                            $status = $session->completed_at
                                ? 'COMPLETED'
                                : ($session->started_at ? 'IN PROGRESS' : ($start->isFuture() ? 'UPCOMING' : 'SCHEDULED'));
                            $participants = collect($session->joinedUsers ?? [])
                                ->map(function ($joined) {
                                    return [
                                        'name' => $joined->user->name ?? 'Unknown',
                                        'role' => 'Participant',
                                    ];
                                })
                                ->prepend([
                                    'name' => $organizerName,
                                    'role' => 'Organizer',
                                ])
                                ->values();
                            $sessionPayload = [
                                'organizer' => $organizerName,
                                'location' => $session->location_name ?? 'Not set',
                                'pace' => $session->average_pace ?? 'N/A',
                                'duration' => $session->duration ?? 'N/A',
                                'start' => $start->format('M d, Y @ h:i A'),
                                'end' => $end->format('M d, Y @ h:i A'),
                                'status' => $status,
                                'participants' => $participants,
                            ];
                        @endphp
                        <tr class="border-b last:border-b-0">
                            <td class="px-4 py-3 hidden" data-bulk-ui="sessionHistory">
                                <input type="checkbox" data-bulk="sessionHistory" data-form="delete-history-{{ $session->session_id }}" class="accent-slate-600">
                            </td>
                            <td class="px-4 py-3">{{ $session->user->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3">{{ $session->location_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $end->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $session->average_pace ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <button type="button" data-session='@json($sessionPayload)' class="js-session-view inline-block px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">View</button>
                                <form id="delete-history-{{ $session->session_id }}" action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete this session?')" class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">No past sessions.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 text-right bg-slate-100">
            <button onclick="toggleModal('sessionHistoryModal')" class="px-4 py-2 bg-slate-800 text-white rounded hover:bg-slate-900">Close</button>
        </div>
    </div>
</div>

<!-- Review History Modal -->
<div id="reviewHistoryModal" class="fixed inset-0 bg-black/70 hidden z-50 items-center justify-center p-4 admin-modal">
    <div class="bg-white text-slate-900 rounded-xl shadow-2xl max-w-5xl w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">User Reviews</h3>
            <button onclick="toggleModal('reviewHistoryModal')" class="text-slate-500 hover:text-black text-xl">&times;</button>
        </div>
        <div class="flex items-center justify-between px-6 py-3 border-b bg-slate-50">
            <button type="button" class="px-3 py-1 bg-slate-800 text-white rounded text-xs hover:bg-slate-900" data-bulk-toggle="reviews">
                Select
            </button>
            <div class="flex items-center gap-4 hidden" data-bulk-ui="reviews">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" data-bulk-select-all="reviews" class="accent-slate-600">
                    Select all
                </label>
                <button type="button" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700" onclick="bulkDeleteSelected('reviews')">
                    Delete selected
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-900">
                <thead class="bg-slate-100 text-slate-700">
                    <tr class="text-left">
                        <th class="px-4 py-3 w-10 hidden" data-bulk-ui="reviews"></th>
                        <th class="px-4 py-3">Organizer</th>
                        <th class="px-4 py-3">Session Time</th>
                        <th class="px-4 py-3">Review</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3 text-center">Featured</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReviews as $review)
                        @php
                            $reviewSession = $review->session;
                            $reviewOrganizer = $reviewSession?->user?->name ?? 'Unknown';
                            $reviewStart = $reviewSession && $reviewSession->start_time
                                ? \Carbon\Carbon::parse($reviewSession->start_time)->format('M d, Y h:i A')
                                : 'N/A';
                            $reviewEnd = $reviewSession && $reviewSession->end_time
                                ? \Carbon\Carbon::parse($reviewSession->end_time)->format('M d, Y h:i A')
                                : 'N/A';
                        @endphp
                        <tr class="border-b last:border-b-0">
                            <td class="px-4 py-3 hidden" data-bulk-ui="reviews">
                                <input type="checkbox" data-bulk="reviews" data-form="delete-review-{{ $review->id }}" class="accent-slate-600">
                            </td>
                            <td class="px-4 py-3">{{ $reviewOrganizer }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $reviewStart }}</div>
                                <div class="text-xs text-slate-500">{{ $reviewEnd }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-amber-500 text-base">
                                    {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', max(0, 5 - (int) $review->rating)) }}
                                </div>
                                <div class="text-slate-700">{{ $review->comment ?: 'No comment' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $review->created_at?->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.reviews.feature', $review->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded text-xs {{ $review->is_featured ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                                        {{ $review->is_featured ? 'Featured' : 'Feature' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form id="delete-review-{{ $review->id }}" action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete this review?')" class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-500">No reviews yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 text-right bg-slate-100">
            <button onclick="toggleModal('reviewHistoryModal')" class="px-4 py-2 bg-slate-800 text-white rounded hover:bg-slate-900">Close</button>
        </div>
    </div>
</div>

<!-- Session Detail Modal -->
<div id="sessionDetailModal" class="fixed inset-0 bg-black/70 hidden z-50 items-center justify-center p-4 admin-modal">
    <div class="bg-white text-slate-900 rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Session Information</h3>
            <button type="button" onclick="closeSessionModal()" class="text-slate-500 hover:text-black text-xl">&times;</button>
        </div>
        <div class="p-6 space-y-6">
            <div class="space-y-2">
                <p class="text-sm uppercase text-slate-400 tracking-wide">Organizer</p>
                <p id="sessionOrganizer" class="text-lg font-semibold">-</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <p class="text-sm uppercase text-slate-400 tracking-wide">Location</p>
                    <p id="sessionLocation" class="text-base font-semibold">-</p>
                </div>
                <div class="space-y-2">
                    <p class="text-sm uppercase text-slate-400 tracking-wide">Average Pace</p>
                    <p id="sessionPace" class="text-base font-semibold">-</p>
                </div>
                <div class="space-y-2">
                    <p class="text-sm uppercase text-slate-400 tracking-wide">Duration</p>
                    <p id="sessionDuration" class="text-base font-semibold">-</p>
                </div>
                <div class="space-y-2">
                    <p class="text-sm uppercase text-slate-400 tracking-wide">Status</p>
                    <p id="sessionStatus" class="text-base font-semibold">-</p>
                </div>
            </div>
            <div class="space-y-2">
                <p class="text-sm uppercase text-slate-400 tracking-wide">Schedule</p>
                <p class="text-base"><span class="font-semibold">Starts:</span> <span id="sessionStart">-</span></p>
                <p class="text-base"><span class="font-semibold">Ends:</span> <span id="sessionEnd">-</span></p>
            </div>
            <div class="space-y-2">
                <p class="text-sm uppercase text-slate-400 tracking-wide">Participants</p>
                <ul id="sessionParticipants" class="space-y-2 text-sm text-slate-700"></ul>
            </div>
        </div>
        <div class="px-6 py-4 text-right bg-slate-100">
            <button type="button" onclick="closeSessionModal()" class="px-4 py-2 bg-slate-800 text-white rounded hover:bg-slate-900">Close</button>
        </div>
    </div>
</div>

<script>
const nextButton = document.getElementById('nextButton');
const carouselInner = document.getElementById('carouselInner');

let currentIndex = 0;
const totalPages = carouselInner ? carouselInner.children.length : 0;

if (nextButton && carouselInner) {
    nextButton.addEventListener('click', () => {
        if (totalPages === 0) return;
        currentIndex = (currentIndex + 1) % totalPages;
        const offset = currentIndex * carouselInner.clientWidth;
        carouselInner.style.transform = `translateX(-${offset}px)`;
    });
}

function toggleModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const modals = ['usersModal', 'sessionHistoryModal', 'reviewHistoryModal', 'sessionDetailModal', 'userCreateModal'];
    const isOpening = el.classList.contains('hidden');
    modals.forEach((modalId) => {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    if (isOpening) {
        el.classList.remove('hidden');
        el.classList.add('flex');
        document.body.classList.add('modal-open');
    } else {
        document.body.classList.remove('modal-open');
    }
}

document.querySelectorAll('[data-bulk-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const group = button.dataset.bulkToggle;
        const targets = Array.from(document.querySelectorAll(`[data-bulk-ui='${group}']`));
        const isHidden = targets.length > 0 && targets[0].classList.contains('hidden');
        targets.forEach((el) => {
            el.classList.toggle('hidden');
        });
        button.textContent = isHidden ? 'Cancel' : 'Select';
    });
});

function bulkDeleteSelected(group) {
    const selected = Array.from(document.querySelectorAll(`input[data-bulk='${group}']:checked`));
    if (selected.length === 0) {
        alert('No items selected.');
        return;
    }
    if (!confirm('Delete selected items?')) {
        return;
    }
    Promise.all(selected.map((input) => submitDeleteForm(input.dataset.form))).then(() => {
        window.location.reload();
    });
}

async function submitDeleteForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    const token = form.querySelector('input[name="_token"]')?.value;
    if (!token) {
        form.submit();
        return;
    }
    const body = new URLSearchParams();
    body.append('_token', token);
    body.append('_method', 'DELETE');
    await fetch(form.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body,
    });
}

document.querySelectorAll('[data-bulk-select-all]').forEach((toggle) => {
    toggle.addEventListener('change', () => {
        const group = toggle.dataset.bulkSelectAll;
        document.querySelectorAll(`input[data-bulk='${group}']`).forEach((checkbox) => {
            checkbox.checked = toggle.checked;
        });
    });
});

function toggleAdminTools() {
    const menu = document.getElementById('adminToolsMenu');
    if (!menu) return;
    menu.classList.toggle('open');
}

document.addEventListener('click', (event) => {
    const menu = document.getElementById('adminToolsMenu');
    const toggle = document.querySelector('.admin-tools-button');
    if (!menu || !toggle) return;
    if (menu.classList.contains('open') && !menu.contains(event.target) && !toggle.contains(event.target)) {
        menu.classList.remove('open');
    }
});

function openSessionModal(sessionData) {
    const modal = document.getElementById('sessionDetailModal');
    if (!modal) return;

    document.getElementById('sessionOrganizer').textContent = sessionData.organizer || '-';
    document.getElementById('sessionLocation').textContent = sessionData.location || '-';
    document.getElementById('sessionPace').textContent = sessionData.pace || '-';
    document.getElementById('sessionDuration').textContent = sessionData.duration || '-';
    document.getElementById('sessionStatus').textContent = sessionData.status || '-';
    document.getElementById('sessionStart').textContent = sessionData.start || '-';
    document.getElementById('sessionEnd').textContent = sessionData.end || '-';

    const participantsEl = document.getElementById('sessionParticipants');
    participantsEl.innerHTML = '';
    (sessionData.participants || []).forEach((participant) => {
        const item = document.createElement('li');
        item.className = 'flex items-center justify-between border border-slate-200 rounded-lg px-3 py-2';
        item.innerHTML = `<span class="font-semibold">${participant.name || 'Unknown'}</span><span class="text-xs uppercase text-slate-500">${participant.role || 'Participant'}</span>`;
        participantsEl.appendChild(item);
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('modal-open');
}

function closeSessionModal() {
    const modal = document.getElementById('sessionDetailModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('modal-open');
}

document.querySelectorAll('.js-session-view').forEach((btn) => {
    btn.addEventListener('click', () => {
        const data = btn.dataset.session ? JSON.parse(btn.dataset.session) : null;
        if (data) {
            openSessionModal(data);
        }
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeSessionModal();
        document.body.classList.remove('modal-open');
        ['usersModal', 'sessionHistoryModal', 'userCreateModal'].forEach(toggleId => {
            const modal = document.getElementById(toggleId);
            if (modal && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    }
});

document.querySelectorAll('.admin-modal').forEach((modal) => {
    modal.addEventListener('click', (e) => {
        if (e.target !== modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('modal-open');
    });
});

@if ($errors->any() && old('redirect_to') === route('admin.dashboard'))
document.addEventListener('DOMContentLoaded', () => {
    toggleModal('userCreateModal');
});
@endif
</script>

</body>
</html>


