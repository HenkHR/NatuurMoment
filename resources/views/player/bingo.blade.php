<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bingokaart</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white font-sans antialiased overflow-hidden">
<main class="relative min-h-screen overflow-hidden">
    
    <div class="w-full px-4 pt-6 pb-12 bg-forest-700"
         style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%);">
        <div class="container max-w-md mx-auto px-4 flex flex-col justify-between relative">
        <h1 class="text-4xl font-bold text-[#FFFFFF] mb-2 text-left">Foto Bingo</h1>
        <!-- Timer Display (if enabled) -->
        @if($game && $game->timer_enabled && $game->timer_ends_at)
            <div class="absolute top-0 right-0">
                <x-game-timer :timerEndsAt="$game->timer_ends_at->toIso8601String()" />
            </div>
        @endif
        </div>
    </div>

    {{--link naar speluitleg--}}
    <div class="flex justify-end mb-4 mt-2 mx-auto max-w-md">
        <a
            href="{{ url('/speluitleg') }}"
            class="text-center bg-forest-700 hover:bg-forest-600 text-white rounded-lg font-semibold transition px-4 py-2 mr-4">
            Speluitleg
        </a>
    </div>

    <!-- Photo Capture Component (includes bingo card) -->
    @livewire('player-photo-capture', [
        'gameId' => $gameId,
        'playerToken' => $playerToken,
        'bingoItemId' => null
    ])

    <nav class="fixed bottom-0 left-0 right-0 bg-[#0076A8]">
        <div class="mx-auto w-full max-w-lg flex justify-around py-4 sm:py-6">
            <a href="{{ route('player.game', $gameId) }}" class="flex items-center justify-center p-2 rounded {{ request()->routeIs('player.game') ? 'bg-sky-500' : '' }}">
                <x-bi-grid alt="Bingo" class="w-8 h-8 sm:w-10 sm:h-10 text-white" />
            </a>
            <a href="{{ route('player.leaderboard', $gameId) }}" class="flex items-center justify-center p-2 rounded {{ request()->routeIs('player.leaderboard') ? 'bg-sky-500' : '' }}">
                <x-lucide-trophy alt="Ranglijst" class="w-8 h-8 sm:w-10 sm:h-10 text-white"/>
            </a>
            <a href="{{ route('player.route', $gameId) }}" class="flex items-center justify-center p-2 rounded {{ request()->routeIs('player.route') ? 'bg-sky-500' : '' }}">
                <x-lucide-route alt="Route" class="w-8 h-8 sm:w-10 sm:h-10 text-white"/>
            </a>
        </div>
    </nav>

</main>
@livewireScripts
</body>
</html>