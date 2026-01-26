<div class="space-y-4">
    <!-- Session Header -->
    <div class="border-b border-slate-600 pb-4">
        <h4 class="font-semibold mb-2" style="color: #a1e8c5;">Session Information</h4>
        <div class="space-y-2 text-sm">
            <p><strong>Organizer:</strong> <span class="text-slate-300">{{ $session->user->name ?? 'Unknown' }}</span></p>
            <p><strong>Location:</strong> <span class="text-slate-300">{{ $session->location_name ?? 'N/A' }}</span></p>
            <p><strong>Average Pace:</strong> <span class="text-slate-300">{{ $session->average_pace ?? 'N/A' }}</span></p>
            <p><strong>Duration:</strong> <span class="text-slate-300">{{ $session->duration ?? 'N/A' }}</span></p>
        </div>
    </div>

    <!-- Schedule Details -->
    <div class="border-b border-slate-600 pb-4">
        <h4 class="font-semibold mb-2" style="color: #a1e8c5;">Schedule</h4>
        <div class="space-y-2 text-sm">
            <p><strong>Starts:</strong> <span class="text-slate-300">
                @if(is_object($session->start_time))
                    {{ $session->start_time->format('M d, Y @ g:i A') }}
                @else
                    {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y @ g:i A') }}
                @endif
            </span></p>
            <p><strong>Ends:</strong> <span class="text-slate-300">
                @if(is_object($session->end_time))
                    {{ $session->end_time->format('M d, Y @ g:i A') }}
                @else
                    {{ \Carbon\Carbon::parse($session->end_time)->format('M d, Y @ g:i A') }}
                @endif
            </span></p>
            <p><strong>Status:</strong>
                @if(is_object($session->start_time) && is_object($session->end_time))
                    @if($session->start_time <= now() && $session->end_time >= now())
                        <span class="inline-block ml-2 bg-orange-900 text-orange-200 px-2 py-1 rounded-full text-xs font-semibold">🔴 ACTIVE</span>
                    @elseif($session->start_time > now())
                        <span class="inline-block ml-2 bg-blue-900 text-blue-200 px-2 py-1 rounded-full text-xs font-semibold">📅 UPCOMING</span>
                    @else
                        <span class="inline-block ml-2 bg-slate-900 text-slate-300 px-2 py-1 rounded-full text-xs font-semibold">✓ COMPLETED</span>
                    @endif
                @else
                    @php
                        $startTime = is_object($session->start_time) ? $session->start_time : \Carbon\Carbon::parse($session->start_time);
                        $endTime = is_object($session->end_time) ? $session->end_time : \Carbon\Carbon::parse($session->end_time);
                    @endphp
                    @if($startTime <= now() && $endTime >= now())
                        <span class="inline-block ml-2 bg-orange-900 text-orange-200 px-2 py-1 rounded-full text-xs font-semibold">🔴 ACTIVE</span>
                    @elseif($startTime > now())
                        <span class="inline-block ml-2 bg-blue-900 text-blue-200 px-2 py-1 rounded-full text-xs font-semibold">📅 UPCOMING</span>
                    @else
                        <span class="inline-block ml-2 bg-slate-900 text-slate-300 px-2 py-1 rounded-full text-xs font-semibold">✓ COMPLETED</span>
                    @endif
                @endif
            </p>
        </div>
    </div>

    <!-- Participants -->
    <div>
        <h4 class="font-semibold mb-3" style="color: #a1e8c5;">Participants ({{ $session->joinedUsers()->count() + 1 }})</h4>
        <div class="bg-slate-700 bg-opacity-30 p-3 rounded max-h-40 overflow-y-auto">
            <div class="space-y-2 text-sm">
                <!-- Organizer -->
                <div class="flex items-center justify-between p-2 bg-emerald-900 bg-opacity-30 rounded">
                    <span class="font-semibold">{{ $session->user->name ?? 'Unknown' }}</span>
                    <span class="text-xs bg-emerald-900 px-2 py-1 rounded">👤 Organizer</span>
                </div>

                <!-- Joined Users -->
                @forelse($session->joinedUsers as $joined)
                    <div class="flex items-center justify-between p-2 border border-slate-600 rounded">
                        <span>{{ $joined->user->name ?? 'Unknown' }}</span>
                        <span class="text-xs text-slate-400">{{ $joined->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-2">Only the organizer</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
