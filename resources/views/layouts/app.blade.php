<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'StrideSync Admin')</title>
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="bg-slate-900 text-white min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
