<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white h-screen relative overflow-hidden">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('{{ asset('images/user-bg.jpg') }}');"></div>

<div class="absolute top-10 left-10 z-30 text-left">
    <p class="text-2xl font-semibold text-white mb-1">Hello,</p>
    <p class="text-3xl font-bold text-white">
        {{ Auth::user()->name }}
    </p>
</div>

<div class="absolute top-10 right-10 z-30 flex items-center space-x-3">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
    <h1 class="text-5xl poppins-title tracking-tighter" style="color: #a1e8c5;">STRIDESYNC</h1>
</div>

<!-- Cards Carousel -->
<div class="absolute top-40 left-10 right-10 bottom-20 z-20 flex flex-col items-center">
    <div id="carouselWrapper" class="overflow-x-hidden overflow-y-visible w-full max-w-[1680px] py-5">
        <div id="carouselInner" class="flex transition-transform duration-500 ease-in-out">
            @foreach ($sessions->chunk(10) as $batch)
                <div class="grid grid-cols-5 grid-rows-2 gap-4 min-w-full px-4">
                    @foreach ($batch as $session)
                        <div class="bg-white rounded-3xl border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)] overflow-hidden hover:scale-105 hover:z-20 transition-transform duration-300">

                            <div class="h-[200px] flex">

                                <!-- Left section -->
                                <div class="w-1/3 bg-white text-black flex flex-col items-center justify-center p-2">
                                    <p class="text-xs font-semibold">{{ \Carbon\Carbon::parse($session->start_time)->format('M d') }}</p>
                                    <p class="text-xs">{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}</p>
                                </div>

                                <!-- Right section -->
                                <div class="w-2/3 bg-[#779286] text-white p-2 flex flex-col justify-center">
                                    <p class="text-left text-xs mb-0.5">Name: {{ $session->user->name ?? 'Unknown' }}</p>
                                    <p class="text-left text-xs mb-0.5">Location: {{ $session->location_name }}</p>
                                    <p class="text-left text-xs mb-0.5">Pace: {{ $session->average_pace }}</p>
                                    <p class="text-left text-xs mb-1">Duration: {{ $session->duration }}</p>

                                    <div class="flex justify-center space-x-2 mt-1">
                                        <button
                                            type="button"
                                            onclick="openModal('modal-{{ $session->session_id }}')"
                                            class="bg-gray-700 text-white px-3 py-0.5 text-xs rounded hover:brightness-90 transition duration-200 h-[25px] w-[50px]">
                                            View
                                        </button>
                                        <form action="{{ route('sessions.join', ['session_id' => $session->session_id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-gray-700 text-white px-3 py-0.5 text-xs rounded hover:brightness-90 transition duration-200 h-[25px] w-[50px]">
                                                Join
                                            </button>
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

    <!-- Next button -->
    <button id="nextButton" class="mt-4 w-10 h-10 rounded-full bg-white text-black text-xl font-bold shadow hover:scale-110 transition">
        &gt;
    </button>
</div>

<!-- Modals Carousel -->
@foreach ($sessions as $session)
    <div id="modal-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black opacity-60"></div>

        <!-- Modal -->
        <div class="relative bg-white text-black rounded-xl p-6 w-96 z-10">
            <h2 class="text-lg font-bold mb-3">Joined Users</h2>

            @if ($session->joinedUsers->isEmpty())
                <p>No one has joined this session yet.</p>
            @else
                <ul class="list-disc pl-5 mb-4">
                    @foreach ($session->joinedUsers as $joined)
                        <li>{{ $joined->user->name ?? 'Unknown' }}</li>
                    @endforeach
                </ul>
            @endif

            <!-- Google Maps Button -->
            @if (!empty($session->location_name))
                @php
                    $locationQuery = urlencode($session->location_name);
                @endphp
                <div class="mb-4">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $locationQuery }}"
                       target="_blank"
                       class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200 text-sm">
                        View on Map
                    </a>
                </div>
            @endif

            <!-- Close Button -->
            <button
                onclick="closeModal('modal-{{ $session->session_id }}')"
                class="absolute top-2 right-2 text-black hover:text-red-600 font-bold text-lg">&times;
            </button>
        </div>
    </div>
@endforeach


<!-- Logout Button at Bottom Left -->
<div class="absolute bottom-10 left-10 z-30">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="relative px-6 py-2 rounded-lg overflow-hidden shadow-lg transition-all duration-300 group">
            <span class="absolute inset-0 bg-gradient-to-r from-black to-white opacity-50 group-hover:opacity-30 rounded-lg"></span>
            <span class="relative z-10 text-white font-semibold tracking-wide">Logout</span>
        </button>
    </form>
</div>

<!-- Create Session Button -->
<button onclick="openModal('createSessionModal')" class="absolute bottom-10 right-10 z-30 group flex items-center">
    <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center">
        <span class="text-white text-sm font-semibold">Create Session</span>
    </div>
    <div
        class="w-[60px] h-[60px] -ml-[30px] bg-gray-800 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-700 transition duration-200">
        <span class="text-white text-3xl font-bold leading-none">+</span>
    </div>
</button>

<!-- Create Session Modal -->
<div id="createSessionModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('createSessionModal')"></div>

    <div class="relative bg-white text-black rounded-xl p-6 w-[400px] z-60">
        <button type="button" onclick="closeModal('createSessionModal')" class="absolute top-2 right-2 text-xl font-bold text-gray-700 hover:text-red-600">&times;</button>

        <h2 class="text-lg font-bold mb-4">Create New Session</h2>

        <form action="{{ route('sessions.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="location_name" class="block text-sm font-semibold mb-1">Location</label>
                <input type="text" name="location_name" id="location_name" class="w-full p-2 border rounded" required>
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
                <input type="text" name="average_pace" id="average_pace" placeholder="e.g., 5:30 min/km" class="w-full p-2 border rounded" required>
            </div>

            <div class="mb-4">
                <label for="duration" class="block text-sm font-semibold mb-1">Duration</label>
                <input type="text" name="duration" id="duration" placeholder="e.g., 45 minutes" class="w-full p-2 border rounded" required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('createSessionModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-black">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 rounded hover:bg-green-700 text-white">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Buddy Match Button -->
<button class="absolute bottom-32 right-10 z-30 group flex items-center">
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


<script>
    const nextButton = document.getElementById('nextButton');
    const carouselInner = document.getElementById('carouselInner');

    let currentIndex = 0;
    const totalPages = carouselInner.children.length;

    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % totalPages;
        const offset = currentIndex * carouselInner.clientWidth;
        carouselInner.style.transform = `translateX(-${offset}px)`;
    });

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>

</body>
</html>

