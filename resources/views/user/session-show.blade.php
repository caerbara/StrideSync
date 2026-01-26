<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Details - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-white text-black rounded-xl shadow-xl p-8 w-full max-w-2xl space-y-4">
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold mb-1">Session Details</h1>
                <p class="text-sm text-gray-700">Owner: {{ $runningSession->user->name ?? 'Unknown' }}</p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="px-3 py-1 bg-gray-200 text-black rounded hover:bg-gray-300 text-sm">Back</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <p class="font-semibold">Location</p>
                <p>{{ $runningSession->location_name }}</p>
                @if(!is_null($runningSession->distance_km))
                    <p class="text-sm text-gray-700">~{{ $runningSession->distance_km }} km from you</p>
                @endif
            </div>
            <div>
                <p class="font-semibold">Timing</p>
                <p>Start: {{ \Carbon\Carbon::parse($runningSession->start_time)->format('M d, Y h:i A') }}</p>
                <p>End: {{ \Carbon\Carbon::parse($runningSession->end_time)->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <p class="font-semibold">Pace</p>
                <p>{{ $runningSession->average_pace }}</p>
            </div>
            <div>
                <p class="font-semibold">Duration</p>
                <p>{{ $runningSession->duration }}</p>
            </div>
        </div>

        <div>
            <p class="font-semibold mb-2">Joined Users</p>
            @if($runningSession->joinedUsers->isEmpty())
                <p class="text-sm text-gray-700">No one has joined this session yet.</p>
            @else
                <ul class="list-disc ml-5 space-y-1">
                    @foreach($runningSession->joinedUsers as $joined)
                        <li>{{ $joined->user->name ?? 'Unknown' }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <p class="font-semibold mb-2">Reviews</p>
            @if($runningSession->reviews->isEmpty())
                <p class="text-sm text-gray-700">No reviews yet.</p>
            @else
                <ul class="space-y-2">
                    @foreach($runningSession->reviews as $review)
                        <li class="border rounded px-3 py-2">
                            <p class="font-semibold">{{ $review->rating }}/5</p>
                            <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</body>
</html>
