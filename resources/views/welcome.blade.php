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
        .welcome-hero-bg {
            background-image: url('{{ asset('images/bridge-bg.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #0b0f14;
        }
        @media (max-width: 640px) {
            .welcome-hero-bg {
                background-size: cover;
                background-position: center;
            }
        }
        .funfact-panel {
            background: linear-gradient(135deg, #2c2c2c 0%, #1f1f1f 45%, #141414 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        .funfact-title {
            font-size: clamp(1.6rem, 4vw, 2.4rem);
            letter-spacing: 0.18em;
        }
        .funfact-mono {
            font-family: "Courier New", monospace;
            letter-spacing: 0.08em;
        }
        .funfact-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 18px;
        }
        @media (min-width: 768px) {
            .funfact-grid {
                grid-template-columns: 1.1fr 0.9fr;
            }
        }
        .funfact-stamp {
            letter-spacing: 0.2em;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col relative overflow-x-hidden">

<div class="relative h-screen w-full overflow-hidden">
    <div class="absolute inset-0 welcome-hero-bg z-0"></div>

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
            <div class="rounded-3xl bg-gradient-to-br from-gray-200 to-gray-400 text-black p-8 min-h-[220px] shadow-[6px_6px_0_rgba(0,0,0,1)] border-2 border-black">
                <div class="text-4xl mb-4">💬</div>
                <h3 class="text-lg font-semibold tracking-[0.15em] uppercase">Feedback</h3>
                <p class="text-sm text-gray-800 mt-2">Random runner feedback from recent sessions.</p>
                <div class="mt-4">
                    @if(isset($reviews) && $reviews->isNotEmpty())
                        <div id="feedback-rotator" class="text-base text-gray-900">
                            @foreach($reviews as $review)
                                <div class="feedback-item {{ $loop->first ? '' : 'hidden' }}">
                                    <div class="font-semibold">
                                        {{ $review->user->name ?? 'Runner' }}
                                        <span class="ml-2 text-amber-600">
                                            {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', max(0, 5 - (int) $review->rating)) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-700 mt-1">
                                        {{ $review->comment ?: 'No comment provided.' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-700">No reviews yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<section class="relative z-10 bg-black text-white py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl md:text-4xl poppins-title tracking-tighter mb-8" style="color: #a1e8c5;">INQUIRY</h2>

        <div class="funfact-panel p-6 md:p-10">
            <div class="funfact-grid">
                <div class="rounded-2xl overflow-hidden bg-gray-900">
                    <img src="{{ asset('images/user-bg.jpg') }}" alt="Runners in action" class="w-full h-full object-cover grayscale">
                </div>
                <div class="flex flex-col justify-center gap-6">
                    <div class="funfact-title funfact-mono text-[#a1e8c5]">GOT QUESTIONS?</div>
                    <div class="space-y-5">
                        <div>
                            <div class="flex items-center gap-3 text-[#a1e8c5] funfact-mono text-sm uppercase tracking-[0.2em]">
                                <span>📍</span>
                                <span>Write Us</span>
                            </div>
                            <p class="text-sm text-gray-200 mt-2">Stridesync.ent, Jalan Delima 1, 72120 Jempol, Negeri Sembilan</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 text-[#a1e8c5] funfact-mono text-sm uppercase tracking-[0.2em]">
                                <span>✉️</span>
                                <span>Send An Email</span>
                            </div>
                            <p class="text-sm text-gray-200 mt-2">StrideSynCO@gmail.com</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 text-[#a1e8c5] funfact-mono text-sm uppercase tracking-[0.2em]">
                                <span>📞</span>
                                <span>Call Us</span>
                            </div>
                            <p class="text-sm text-gray-200 mt-2">123-456-7890</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 funfact-mono text-2xl md:text-4xl leading-tight text-white">
                <div>STRIDE SYNC</div>
                <div>STRIDE SYNC</div>
                <div class="funfact-stamp text-[#a1e8c5]">STRIDESYNC</div>
            </div>
            <div class="mt-4 text-xs uppercase tracking-[0.25em] text-gray-300">Jempol, Negeri Sembilan · November 13, 2025</div>
        </div>
    </div>
</section>

<script>
    (function() {
        const items = document.querySelectorAll('#feedback-rotator .feedback-item');
        if (!items || items.length <= 1) return;
        let index = 0;
        setInterval(() => {
            items[index].classList.add('hidden');
            index = (index + 1) % items.length;
            items[index].classList.remove('hidden');
        }, 4000);
    })();
</script>

</body>
</html>
