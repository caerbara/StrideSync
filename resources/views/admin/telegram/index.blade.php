<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Telegram Bot Management - StrideSync Admin</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white min-h-screen relative overflow-y-auto">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('{{ asset('images/user-bg.jpg') }}');"></div>

<div class="max-w-6xl mx-auto relative z-10 p-8">
    <div class="mb-4">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-black font-semibold shadow hover:bg-gray-200 transition">
            ← Back to Admin Dashboard
        </a>
    </div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2 text-emerald-300">Telegram Bot Management</h1>
        <p class="text-slate-200">Manage your StrideSync Telegram bot settings and send messages to users</p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Bot Status -->
        <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h3 class="text-slate-600 text-sm font-semibold mb-2">Bot Status</h3>
            <p class="text-2xl font-bold">
                @if($botInfo && $botInfo['ok'])
                    Online
                @else
                    Offline
                @endif
            </p>
            @if($botInfo && $botInfo['ok'])
                <p class="text-slate-500 text-xs mt-2">StrideSyncBot</p>
            @endif
        </div>

        <!-- Webhook Status -->
        <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h3 class="text-slate-600 text-sm font-semibold mb-2">Webhook Status</h3>
            <p class="text-2xl font-bold">
                @if($webhookInfo && $webhookInfo['ok'] && $webhookInfo['result']['url'])
                    Connected
                @else
                    Not Set
                @endif
            </p>
            @if($webhookInfo && $webhookInfo['ok'])
                <p class="text-slate-500 text-xs mt-2">{{ substr($webhookInfo['result']['url'] ?? '', -20) }}</p>
            @endif
        </div>

        <!-- Total Users -->
        <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h3 class="text-slate-600 text-sm font-semibold mb-2">Total Users</h3>
            <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
            <p class="text-slate-500 text-xs mt-2">Registered accounts</p>
        </div>

        <!-- Telegram Linked -->
        <div class="flex flex-col gap-3">
            <a href="{{ route('admin.telegram.reports') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold shadow hover:bg-emerald-700 transition">
                User Report
            </a>
            <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h3 class="text-slate-600 text-sm font-semibold mb-2">Telegram Linked</h3>
            <p class="text-2xl font-bold">{{ $stats['telegram_linked'] }}</p>
            <p class="text-slate-500 text-xs mt-2">{{ $stats['telegram_percentage'] }}% of users</p>
            </div>
        </div>
    </div>

    <!-- Webhook Management -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Webhook Setup -->
        <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h2 class="text-xl font-bold mb-4">Webhook Setup</h2>
            <div class="space-y-4">
                <p class="text-slate-600 text-sm">
                    The webhook allows Telegram to send updates to your StrideSync server in real-time.
                </p>

                <div class="bg-white rounded-xl p-3 text-sm text-slate-700 border-2 border-black break-all">
                    {{ rtrim(config('app.url'), '/') . '/api/telegram/webhook' }}
                </div>

                <div class="flex gap-2">
            <button onclick="setWebhook()" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold transition">
                Set Webhook
            </button>
            <button onclick="removeWebhook()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition">
                Remove Webhook
            </button>
                </div>

                @if($webhookInfo && $webhookInfo['ok'] && $webhookInfo['result']['url'])
                    <div class="bg-emerald-100 border-2 border-emerald-600 rounded-xl p-3 text-emerald-900 text-sm">
                        Webhook is active and receiving updates
                    </div>
                @endif
            </div>
        </div>

        <!-- Bot Info -->
        <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h2 class="text-xl font-bold mb-4">Bot Information</h2>
            <div class="space-y-3 text-sm">
                @if($botInfo && $botInfo['ok'])
                    <div>
                        <p class="text-slate-600">Bot Username:</p>
                        <p class="text-slate-900 font-semibold">StridesyncBot</p>
                    </div>
                    <div>
                        <p class="text-slate-600">Bot ID:</p>
                        <p class="text-slate-900 font-semibold">{{ $botInfo['result']['id'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-600">Can Join Groups:</p>
                        <p class="text-slate-900 font-semibold">{{ $botInfo['result']['can_join_groups'] ? 'Yes' : 'No' }}</p>
                    </div>
                @else
                    <p class="text-red-400">Unable to fetch bot information. Check your token.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Bot Customization -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Short Description -->
        <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h2 class="text-xl font-bold mb-4">Short Description</h2>
            <textarea id="shortDescription" placeholder="Max 120 characters" maxlength="120" class="w-full bg-white text-slate-900 p-3 rounded-xl border-2 border-black focus:ring-2 focus:ring-emerald-300 outline-none mb-3 resize-none" rows="3">Find your perfect running buddy with StrideSync!</textarea>
            <button onclick="updateShortDescription()" class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold transition">
                Update Short Description
            </button>
        </div>

        <!-- Full Description -->
        <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
            <h2 class="text-xl font-bold mb-4">Description</h2>
            <textarea id="description" placeholder="Max 512 characters" maxlength="512" class="w-full bg-white text-slate-900 p-3 rounded-xl border-2 border-black focus:ring-2 focus:ring-emerald-300 outline-none mb-3 resize-none" rows="3">StrideSync helps you find running buddies, manage your running sessions, and connect with other runners in your area!</textarea>
            <button onclick="updateDescription()" class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold transition">
                Update Description
            </button>
        </div>
    </div>

    <!-- Broadcast Message -->
    <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)] mb-8">
        <h2 class="text-xl font-bold mb-4">Broadcast Message to All Users</h2>
        <p class="text-slate-600 text-sm mb-4">Send a message to all users who have Telegram linked ({{ $stats['telegram_linked'] }} users)</p>
        
        <textarea id="broadcastMessage" placeholder="Enter your message (supports HTML formatting)" class="w-full bg-white text-slate-900 p-3 rounded-xl border-2 border-black focus:ring-2 focus:ring-emerald-300 outline-none mb-4 resize-none" rows="4"></textarea>
        
        <div class="bg-white rounded-xl p-3 text-xs text-slate-600 border-2 border-black mb-4">
            <p class="font-semibold mb-2">Supported HTML tags:</p>
            <p>&lt;b&gt;bold&lt;/b&gt; | &lt;i&gt;italic&lt;/i&gt; | &lt;u&gt;underline&lt;/u&gt; | &lt;code&gt;code&lt;/code&gt;</p>
        </div>

        <button onclick="sendBroadcast()" class="w-full px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold text-lg transition">
            Send to All Users
        </button>
    </div>

    <!-- Success/Error Messages -->
    <div id="alertBox"></div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function showAlert(message, type = 'success') {
        const alertBox = document.getElementById('alertBox');
        const bgColor = type === 'success' ? 'ring-2 ring-emerald-400' : 'ring-2 ring-red-400';
        
        const alert = document.createElement('div');
        alert.className = `fixed top-4 right-4 max-w-md bg-white text-slate-900 border-4 border-black rounded-2xl p-4 shadow-[6px_6px_0_rgba(0,0,0,1)] ${bgColor} z-50`;
        alert.innerHTML = `
            <div class="flex justify-between items-start">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-xl ml-4">&times;</button>
            </div>
        `;
        
        alertBox.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    function setWebhook() {
        fetch('/admin/telegram/set-webhook', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1500);
        })
        .catch(e => showAlert('Error: ' + e.message, 'error'));
    }

    function removeWebhook() {
        if (!confirm('Are you sure? This will stop receiving Telegram updates.')) return;
        
        fetch('/admin/telegram/remove-webhook', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1500);
        })
        .catch(e => showAlert('Error: ' + e.message, 'error'));
    }

    function updateDescription() {
        const description = document.getElementById('description').value;
        if (!description) return showAlert('Description cannot be empty', 'error');

        fetch('/admin/telegram/update-description', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ description })
        })
        .then(r => r.json())
        .then(data => showAlert(data.message, data.success ? 'success' : 'error'))
        .catch(e => showAlert('Error: ' + e.message, 'error'));
    }

    function updateShortDescription() {
        const short_description = document.getElementById('shortDescription').value;
        if (!short_description) return showAlert('Short description cannot be empty', 'error');

        fetch('/admin/telegram/update-short-description', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ short_description })
        })
        .then(r => r.json())
        .then(data => showAlert(data.message, data.success ? 'success' : 'error'))
        .catch(e => showAlert('Error: ' + e.message, 'error'));
    }

    function sendBroadcast() {
        const message = document.getElementById('broadcastMessage').value;
        if (!message) return showAlert('Message cannot be empty', 'error');
        if (!confirm('Send this message to all {{ $stats["telegram_linked"] }} users?')) return;

        fetch('/admin/telegram/broadcast', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ message })
        })
        .then(r => r.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) document.getElementById('broadcastMessage').value = '';
        })
        .catch(e => showAlert('Error: ' + e.message, 'error'));
    }
</script>

</body>
</html>


