<div class="space-y-4">
    <!-- Profile Section -->
    <div class="border-b border-slate-600 pb-4">
        <h4 class="font-semibold mb-3" style="color: #a1e8c5;">Profile Information</h4>
        <div class="space-y-2 text-sm">
            <p><strong>Name:</strong> <span class="text-slate-300">{{ $user->name }}</span></p>
            <p><strong>Email:</strong> <span class="text-slate-300">{{ $user->email }}</span></p>
            <p><strong>Telegram ID:</strong> <span class="text-slate-300">{{ $user->telegram_id ?? 'Not linked' }}</span></p>
            <p><strong>Joined:</strong> <span class="text-slate-300">
                @if(is_object($user->created_at))
                    {{ $user->created_at->format('M d, Y @ g:i A') }}
                @else
                    {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y @ g:i A') }}
                @endif
            </span></p>
        </div>
    </div>

    <!-- Running Profile Section -->
    <div class="border-b border-slate-600 pb-4">
        <h4 class="font-semibold mb-3" style="color: #a1e8c5;">Running Profile</h4>
        <div class="space-y-2 text-sm">
            <p><strong>Gender:</strong> <span class="text-slate-300">{{ $user->gender ?? '—' }}</span></p>
            <p><strong>Average Pace:</strong> <span class="text-slate-300">{{ $user->avg_pace ?? '—' }}</span></p>
            <p><strong>Location:</strong> <span class="text-slate-300">{{ $user->formatLocationText('Not set') }}</span></p>
        </div>
    </div>

    <!-- Telegram State -->
    <div class="border-b border-slate-600 pb-4">
        <h4 class="font-semibold mb-3" style="color: #a1e8c5;">Telegram Status</h4>
        <p class="text-sm">
            <strong>Current State:</strong>
            <span class="text-slate-300">{{ $user->telegram_state ?? 'Never interacted' }}</span>
        </p>
    </div>

    <!-- Sessions & Participation -->
    <div>
        <h4 class="font-semibold mb-3" style="color: #a1e8c5;">Activity</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="bg-slate-700 bg-opacity-30 p-3 rounded">
                <p class="text-slate-400 text-xs">Sessions Created</p>
                <p class="text-2xl font-bold" style="color: #a1e8c5;">{{ $user->runningSessions()->count() }}</p>
            </div>
            @if(isset($user->joinedSessions))
                <div class="bg-slate-700 bg-opacity-30 p-3 rounded">
                    <p class="text-slate-400 text-xs">Sessions Joined</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $user->joinedSessions()->count() ?? 0 }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
