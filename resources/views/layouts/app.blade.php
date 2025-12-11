<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
    <a
        href="#maincontent"
        class="absolute top-2 left-2 z-[100] bg-white text-black px-4 py-2 rounded shadow
           -translate-y-20 focus:translate-y-0 transition-transform
           focus:outline-none focus:ring-2 focus:ring-green-700"
    >
        Ga naar hoofdinhoud
    </a>

    <div class="min-h-screen bg-surface-light">

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-pure-white shadow-card">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </body>
</html>
