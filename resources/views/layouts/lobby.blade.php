<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Lobby')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{--
        CRITICAL: Dit is een LIVEWIRE layout

        @livewireScripts (onderaan) laadt AUTOMATISCH Alpine.js.
        VOEG GEEN handmatige Alpine.start() toe - dit breekt wire:click!

        De app.js heeft defensive checks om dubbele initialisatie te voorkomen.
        @see docs/ALPINE_ARCHITECTURE.md
    --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-surface-light overflow-hidden">
    <div class="min-h-screen flex flex-col">
        @yield('content')
    </div>

    {{-- Alpine.js wordt AUTOMATISCH geladen door @livewireScripts --}}
    @livewireScripts
</body>
</html>
