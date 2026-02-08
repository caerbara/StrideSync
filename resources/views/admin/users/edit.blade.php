@extends('layouts.app')

@section('title', 'Edit User - StrideSync Admin')

@section('content')
<div class="min-h-screen bg-white text-slate-900 p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900">Edit User</h1>
        <p class="text-slate-500 mt-2">Update user information</p>
    </div>

    <!-- Form Container -->
    <div class="max-w-2xl mx-auto bg-white rounded-lg border border-slate-200 p-8 shadow-sm">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <h3 class="font-semibold mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold mb-2">Name <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900 placeholder-slate-400"
                    required>
                @error('name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold mb-2">Email <span class="text-red-500">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900 placeholder-slate-400"
                    required>
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-semibold mb-2">Phone Number</label>
                <input
                    type="text"
                    id="phone_number"
                    name="phone_number"
                    value="{{ old('phone_number', $user->phone_number) }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900 placeholder-slate-400"
                    placeholder="+60 12-345 6789">
            </div>

            <!-- Password (Optional) -->
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <p class="text-sm text-slate-500 mb-3">Leave password fields empty to keep the current password</p>

                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold mb-2">New Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900 placeholder-slate-400"
                            placeholder="Leave empty to keep current password">
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold mb-2">Confirm Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900 placeholder-slate-400"
                            placeholder="Confirm new password">
                    </div>
                </div>
            </div>

            <!-- Gender -->
            <div>
                <label for="gender" class="block text-sm font-semibold mb-2">Gender</label>
                <select
                    id="gender"
                    name="gender"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900">
                    <option value="">Select gender</option>
                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Average Pace -->
            <div>
                <label for="avg_pace" class="block text-sm font-semibold mb-2">Average Pace</label>
                <input
                    type="text"
                    id="avg_pace"
                    name="avg_pace"
                    value="{{ old('avg_pace', $user->avg_pace) }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900 placeholder-slate-400"
                    placeholder="e.g., 8:30 min/km">
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-semibold mb-2">Location</label>
                <input
                    type="text"
                    id="location"
                    name="location"
                    value="{{ old('location', $user->location) }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 text-slate-900 placeholder-slate-400"
                    placeholder="e.g., Central Park">
            </div>

            <!-- Telegram ID -->
            <div>
                <label for="telegram_id" class="block text-sm font-semibold mb-2">Telegram ID</label>
                <input
                    type="text"
                    id="telegram_id"
                    name="telegram_id"
                    value="{{ old('telegram_id', $user->telegram_id) }}"
                    class="w-full px-4 py-2 bg-slate-100 border border-slate-300 rounded-lg text-slate-700"
                    placeholder="Optional - for Telegram bot users"
                    readonly>
                <p class="text-xs text-slate-500 mt-1">This is read-only and set by the Telegram bot</p>
            </div>

            <!-- Admin Status -->
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <label for="is_admin" class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        id="is_admin"
                        name="is_admin"
                        value="1"
                        {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                        class="w-4 h-4 rounded">
                    <span class="text-sm font-semibold">Administrator Privileges</span>
                </label>
                <p class="text-xs text-slate-500 mt-2">Grant this user admin access to the dashboard</p>
            </div>

            <!-- User Info -->
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-2 text-sm">
                <p><strong>Created:</strong>
                    @if(is_object($user->created_at))
                        {{ $user->created_at->format('M d, Y @ g:i A') }}
                    @else
                        {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y @ g:i A') }}
                    @endif
                </p>
                <p><strong>Last Updated:</strong>
                    @if(is_object($user->updated_at))
                        {{ $user->updated_at->format('M d, Y @ g:i A') }}
                    @else
                        {{ \Carbon\Carbon::parse($user->updated_at)->format('M d, Y @ g:i A') }}
                    @endif
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-4">
                <button
                    type="submit"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Update User
                </button>
                <a
                    href="{{ route('users.index') }}"
                    class="flex-1 text-center bg-slate-200 hover:bg-slate-300 px-6 py-3 rounded-lg font-semibold transition text-slate-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection


