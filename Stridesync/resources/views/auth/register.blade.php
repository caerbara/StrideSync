<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white h-screen flex items-center justify-center relative overflow-hidden">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/Register-BG.jpg') }}');"></div>

<!-- Registration Form -->
<div class="relative z-20 bg-white/40 p-8 rounded-lg shadow-lg max-w-md w-full text-black backdrop-blur-sm">
    <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>

    <form method="POST" action="{{ route('custom.register') }}">
        @csrf

        <div class="mb-4">
            <label for="name" class="block mb-1 text-sm font-medium">Name</label>
            <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div class="mb-4">
            <label for="email" class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div class="mb-4">
            <label for="password" class="block mb-1 text-sm font-medium">Password</label>
            <input type="password" id="password" name="password" required minlength="6"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
            <p id="password-error" class="text-red-600 text-sm mt-1 hidden">Password must be at least 6 characters.</p>
        </div>


        <div class="mb-6">
            <label for="password_confirmation" class="block mb-1 text-sm font-medium">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Register
        </button>
    </form>

    <p class="text-center text-sm text-gray-700 mt-4">
        Already have an account? <a href="{{ route('login') }}" class="text-green-600 hover:underline">Login here</a>.
    </p>
</div>


</body>
</html>

