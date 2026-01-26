<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white h-screen flex items-center justify-center relative overflow-hidden">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/Register-BG.jpg') }}');"></div>

<div class="relative z-20 bg-white/40 p-8 rounded-lg shadow-lg max-w-md w-full text-black backdrop-blur-sm">
    <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.reset') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div>
            <label for="tac_code" class="block mb-1 text-sm font-medium">TAC Code (Email)</label>
            <div class="flex gap-2">
                <input type="text" id="tac_code" name="tac_code" value="{{ old('tac_code') }}" required class="flex-1 px-4 py-2 border rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-green-400">
                <button type="button" id="sendForgotTac" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition text-sm">Send TAC</button>
            </div>
            <p id="forgotTacStatus" class="text-xs text-gray-600 mt-1"></p>
        </div>

        <div>
            <label for="password" class="block mb-1 text-sm font-medium">New Password</label>
            <input type="password" id="password" name="password" required minlength="6" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div>
            <label for="password_confirmation" class="block mb-1 text-sm font-medium">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Reset Password
        </button>
    </form>

    <p class="text-center text-sm text-gray-700 mt-4">
        Remembered your password? <a href="{{ route('login') }}" class="text-green-600 hover:underline">Login here</a>.
    </p>
</div>

</body>
</html>

<script>
    const forgotTacBtn = document.getElementById('sendForgotTac');
    const forgotTacStatus = document.getElementById('forgotTacStatus');
    const forgotEmailInput = document.getElementById('email');

    const setForgotStatus = (msg, isError = false) => {
        if (!forgotTacStatus) return;
        forgotTacStatus.textContent = msg;
        forgotTacStatus.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-700');
    };

    if (forgotTacBtn) {
        forgotTacBtn.addEventListener('click', async () => {
            const email = forgotEmailInput ? forgotEmailInput.value.trim() : '';
            if (!email) {
                setForgotStatus('Please enter your email first.', true);
                return;
            }

            const token = document.querySelector('input[name=\"_token\"]')?.value || '';
            setForgotStatus('Sending TAC...');
            try {
                const response = await fetch("{{ route('password.send-tac') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email })
                });
                const data = await response.json();
                if (data.success) {
                    setForgotStatus(data.message || 'TAC sent.');
                } else {
                    setForgotStatus(data.message || 'Unable to send TAC.', true);
                }
            } catch (error) {
                setForgotStatus('Unable to send TAC. Please try again.', true);
            }
        });
    }
</script>
