<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/dom.gif') }}" type="image/gif">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center"
        style="background-image: url('{{ asset('images/login-bg.jpg') }}');">
        <div class="flex flex-col items-center bg-white/90 p-6 rounded-lg shadow-lg mb-6 max-w-md w-full mx-4">
            <a href="/">
                <img src="{{ asset('images/dom.gif') }}" alt="Logo" class="w-24 h-auto mb-4" />
            </a>
            <h1 class="text-xl font-bold text-gray-800 text-center">Hotel de Trânsito de Oficiais de Campinas</h1>
            <h2 class="text-lg font-medium text-gray-600 text-center mt-1">"Hotel de Trânsito Brasilinha"</h2>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>