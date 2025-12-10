<div class="relative min-h-screen overflow-hidden pb-24" wire:poll.5s.visible="refreshLeaderboard">

    <div class="w-full px-4 pt-6 pb-12 bg-[#2E7D32]"
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


    {{-- TODO: Voeg de ranglijst toe --}}
    <div class="container mx-auto px-4 mt-6 mb-6">
        <div class="max-w-xl mx-auto p-4 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-center text-2xl font-bold">Speler score</h2>
            </div>

            @php
                $board = $leaderboardData ?? $players ?? [];
                // helper to safely get property
                $getName = function($p){ return is_array($p) ? ($p['name'] ?? 'Onbekend') : ($p->name ?? 'Onbekend'); };
                $getScore = function($p){ return is_array($p) ? ($p['score'] ?? 0) : ($p->score ?? 0); };

                $p1 = $board[0] ?? null;
                $p2 = $board[1] ?? null;
                $p3 = $board[2] ?? null;
                $p4 = $board[3] ?? null;
                $p5 = $board[4] ?? null;
            @endphp

            <!-- Podium (Top 3) -->
            <div class="flex justify-center items-end gap-4 mb-6">
                <!-- #3 -->
                <div class="flex flex-col items-center">
                    <div class="bg-gray-300 w-20 h-24 rounded-t-lg flex items-center justify-center font-semibold">
                        #3
                    </div>
                    <span class="mt-2 text-sm text-gray-700 truncate w-28 text-center">
                        {{ $getName($p3) }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $getScore($p3) }} punten</span>
                </div>

                <!-- #1 -->
                <div class="flex flex-col items-center">
                    <div class="bg-yellow-400 w-20 h-32 rounded-t-lg flex items-center justify-center font-bold">
                        #1
                    </div>
                    <span class="mt-2 text-sm text-gray-700 truncate w-32 text-center">
                        {{ $getName($p1) }}
                    </span>
                    <span class="text-sm text-yellow-700 font-bold">{{ $getScore($p1) }} punten</span>
                </div>

                <!-- #2 -->
                <div class="flex flex-col items-center">
                    <div class="bg-orange-300 w-20 h-28 rounded-t-lg flex items-center justify-center font-semibold">
                        #2
                    </div>
                    <span class="mt-2 text-sm text-gray-700 truncate w-28 text-center">
                        {{ $getName($p2) }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $getScore($p2) }} punten</span>
                </div>
            </div>

            <!-- Posities 4 en 5 -->
            <div class="space-y-2 mb-6">
                <div class="flex justify-between bg-gray-100 px-4 py-2 rounded">
                    <div class="flex items-center gap-3">
                        <span class="font-semibold">#04</span>
                        <span class="text-sm">{{ $getName($p4) }}</span>
                    </div>
                    <span class="text-sm font-bold">{{ $getScore($p4) }} pt</span>
                </div>

                <div class="flex justify-between bg-gray-100 px-4 py-2 rounded">
                    <div class="flex items-center gap-3">
                        <span class="font-semibold">#05</span>
                        <span class="text-sm">{{ $getName($p5) }}</span>
                    </div>
                    <span class="text-sm font-bold">{{ $getScore($p5) }} pt</span>
                </div>

                <div class="text-center space-x-4">
                    {{-- extra action button (zelfde als boven) --}}
                    <button type="button" wire:click="showAllScores" class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-blue-200 transition">
                        Zie alle scores..
                    </button>
                </div>
            </div>

            {{-- eventueel: volledige ranglijst renderen als je dat wilt --}}
            {{-- ...existing code... --}}
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

</div>
