<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Course - StrideSync</title>
    @vite('resources/css/app.css')
    <style>
        body { font-family: "Poppins", system-ui, sans-serif; }
        .card {
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .card:active {
            transform: scale(0.98);
        }
        .card.active {
            border-color: #111827;
            box-shadow: none;
        }
    </style>
</head>
<body class="min-h-screen bg-[#8b9095] text-gray-900">
    <div class="max-w-4xl mx-auto px-6 py-10">
        <p class="text-xs uppercase tracking-[0.35em] text-gray-500">The Fast Facts</p>
        <h1 class="mt-3 text-3xl md:text-4xl font-semibold">Running goals: choosing the right race distance</h1>
        <p class="mt-2 text-sm text-gray-600">November 13, 2025</p>

        <p class="mt-6 text-sm leading-7 text-gray-800">
            Running is simple, affordable, and powerful for both physical and mental health. If you are new or
            returning to the sport, choosing a distance goal is one of the best ways to build momentum. Below is a
            clear, practical guide to help you decide between popular race distances and plan your next steps.
        </p>

        <div class="mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <button type="button" class="card rounded-3xl bg-gray-200 text-left p-6 border-2 border-gray-900" data-course="5k">
                    <div class="text-xs uppercase tracking-[0.3em] text-gray-600">5K</div>
                    <h2 class="mt-2 text-lg font-semibold text-gray-900">Fast, focused start</h2>
                    <p class="mt-2 text-sm text-gray-700">Build speed and confidence with minimal training time.</p>
                </button>

                <button type="button" class="card rounded-3xl bg-gray-200 text-left p-6 border-2 border-gray-900" data-course="10k">
                    <div class="text-xs uppercase tracking-[0.3em] text-gray-600">10K</div>
                    <h2 class="mt-2 text-lg font-semibold text-gray-900">Balanced middle ground</h2>
                    <p class="mt-2 text-sm text-gray-700">Speed + endurance with more varied training.</p>
                </button>

                <button type="button" class="card rounded-3xl bg-gray-200 text-left p-6 border-2 border-gray-900" data-course="half">
                    <div class="text-xs uppercase tracking-[0.3em] text-gray-600">Half Marathon</div>
                    <h2 class="mt-2 text-lg font-semibold text-gray-900">Big step up</h2>
                    <p class="mt-2 text-sm text-gray-700">Steady long runs and fueling practice.</p>
                </button>

                <button type="button" class="card rounded-3xl bg-gray-200 text-left p-6 border-2 border-gray-900" data-course="full">
                    <div class="text-xs uppercase tracking-[0.3em] text-gray-600">Marathon</div>
                    <h2 class="mt-2 text-lg font-semibold text-gray-900">Long-game goal</h2>
                    <p class="mt-2 text-sm text-gray-700">Months of consistent mileage and patience.</p>
                </button>
            </div>

            <div id="courseDetail" class="mt-6 rounded-2xl border border-gray-200 bg-white/70 p-5">
                <h3 id="courseTitle" class="text-lg font-semibold text-gray-900">5K: a fast, focused starting point</h3>
                <ul id="courseList" class="mt-3 space-y-2 text-sm text-gray-800 list-disc pl-5">
                    <li>Builds speed and strength while staying short and manageable.</li>
                    <li>Great for beginners, returners, or as a stepping stone to longer races.</li>
                    <li>Shorter training block; around 4 weeks for active runners, longer if you are restarting.</li>
                </ul>
            </div>
        </div>

        <p class="mt-8 text-sm text-gray-800">
            Choose the distance that fits your current fitness, time, and motivation. Train patiently, recover well,
            and enjoy the process—each finish line leads to the next goal.
        </p>

        <div class="mt-8">
            <button type="button" onclick="handleCourseClose()" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                <span>Close</span>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <script>
        (function () {
            var cards = document.querySelectorAll('[data-course]');
            var titleEl = document.getElementById('courseTitle');
            var listEl = document.getElementById('courseList');

            var content = {
                '5k': {
                    title: '5K: a fast, focused starting point',
                    items: [
                        'Builds speed and strength while staying short and manageable.',
                        'Great for beginners, returners, or as a stepping stone to longer races.',
                        'Shorter training block; around 4 weeks for active runners, longer if you are restarting.'
                    ]
                },
                '10k': {
                    title: '10K: the balanced middle ground',
                    items: [
                        'Combines 5K speed with more endurance and pacing control.',
                        'Excellent progression if you feel strong at 5K and want more distance.',
                        'Great preparation for half-marathon and marathon training plans.'
                    ]
                },
                'half': {
                    title: 'Half marathon: the big step up',
                    items: [
                        'Demands steady long runs and good fueling habits.',
                        'Builds confidence before attempting a full marathon.',
                        'Requires more consistent weekly mileage than a 10K plan.'
                    ]
                },
                'full': {
                    title: 'Full marathon: the long-game goal',
                    items: [
                        'Needs structured training, patience, and a steady base of weekly mileage.',
                        'Progress is built over months, not weeks—focus on consistency.',
                        'Shorter races (5K/10K/half) help build the speed and mental readiness you need.'
                    ]
                }
            };

            function setActive(key) {
                cards.forEach(function(card) {
                    card.classList.toggle('active', card.getAttribute('data-course') === key);
                });
                var data = content[key];
                if (!data || !titleEl || !listEl) return;
                titleEl.textContent = data.title;
                listEl.innerHTML = data.items.map(function(item) {
                    return '<li>' + item + '</li>';
                }).join('');
            }

            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    setActive(card.getAttribute('data-course'));
                });
            });

            setActive('5k');
        })();
        function handleCourseClose() {
            if (window.parent && window.parent !== window && typeof window.parent.closeModal === 'function') {
                window.parent.closeModal('courseModal');
                return;
            }
            window.location.href = "{{ url('/') }}";
        }
    </script>
</body>
</html>


