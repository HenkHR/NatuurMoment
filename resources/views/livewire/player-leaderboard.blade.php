<div class="relative min-h-screen overflow-hidden pb-24" wire:poll.5s.visible="refreshLeaderboard">

    <div class="w-full px-4 pt-6 pb-8 bg-[#2E7D32]"
         style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%);">
        <div class="container mx-auto px-4 flex flex-col justify-between items-center">
        <h1 class="text-4xl font-bold text-[#FFFFFF] mb-2 text-left">Scorebord</h1>
        <!-- Timer Display (if enabled) -->
        @if($game && $game->timer_enabled && $game->timer_ends_at)
            <div class="flex justify-end px-4 mb-2">
                <x-game-timer :timerEndsAt="$game->timer_ends_at->toIso8601String()" />
            </div>
        @endif
        </div>
    </div>


    {{-- TODO: Voeg de ranglijst toe --}}
    <div class="container mx-auto px-4 mt-4 mb-6">
        <x-leaderboard :players="$leaderboardData" :isFinished="false" />
    </div>

    <nav class="fixed bottom-0 left-0 right-0 bg-[#0076A8]">
        <div class="mx-auto w-full max-w-lg flex justify-around py-4 sm:py-6">
            <a href="{{ route('player.game', $gameId) }}" class="flex items-center justify-center p-2 rounded {{ request()->routeIs('player.game') ? 'bg-sky-500' : '' }}">
                <x-bi-grid alt="Bingo" class="w-8 h-8 sm:w-10 sm:h-10 text-white" />
            </a>
            <a href="{{ route('home') }}" class="flex items-center justify-center p-2 rounded">
                <x-lucide-home alt="Home" class="w-8 h-8 sm:w-10 sm:h-10 text-white"/>
            </a>
            <a href="{{ route('player.route', $gameId) }}" class="flex items-center justify-center p-2 rounded {{ request()->routeIs('player.route') ? 'bg-sky-500' : '' }}">
                <x-lucide-route alt="Route" class="w-8 h-8 sm:w-10 sm:h-10 text-white"/>
            </a>
            <a href="{{ route('player.leaderboard', $gameId) }}" class="flex items-center justify-center p-2 rounded {{ request()->routeIs('player.leaderboard') ? 'bg-sky-500' : '' }}">
                <x-lucide-trophy alt="Ranglijst" class="w-8 h-8 sm:w-10 sm:h-10 text-white"/>
            </a>
        </div>
    </nav>

</div>
