@extends('layouts.app')

@section('title', 'Create User - StrideSync Admin')

@section('content')
<div class="min-h-screen bg-white text-slate-900 p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900">Create New User</h1>
        <p class="text-slate-500 mt-2">Add a new user to the system</p>
    </div>

    <!-- Form Container -->
    <div class="max-w-2xl mx-auto bg-white rounded-lg border border-slate-200 p-8 shadow-sm">
        
        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6">
                <h3 class="font-semibold mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold mb-2">Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900 placeholder-slate-400"
                    placeholder="John Doe"
                    required>
                @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold mb-2">Email <span class="text-red-500">*</span></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900 placeholder-slate-400"
                    placeholder="john@example.com"
                    required>
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-semibold mb-2">Phone Number</label>
                <input 
                    type="text" 
                    id="phone_number" 
                    name="phone_number" 
                    value="{{ old('phone_number') }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900 placeholder-slate-400"
                    placeholder="+60 12-345 6789">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold mb-2">Password <span class="text-red-500">*</span></label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900 placeholder-slate-400"
                    placeholder="Minimum 6 characters"
                    required>
                @error('password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold mb-2">Confirm Password <span class="text-red-500">*</span></label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900 placeholder-slate-400"
                    placeholder="Confirm password"
                    required>
            </div>

            <!-- Gender -->
            <div>
                <label for="gender" class="block text-sm font-semibold mb-2">Gender</label>
                <select 
                    id="gender" 
                    name="gender"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900">
                    <option value="">Select gender</option>
                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Average Pace -->
            <div>
                <label for="avg_pace" class="block text-sm font-semibold mb-2">Average Pace</label>
                <input 
                    type="text" 
                    id="avg_pace" 
                    name="avg_pace" 
                    value="{{ old('avg_pace') }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900 placeholder-slate-400"
                    placeholder="e.g., 8:30 min/km">
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-semibold mb-2">Location</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    value="{{ old('location') }}"
                    class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-300 text-slate-900 placeholder-slate-400"
                    placeholder="e.g., Central Park">
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 px-6 py-3 rounded-lg font-semibold transition">
                    ✓ Create User
                </button>
                <a 
                    href="{{ route('users.index') }}"
                    class="flex-1 text-center bg-slate-200 hover:bg-slate-300 text-slate-900 px-6 py-3 rounded-lg font-semibold transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
