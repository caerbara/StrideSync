@php
    $showTitle = $showTitle ?? true;
    $embed = $embed ?? false;
    $user = $user ?? Auth::user();
    $locationText = $user ? $user->formatLocationText('Not set') : 'Not set';
@endphp

@if($showTitle)
    <h1 class="text-xl font-bold mb-3">Your Profile</h1>
@endif

@if(!$user)
    <p class="text-sm text-gray-600">Profile not available.</p>
@else
<div class="flex flex-col md:flex-row gap-4">
    <dl class="space-y-1.5 flex-1 text-sm">
        <div>
            <dt class="font-semibold text-slate-700">Name</dt>
            <dd>{{ $user->name }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-slate-700">Email</dt>
            <dd>{{ $user->email }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-slate-700">Telegram</dt>
            <dd class="{{ $user->telegram_id ? 'text-green-600' : 'text-red-600' }}">
                {{ $user->telegram_id ? 'Linked' : 'Not linked' }}
            </dd>
        </div>
        <div>
            <dt class="font-semibold text-slate-700">Average Pace</dt>
            <dd>{{ $user->avg_pace ?? 'Not set' }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-slate-700">Gender</dt>
            <dd>{{ $user->gender ?? 'Not set' }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-slate-700">Location</dt>
            <dd>{{ $locationText }}</dd>
        </div>
    </dl>

    <div class="w-full md:w-64 flex flex-col items-center">
        <dt class="font-semibold self-start text-slate-700 text-sm">Photo</dt>
        @if($user->photo_url)
            <img src="{{ $user->photo_url }}" alt="Profile photo" class="mt-2 h-56 w-full object-cover rounded-lg border">
        @else
            <div class="mt-2 h-56 w-full rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-sm text-gray-500">
                No photo uploaded
            </div>
        @endif
    </div>
</div>

@if(!$embed)
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('user.profile.edit') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit Profile</a>
        <form method="POST" action="{{ route('user.profile.delete') }}" onsubmit="return confirm('Delete your profile? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete Profile</button>
        </form>
        <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-gray-200 text-black rounded hover:bg-gray-300">Back to Dashboard</a>
    </div>
@endif
@endif
