<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrideSync</title>
    @vite('resources/css/app.css')
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col relative overflow-x-hidden">

<div class="relative h-screen w-full overflow-hidden">
    <div class="absolute inset-0 bg-no-repeat bg-cover bg-center z-0" style="background-image: url('{{ asset('images/bridge-bg.jpg') }}');"></div>

    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-b from-transparent to-black z-10"></div>

    <div class="relative z-20 h-full flex items-center justify-center">
        <div class="absolute bottom-6 left-6 z-30 flex flex-col space-y-4 max-w-xs">
            <a href="{{ route('login') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow-md">Login</a>
            <a href="{{ route('register') }}" class="bg-white hover:bg-gray-100 text-black px-5 py-2 rounded-lg shadow-md">Register</a>

            <p class="text-sm text-gray-300 mt-2 text-left">
                Not a user yet? <a href="{{ route('register') }}" class="underline hover:text-white">Register now</a>.
            </p>
        </div>
    </div>
</div>

<section class="relative z-10 bg-gradient-to-b from-black via-[#0b0b0b] to-[#1a1a1a] text-white py-20 px-6">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/40 to-black/70 pointer-events-none"></div>
    <div class="relative">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl md:text-4xl poppins-title tracking-tighter mb-8" style="color: #a1e8c5;">THE FAST FACTS</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('event.calendar') }}"
               class="group block rounded-3xl bg-gradient-to-br from-gray-200 to-gray-400 text-black p-8 min-h-[220px] shadow-[6px_6px_0_rgba(0,0,0,1)] border-2 border-black transition-transform duration-200 hover:-translate-y-1 hover:shadow-[10px_10px_0_rgba(0,0,0,1)]">
                <div class="text-4xl mb-4">📅</div>
                <h3 class="text-lg font-semibold tracking-[0.15em] uppercase">The Schedule</h3>
                <p class="text-sm text-gray-800 mt-2">Take a look at this year's marathon calendar.</p>
                <span class="inline-block mt-6 px-4 py-2 rounded-lg bg-black text-white text-xs font-semibold tracking-widest group-hover:bg-gray-900">Go To Calendar</span>
            </a>

            <a href="{{ route('course') }}"
               class="group block rounded-3xl bg-gradient-to-br from-gray-200 to-gray-400 text-black p-8 min-h-[220px] shadow-[6px_6px_0_rgba(0,0,0,1)] border-2 border-black transition-transform duration-200 hover:-translate-y-1 hover:shadow-[10px_10px_0_rgba(0,0,0,1)]">
                <div class="text-4xl mb-4">🏁</div>
                <h3 class="text-lg font-semibold tracking-[0.15em] uppercase">The Course</h3>
                <p class="text-sm text-gray-800 mt-2">Read important information on the course and distance.</p>
                <span class="inline-block mt-6 px-4 py-2 rounded-lg bg-black text-white text-xs font-semibold tracking-widest group-hover:bg-gray-900">Explore Course</span>
            </a>

            <a href="https://checkpointspot.asia" target="_blank" rel="noopener noreferrer"
               class="group block rounded-3xl bg-gradient-to-br from-gray-200 to-gray-400 text-black p-8 min-h-[220px] shadow-[6px_6px_0_rgba(0,0,0,1)] border-2 border-black transition-transform duration-200 hover:-translate-y-1 hover:shadow-[10px_10px_0_rgba(0,0,0,1)]">
                <div class="text-4xl mb-4">📝</div>
                <h3 class="text-lg font-semibold tracking-[0.15em] uppercase">Registration</h3>
                <p class="text-sm text-gray-800 mt-2">Find out when and where to purchase marathon tickets.</p>
                <span class="inline-block mt-6 px-4 py-2 rounded-lg bg-black text-white text-xs font-semibold tracking-widest group-hover:bg-gray-900">Learn More</span>
            </a>
            <a href="{{ route('event.calendar') }}"
               class="group block rounded-3xl bg-gradient-to-br from-gray-200 to-gray-400 text-black p-8 min-h-[220px] shadow-[6px_6px_0_rgba(0,0,0,1)] border-2 border-black transition-transform duration-200 hover:-translate-y-1 hover:shadow-[10px_10px_0_rgba(0,0,0,1)]">
                <div class="text-4xl mb-4">💬</div>
                <h3 class="text-lg font-semibold tracking-[0.15em] uppercase">Feedback</h3>
                <p class="text-sm text-gray-800 mt-2">Random runner feedback from recent sessions.</p>
                <span class="inline-block mt-6 px-4 py-2 rounded-lg bg-black text-white text-xs font-semibold tracking-widest group-hover:bg-gray-900">View Feedback</span>
            </a>
        </div>
    </div>
    </div>
</section>

</body>
</html>
