<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white h-screen flex items-center justify-center relative overflow-hidden">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/Register-BG.jpg') }}');"></div>

<div class="relative z-20 bg-white/40 p-8 rounded-lg shadow-lg max-w-md w-full text-black backdrop-blur-sm">
    <h2 class="text-2xl font-bold mb-6 text-center">Welcome Back</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('custom.login') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div class="mb-6">
            <label for="password" class="block mb-1 text-sm font-medium">Password</label>
            <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Login
        </button>
    </form>

    <!-- Telegram login section removed -->

    <p class="text-center text-sm text-gray-700 mt-4">
        Don't have an account? <a href="{{ route('register') }}" class="text-green-600 hover:underline">Register here</a>.
    </p>
    <p class="text-center text-sm text-gray-700 mt-2">
        <a href="{{ route('password.forgot') }}" class="text-green-600 hover:underline">Forgot password?</a>
    </p>
</div>

</body>
</html>

