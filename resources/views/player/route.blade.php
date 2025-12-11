<html lang="nl">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Vragen</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-white">
<main class="relative min-h-screen overflow-hidden pb-24">
    
    <div class="w-full px-4 pt-6 pb-12 bg-[#388E3C]"
         style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%);">
        <div class="container max-w-md mx-auto px-4 flex flex-col justify-between relative">
        <h1 class="text-4xl font-bold text-[#FFFFFF] mb-2 text-left">Route Vragen</h1>
        <!-- Timer Display (if enabled) -->
        @if($game && $game->timer_enabled && $game->timer_ends_at)
            <div class="absolute top-0 right-0">
                <x-game-timer :timerEndsAt="$game->timer_ends_at->toIso8601String()" />
            </div>
        @endif
        </div>
    </div>

    {{-- Game Status Check (polling for finished games) --}}
    @livewire('player-route-check', ['gameId' => $gameId])

    {{-- TODO: Voeg route vragen toe --}}
    <div class="container mx-auto px-4 mt-6 mb-6">
        <div class="text-center py-8">
            <p class="text-gray-600 text-lg">Er zijn nog geen route vragen beschikbaar voor deze locatie.</p>
        </div>
    </div>

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

