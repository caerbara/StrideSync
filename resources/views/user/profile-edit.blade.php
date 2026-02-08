<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - StrideSync</title>
    @vite('resources/css/app.css')
</head>
@php
    $embed = request()->boolean('embed');
@endphp
<body class="{{ $embed ? 'bg-transparent text-black min-h-0' : 'bg-gray-900 text-white min-h-screen' }} flex items-center justify-center">
    <div class="bg-white text-black rounded-xl shadow-xl p-8 w-full {{ $embed ? 'max-w-none' : 'max-w-xl' }}">
        @include('user.partials.profile-edit-form', ['embed' => $embed])
    </div>
</body>
</html>


