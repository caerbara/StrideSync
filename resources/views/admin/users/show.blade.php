@extends('layouts.app')

@section('title', $user->name . ' - StrideSync Admin')

@section('content')
@php
    $locationText = $user->formatLocationText('Not set');
@endphp
<div class="min-h-screen bg-white text-slate-900 p-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-slate-900">{{ $user->name }}</h1>
            <p class="text-slate-500 mt-2">{{ $user->email }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('users.edit', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                Edit
            </a>
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Profile Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h2 class="text-xl font-bold mb-4 text-slate-900">Profile Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-start">
                        <span class="text-slate-500">Name</span>
                        <span class="font-semibold">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between items-start border-t border-slate-200 pt-3">
                        <span class="text-slate-500">Email</span>
                        <span class="font-semibold">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-start border-t border-slate-200 pt-3">
                        <span class="text-slate-500">Status</span>
                        <span class="font-semibold">
                            @if($user->is_admin)
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm">Administrator</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">Regular User</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h2 class="text-xl font-bold mb-4 text-slate-900">Running Profile</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-start">
                        <span class="text-slate-500">Gender</span>
                        <span class="font-semibold">{{ $user->gender ?? 'Not set' }}</span>
                    </div>
                    <div class="flex justify-between items-start border-t border-slate-200 pt-3">
                        <span class="text-slate-500">Average Pace</span>
                        <span class="font-semibold">{{ $user->avg_pace ?? 'Not set' }}</span>
                    </div>
                    <div class="flex justify-between items-start border-t border-slate-200 pt-3">
                        <span class="text-slate-500">Location</span>
                        <span class="font-semibold">{{ $locationText }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h2 class="text-xl font-bold mb-4 text-slate-900">Telegram Bot</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-start">
                        <span class="text-slate-500">Telegram ID</span>
                        <span class="font-semibold">
                            @if($user->telegram_id)
                                <code class="bg-slate-100 px-3 py-1 rounded text-slate-700">{{ $user->telegram_id }}</code>
                            @else
                                <span class="text-slate-500">Not connected</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-start border-t border-slate-200 pt-3">
                        <span class="text-slate-500">Bot Status</span>
                        <span class="font-semibold">
                            @if($user->telegram_id)
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm">Connected</span>
                            @else
                                <span class="bg-slate-200 text-slate-500 px-3 py-1 rounded-full text-sm">Not connected</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Activity & Stats -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold mb-4 text-slate-900">Activity</h2>
                <div class="space-y-4">
                    <div class="bg-slate-50 p-4 rounded">
                        <p class="text-sm text-slate-500">Sessions Created</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $user->runningSessions()->count() }}</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded">
                        <p class="text-sm text-slate-500">Profile Completion</p>
                        <p class="text-3xl font-bold text-blue-600">
                            @if($user->gender && $user->avg_pace && $user->location)
                                100%
                            @elseif($user->gender || $user->avg_pace || $user->location)
                                66%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold mb-4 text-slate-900">Account Timeline</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500">Created</p>
                        <p class="font-semibold">
                            @if(is_object($user->created_at))
                                {{ $user->created_at->format('M d, Y @ g:i A') }}
                            @else
                                {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y @ g:i A') }}
                            @endif
                        </p>
                    </div>
                    <div class="border-t border-slate-200 pt-3">
                        <p class="text-slate-500">Last Updated</p>
                        <p class="font-semibold">
                            @if(is_object($user->updated_at))
                                {{ $user->updated_at->format('M d, Y @ g:i A') }}
                            @else
                                {{ \Carbon\Carbon::parse($user->updated_at)->format('M d, Y @ g:i A') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ route('users.index') }}" class="text-slate-500 hover:text-slate-900 transition">
            Back to Users
        </a>
    </div>
</div>
@endsection
