@extends('layouts.app')

@section('title', 'Manage Users - StrideSync Admin')

@section('content')
@php
    $embed = request()->boolean('embed');
@endphp
<div class="min-h-screen bg-white text-slate-900 {{ $embed ? 'p-4' : 'p-8' }}">
    <!-- Header -->
    @if (!$embed)
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-slate-900">User Management</h1>
                <p class="text-slate-500 mt-2">Manage all registered users</p>
            </div>
            <a href="{{ route('users.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Add New User
            </a>
        </div>
    @endif

    <!-- Alert Messages -->
    @if (!$embed && $errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!$embed && session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (!$embed && session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">
            <button type="button" id="toggleUserSelect" class="bg-slate-800 hover:bg-slate-900 text-white px-3 py-1 rounded text-xs font-semibold">
                Select
            </button>
            <div class="flex items-center gap-4 hidden" id="userBulkControls">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" id="selectAllUsers" class="accent-slate-600">
                    Select all
                </label>
                <button type="button" id="deleteSelectedUsers" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold">
                    Delete selected
                </button>
            </div>
            <div class="text-xs text-slate-500 font-semibold">
                Admins: {{ $adminCount }} of {{ $totalUsers }}
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm table-fixed">
                <thead class="bg-slate-50">
                    <tr class="border-b border-slate-200">
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-10 hidden" id="userSelectHeader"></th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-12">No.</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-36">Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-24">Role</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-56">Email</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-48">Location</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-28">Telegram</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600 w-32">Joined</th>
                        <th class="px-4 py-2 text-center font-semibold text-slate-600 w-40">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        @php
                            $locationText = $user->formatLocationText('Not set');
                        @endphp
                        <tr class="border-b border-slate-200 hover:bg-slate-50 transition">
                            <td class="px-4 py-2 hidden user-select-cell">
                                <input type="checkbox" class="js-user-select accent-slate-600" data-form="delete-user-{{ $user->id }}">
                            </td>
                            <td class="px-4 py-2 text-slate-900">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-2 font-semibold text-slate-900">{{ $user->name }}</td>
                            <td class="px-4 py-2">
                                @if($user->is_admin)
                                    <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs font-semibold">Admin</span>
                                @else
                                    <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full text-xs font-semibold">User</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-2 text-slate-600 truncate" title="{{ $locationText }}">{{ $locationText }}</td>
                            <td class="px-4 py-2">
                                @if($user->telegram_id)
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">Connected</span>
                                @else
                                    <span class="bg-slate-200 text-slate-500 px-2 py-0.5 rounded-full text-xs font-semibold">Not linked</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-slate-500 whitespace-nowrap">
                                @if(is_object($user->created_at))
                                    {{ $user->created_at->format('M d, Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('users.show', $user->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-0.5 rounded text-xs font-semibold transition">
                                        View
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-0.5 rounded text-xs font-semibold transition">
                                        Edit
                                    </a>
                                    <form id="delete-user-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-0.5 rounded text-xs font-semibold transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-slate-500">
                                No users found. <a href="{{ route('users.create') }}" class="text-emerald-600 hover:underline">Create one now.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                <div class="flex items-center gap-2">
                    {{ $users->links('pagination::simple-tailwind') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Back Button -->
<div class="mt-6 {{ $embed ? 'hidden' : '' }}">
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-900 transition">
        Back to Dashboard
    </a>
</div>
</div>
<script>
const selectAllUsers = document.getElementById('selectAllUsers');
const deleteSelectedUsers = document.getElementById('deleteSelectedUsers');
const toggleUserSelect = document.getElementById('toggleUserSelect');
const userBulkControls = document.getElementById('userBulkControls');
const userSelectHeader = document.getElementById('userSelectHeader');

if (selectAllUsers) {
    selectAllUsers.addEventListener('change', () => {
        document.querySelectorAll('.js-user-select').forEach((checkbox) => {
            checkbox.checked = selectAllUsers.checked;
        });
    });
}

if (toggleUserSelect) {
    toggleUserSelect.addEventListener('click', () => {
        const isHidden = userBulkControls?.classList.contains('hidden');
        userBulkControls?.classList.toggle('hidden');
        userSelectHeader?.classList.toggle('hidden');
        document.querySelectorAll('.user-select-cell').forEach((cell) => {
            cell.classList.toggle('hidden');
        });
        toggleUserSelect.textContent = isHidden ? 'Cancel' : 'Select';
    });
}

if (deleteSelectedUsers) {
    deleteSelectedUsers.addEventListener('click', async () => {
        const selected = Array.from(document.querySelectorAll('.js-user-select:checked'));
        if (selected.length === 0) {
            alert('No users selected.');
            return;
        }
        if (!confirm('Delete selected users?')) {
            return;
        }
        for (const input of selected) {
            await submitDeleteForm(input.dataset.form);
        }
        window.location.reload();
    });
}

async function submitDeleteForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    const token = form.querySelector('input[name="_token"]')?.value;
    if (!token) {
        form.submit();
        return;
    }
    const body = new URLSearchParams();
    body.append('_token', token);
    body.append('_method', 'DELETE');
    await fetch(form.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body,
    });
}
</script>
@endsection


