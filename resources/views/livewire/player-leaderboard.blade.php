<div class="h-screen w-full bg-white flex flex-col overflow-hidden" wire:poll.5s.visible="refreshLeaderboard">
    
    <!-- Header -->
    <div class="w-full px-4 pt-6 pb-8 bg-forest-700 flex-shrink-0"
         style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%);">
        <div class="container max-w-md mx-auto px-4 flex flex-col justify-between relative">
            <h1 class="text-4xl font-bold text-[#FFFFFF] mb-2 text-left">Scorebord</h1>
        <!-- Timer Display (if enabled) -->
        @if($game && $game->timer_enabled && $game->timer_ends_at)
                <div class="absolute top-0 right-0">
                <x-game-timer :timerEndsAt="$game->timer_ends_at->toIso8601String()" />
            </div>
        @endif
        </div>
    </div>

    <!-- Content -->
    <section class="flex-1 w-full pt-4 px-4 pb-24 relative z-10 overflow-hidden min-h-0">
        <div class="container max-w-md mx-auto px-4 h-full flex flex-col">
            <x-leaderboard 
                :players="$leaderboardData" 
                :isFinished="false"
                class="h-full flex flex-col"
            />
    </div>
    </section>

    <!-- Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-[#0076A8] mt-4 pb-safe">
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

</div>
