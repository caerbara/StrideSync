<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white h-screen relative overflow-hidden">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('{{ asset('images/user-bg.jpg') }}');"></div>

<div class="absolute top-10 left-10 z-30 text-left">
    <p class="text-2xl font-semibold text-white mb-1">Welcome,</p>
    <p class="text-3xl font-bold text-white">{{ Auth::user()->name ?? 'Admin' }}</p>
</div>

<div class="absolute top-10 right-10 z-30 flex items-center space-x-3">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
    <h1 class="text-5xl poppins-title tracking-tighter" style="color: #a1e8c5;">STRIDESYNC</h1>
</div>

<!-- Cards Carousel -->
<div class="absolute top-40 left-10 right-10 bottom-20 z-20 flex flex-col items-center">
    <div id="carouselWrapper" class="overflow-x-hidden overflow-y-visible w-full max-w-[1680px] py-5">
        <div id="carouselInner" class="flex transition-transform duration-500 ease-in-out">
            @foreach ($sessions->chunk(10) as $chunk)
                <div class="grid grid-cols-5 grid-rows-2 gap-4 min-w-full px-4">
                    @foreach ($chunk as $session)
                        <div class="bg-white rounded-3xl border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)] overflow-hidden hover:scale-105 hover:z-20 transition-transform duration-300">
                            <div class="h-[200px] flex">
                                <!-- Left Date Box -->
                                <div class="w-1/3 bg-white text-black flex flex-col items-center justify-center p-2">
                                    <p class="text-xs font-semibold">{{ \Carbon\Carbon::parse($session->start_time)->format('M d') }}</p>
                                    <p class="text-xs">{{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}</p>
                                </div>
                                <!-- Right Info -->
                                <div class="w-2/3 bg-[#779286] text-white p-2 flex flex-col justify-center">
                                    <p class="text-left text-xs mb-0.5">Name: {{ $session->user->name ?? 'N/A' }}</p>
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

                                        <form method="POST" action="{{ route('running_sessions.destroy', $session->session_id) }}">

                                        @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-0.5 text-xs rounded h-[25px] w-[50px]">Delete</button>
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

<!-- Trigger Buttons Container -->
<div class="absolute bottom-10 right-10 z-30 flex flex-col items-end space-y-4">

    <!-- Session History Button -->
    <button onclick="openModal('sessionHistoryModal')" class="group flex items-center">
        <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center space-x-2">
            <span class="text-white text-sm font-semibold">Session History</span>
        </div>
        <div class="w-[60px] h-[60px] -ml-[30px] bg-gray-800 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-700 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8l-6-6H9z" />
            </svg>
        </div>
    </button>

    <!-- List of Users Button -->
    <!-- List of Users Button -->
    <button onclick="openModal('userListModal')" class="group flex items-center">
    <div class="w-[180px] h-[60px] bg-gradient-to-r from-black to-white rounded-lg shadow-lg flex items-center justify-center space-x-2">
            <span class="text-white text-sm font-semibold">List of Users</span>
        </div>
        <div class="w-[60px] h-[60px] -ml-[30px] bg-gray-800 rounded-full flex items-center justify-center shadow-md group-hover:bg-gray-700 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
    </button>

</div>



    <!-- Modals Carousel -->
@foreach ($sessions as $session)
    <div id="modal-{{ $session->session_id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black opacity-60"></div>

        <!-- Modal Content -->
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

            <!-- Close Button -->
            <button
                onclick="closeModal('modal-{{ $session->session_id }}')"
                class="absolute top-2 right-2 text-black hover:text-red-600 font-bold text-lg">&times;
            </button>
        </div>
    </div>
@endforeach

<!-- Session History Modal -->
<div id="sessionHistoryModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('sessionHistoryModal')"></div>

    <div class="relative bg-white text-black rounded-xl p-6 w-[500px] h-[600px] overflow-y-auto z-10">
        <h2 class="text-xl font-bold mb-4 text-center">Session History</h2>

        @if ($pastSessions->isEmpty())
            <p class="text-center">No past sessions available.</p>
        @else
            <ul class="space-y-3">
                @foreach ($pastSessions as $session)
                    <li class="border border-gray-300 rounded-lg p-4 shadow-sm">
                        <p><strong>User:</strong> {{ $session->user->name ?? 'Unknown' }}</p>
                        <p><strong>Location:</strong> {{ $session->location_name }}</p>
                        <p><strong>Start:</strong> {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y h:i A') }}</p>
                        <p><strong>End:</strong> {{ \Carbon\Carbon::parse($session->end_time)->format('M d, Y h:i A') }}</p>
                        <p><strong>Duration:</strong> {{ $session->duration }}</p>
                        <p><strong>Average Pace:</strong> {{ $session->average_pace }}</p>
                    </li>
                @endforeach
            </ul>
        @endif



        <!-- Close Button -->
        <button
            onclick="closeModal('sessionHistoryModal')"
            class="absolute top-2 right-2 text-black hover:text-red-600 font-bold text-lg">&times;
        </button>
    </div>
</div>

<!-- List of Users Modal -->
<div id="userListModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('userListModal')"></div>

    <div class="relative bg-white text-black rounded-xl p-6 w-[900px] h-[600px] overflow-y-auto z-10">
        <h2 class="text-xl font-bold mb-4 text-center">List of Users</h2>

        @if ($users->isEmpty())
            <p class="text-center">No users found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left border">
                    <thead class="bg-gray-200 text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-2 border">No.</th>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">Joined Date</th>
                        <th class="px-4 py-2 border">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $index => $user)
                        <tr class="border-b hover:bg-gray-100 transition">
                            <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border">{{ $user->name }}</td>
                            <td class="px-4 py-2 border">{{ $user->email }}</td>
                            <td class="px-4 py-2 border">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-2 border">
                                <div class="flex space-x-2">
                                    <button onclick="openModal('userModal{{ $user->id }}')" class="bg-blue-500 text-white px-3 py-1 text-xs rounded hover:bg-blue-600">View</button>
                                    <button onclick="openModal('editUserModal{{ $user->id }}')" class="bg-yellow-500 text-white px-3 py-1 text-xs rounded hover:bg-yellow-600">Edit</button>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @foreach ($users as $user)
                    <div id="userModal{{ $user->id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('userModal{{ $user->id }}')"></div>

                        <div class="relative bg-white text-black rounded-xl p-6 w-[400px] z-10">
                            <h2 class="text-xl font-bold mb-4 text-center">User Details</h2>
                            <ul class="space-y-2 text-sm">
                                <li><strong>Name:</strong> {{ $user->name }}</li>
                                <li><strong>Email:</strong> {{ $user->email }}</li>
                                <li><strong>Joined:</strong> {{ $user->created_at->format('M d, Y h:i A') }}</li>
                                <li><strong>Updated:</strong> {{ $user->updated_at->format('M d, Y h:i A') }}</li>
                                <!-- Add more fields as needed -->
                            </ul>

                            <!-- Close Button -->
                            <button
                                onclick="closeModal('userModal{{ $user->id }}')"
                                class="absolute top-2 right-2 text-black hover:text-red-600 font-bold text-lg">&times;
                            </button>
                        </div>
                    </div>

                    <!-- Edit User Modal -->
                    <div id="editUserModal{{ $user->id }}" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="absolute inset-0 bg-black opacity-60" onclick="closeModal('editUserModal{{ $user->id }}')"></div>

                        <div class="relative bg-white text-black rounded-xl p-6 w-[400px] z-10">
                            <h2 class="text-xl font-bold mb-4 text-center">Edit User</h2>
                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="block text-sm font-semibold">Name</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="w-full border border-gray-300 rounded px-3 py-1 mt-1 text-sm">
                                </div>

                                <div class="mb-3">
                                    <label class="block text-sm font-semibold">Email</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="w-full border border-gray-300 rounded px-3 py-1 mt-1 text-sm">
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal('editUserModal{{ $user->id }}')" class="px-3 py-1 text-sm rounded bg-gray-500 text-white hover:bg-gray-600">Cancel</button>
                                    <button type="submit" class="px-3 py-1 text-sm rounded bg-green-600 text-white hover:bg-green-700">Update</button>
                                </div>
                            </form>

                            <button onclick="closeModal('editUserModal{{ $user->id }}')" class="absolute top-2 right-2 text-black hover:text-red-600 font-bold text-lg">&times;</button>
                        </div>
                    </div>
                @endforeach

        @endif

        <!-- Close Button -->
        <button
            onclick="closeModal('userListModal')"
            class="absolute top-2 right-2 text-black hover:text-red-600 font-bold text-lg">&times;
        </button>
    </div>
</div>




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

