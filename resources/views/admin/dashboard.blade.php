<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StrideSync</title>
    @vite('resources/css/app.css')
    <style>
        .bg-hero {
            background: linear-gradient(180deg, rgba(0,0,0,0.45), rgba(0,0,0,0.85)), url('{{ asset('images/user-bg.jpg') }}');
            background-size: cover;
            background-position: center;
        }
        body.modal-open .admin-floating {
            display: none;
        }
    </style>
</head>
<body class="bg-black text-white h-screen relative overflow-hidden bg-hero">

@php
    $sessionCards = collect($activeSessions)->merge($upcomingSessions)->merge($pastSessions)->unique('session_id');
    $userList = \App\Models\User::where('is_admin', false)->orderBy('created_at', 'desc')->get();
@endphp

<div class="absolute top-10 left-10 z-30 text-left">
    <p class="text-2xl font-semibold text-white mb-1">Hello,</p>
    <p class="text-3xl font-bold text-white">{{ Auth::user()->name ?? 'Administrator' }}</p>
</div>

<div class="absolute top-10 right-10 z-30 flex items-center space-x-3">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
    <h1 class="text-5xl poppins-title tracking-tighter" style="color: #a1e8c5;">STRIDESYNC</h1>
</div>

<div class="absolute top-40 left-10 right-10 bottom-24 z-20 flex flex-col items-center">
    @if($sessionCards->isEmpty())
        <div class="text-slate-200 mt-10">No sessions yet.</div>
    @else
        <div id="carouselWrapper" class="overflow-x-hidden overflow-y-visible w-full max-w-[1680px] py-5">
            <div id="carouselInner" class="flex transition-transform duration-500 ease-in-out">
                @foreach ($sessionCards->chunk(10) as $batch)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 grid-rows-2 gap-4 min-w-full px-4">
                        @foreach ($batch as $session)
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
                            <div class="bg-white rounded-3xl border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)] overflow-hidden hover:scale-105 hover:z-20 transition-transform duration-300">
                                <div class="h-[240px] flex">
                                    <div class="w-1/3 bg-white text-black flex flex-col items-center justify-center p-3">
                                        <p class="text-sm font-semibold">{{ $start->format('M d') }}</p>
                                        <p class="text-sm">{{ $start->format('h:i A') }}</p>
                                    </div>
                                    <div class="w-2/3 bg-[#779286] text-white p-3 flex flex-col justify-center">
                                        <p class="text-left text-sm mb-0.5">Name: {{ $session->user->name ?? 'Unknown' }}</p>
                                        <p class="text-left text-sm mb-0.5">Location: {{ $session->location_name ?? 'Not set' }}</p>
                                        <p class="text-left text-sm mb-0.5">Pace: {{ $session->average_pace ?? 'N/A' }}</p>
                                        <p class="text-left text-sm mb-1">Duration: {{ $session->duration ?? 'N/A' }}</p>

                                        <div class="flex justify-center space-x-2 mt-1">
                                            <button type="button" data-session='@json($sessionPayload)' class="js-session-view bg-gray-700 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[50px] text-center">View</button>
                                            <form action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this session?')" class="bg-red-600 text-white px-3 py-0.5 text-sm rounded hover:brightness-90 transition duration-200 h-[25px] w-[70px]">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-center w-full mt-4">
            <button id="nextButton" class="w-10 h-10 rounded-full bg-white text-black text-xl font-bold shadow hover:scale-110 transition">
                &gt;
            </button>
        </div>
    @endif
</div>

<div class="absolute bottom-10 left-10 z-30">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="relative px-6 py-2 rounded-lg overflow-hidden shadow-lg transition-all duration-300 group">
            <span class="absolute inset-0 bg-gradient-to-r from-black to-white opacity-50 group-hover:opacity-30 rounded-lg"></span>
            <span class="relative z-10 text-white font-semibold tracking-wide">Logout</span>
        </button>
    </form>
</div>

<a href="{{ route('admin.telegram.index') }}" class="admin-floating absolute bottom-52 right-10 z-30 group flex items-center hover:scale-110 transition-transform">
    <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center">
        <span class="text-white text-sm font-semibold">Admin Telegram</span>
    </div>
    <div class="w-[60px] h-[60px] -ml-[30px] bg-gray-900 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-800 transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M22 2 11 13" />
            <path d="M22 2 15 22 11 13 2 9z" />
        </svg>
    </div>
</a>

<button onclick="toggleModal('sessionHistoryModal')" class="admin-floating absolute bottom-32 right-10 z-30 group flex items-center hover:scale-110 transition-transform">
    <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center">
        <span class="text-white text-sm font-semibold">Session History</span>
    </div>
    <div class="w-[60px] h-[60px] -ml-[30px] bg-gray-800 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-700 transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2 2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 11a9 9 0 111.7 4.7" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h4" />
        </svg>
    </div>
</button>

<button onclick="toggleModal('usersModal')" class="admin-floating absolute bottom-10 right-10 z-30 group flex items-center">
    <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center">
        <span class="text-white text-sm font-semibold">User Management</span>
    </div>
    <div class="w-[60px] h-[60px] -ml-[30px] bg-gray-800 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-700 transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20H4v-2a3 3 0 015.356-1.857" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a4 4 0 10-8 0 4 4 0 008 0z" />
        </svg>
    </div>
</button>

<!-- Users Modal -->
<div id="usersModal" class="fixed inset-0 bg-black/70 hidden z-50 items-center justify-center p-4">
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
<div id="userCreateModal" class="fixed inset-0 bg-white/90 hidden z-50 items-center justify-center p-4">
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
<div id="sessionHistoryModal" class="fixed inset-0 bg-black/70 hidden z-50 items-center justify-center p-4">
    <div class="bg-white text-slate-900 rounded-xl shadow-2xl max-w-5xl w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Session History</h3>
            <button onclick="toggleModal('sessionHistoryModal')" class="text-slate-500 hover:text-black text-xl">&times;</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100">
                    <tr class="text-left">
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
                            <td class="px-4 py-3">{{ $session->user->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3">{{ $session->location_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $end->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $session->average_pace ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <button type="button" data-session='@json($sessionPayload)' class="js-session-view inline-block px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">View</button>
                                <form action="{{ route('running_sessions.destroy', $session->session_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete this session?')" class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">No past sessions.</td>
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

<!-- Session Detail Modal -->
<div id="sessionDetailModal" class="fixed inset-0 bg-black/70 hidden z-50 items-center justify-center p-4">
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
    const modals = ['usersModal', 'sessionHistoryModal', 'sessionDetailModal', 'userCreateModal'];
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

@if ($errors->any() && old('redirect_to') === route('admin.dashboard'))
document.addEventListener('DOMContentLoaded', () => {
    toggleModal('userCreateModal');
});
@endif
</script>

</body>
</html>
