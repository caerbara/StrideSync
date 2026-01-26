<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrideSync</title>
    @vite('resources/css/app.css')
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col relative overflow-x-hidden">

<div class="relative h-screen w-full overflow-hidden">
    <div class="absolute inset-0 bg-no-repeat bg-cover bg-center z-0" style="background-image: url('{{ asset('images/bridge-bg.jpg') }}');"></div>

    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-b from-transparent to-black z-10"></div>

    <div class="relative z-20 h-full flex items-center justify-center">
        <div class="absolute bottom-6 left-6 z-30 flex flex-col space-y-4 max-w-xs">
            <a href="{{ route('login') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow-md">Login</a>
            <a href="{{ route('register') }}" class="bg-white hover:bg-gray-100 text-black px-5 py-2 rounded-lg shadow-md">Register</a>

            <p class="text-sm text-gray-300 mt-2 text-left">
                Not a user yet? <a href="{{ route('register') }}" class="underline hover:text-white">Register now</a>.
            </p>
        </div>
    </div>
</div>

<div class="w-screen overflow-hidden relative">
    <img src="{{ asset('images/StrideSync UI.jpg') }}" alt="StrideSync UI"
         class="w-full object-cover"
         style="margin-top:-120;">
</div>

</body>
</html>
