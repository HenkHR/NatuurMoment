<div
    wire:poll.10s.visible="loadPlayers"
    class="container mx-auto p-4">

    @if($showLeaderboard)
        <!-- Leaderboard View -->
        <x-leaderboard :players="$leaderboardData" :showContinueButton="true">
            <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-forest-500 hover:bg-forest-600 text-white rounded-lg font-semibold transition">
                Terug naar Home
            </a>
        </x-leaderboard>
    @else
        <!-- Game View -->
        <div class="flex flex-col gap-4">
            <!-- Header with Timer and End Game Button -->
            <div class="flex flex-row justify-between items-center mb-4">
                <!-- End Game Button (left) -->
                <button
                    wire:click="confirmEndGame"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                    </svg>
                    Spel afronden
                </button>

                <!-- Title (center) -->
                <h1 class="text-3xl font-bold text-center">Host Dashboard</h1>

                <!-- Timer (right) -->
                @if($game && $game->timer_enabled && $game->timer_ends_at)
                    <x-game-timer :timerEndsAt="$game->timer_ends_at->toIso8601String()" />
                @else
                    <div class="w-24"></div>
                @endif
            </div>

            @if (session('photo-message'))
                <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">
                    {{ session('photo-message') }}
                </div>
            @endif

            <div class="flex flex-col gap-3">
                <h2 class="text-xl font-semibold">Spelers</h2>
                @if(count($players) > 0)
                    <div class="space-y-2">
                        @foreach($players as $player)
                            <div wire:key="player-{{ $player['id'] }}" class="border border-gray-300 rounded-lg overflow-hidden">
                                <!-- Player Header (Accordion Toggle) -->
                                <button
                                    wire:click="togglePlayer({{ $player['id'] }})"
                                    class="w-full text-left flex flex-row justify-between items-center bg-[#2E7D32] text-white p-4 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold text-lg">{{ $player['name'] }}</span>
                                        @if($player['pending_photos'] > 0)
                                            <span class="bg-yellow-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold" title="{{ $player['pending_photos'] }} foto(s) wachten op goedkeuring">
                                                {{ $player['pending_photos'] }}
                                            </span>
                                        @endif
                                        @if($player['completed'] ?? false)
                                            <span class="bg-green-300 text-green-800 px-2 py-0.5 rounded-full text-xs font-bold">Klaar</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm">Score: {{ $player['score'] }}</span>
                                        <svg class="w-5 h-5 transform transition-transform {{ $expandedPlayerId === $player['id'] ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </button>

                                <!-- Player Bingo Card (Accordion Content) -->
                                @if($expandedPlayerId === $player['id'])
                                    <div class="p-4 bg-gray-50" wire:poll.5s.visible="refreshBingoItems">
                                        <h3 class="text-lg font-semibold mb-3">Bingokaart</h3>

                                        @if($loadingBingoItems)
                                            <div class="flex justify-center items-center py-8">
                                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                                                <span class="ml-3 text-gray-600">Bingokaart laden...</span>
                                            </div>
                                        @else
                                            @php
                                                $bingoItems = $this->getPlayerBingoItems($player['id']);
                                            @endphp

                                            @if($bingoItems->count() > 0)
                                                <div class="grid grid-cols-3 gap-3 max-w-md mx-auto bg-[#e0e0e0] p-2 rounded-lg">
                                                    @foreach($bingoItems as $item)
                                                        @php
                                                            $statusClass = match($item['photo']['status'] ?? null) {
                                                                'approved' => 'bg-green-100 border-green-500 border-2',
                                                                'rejected' => 'bg-red-100 border-red-500 border-2',
                                                                'pending' => 'bg-yellow-100 border-yellow-500 border-2',
                                                                default => 'bg-[#FFFFFF] border-[#e0e0e0]'
                                                            };
                                                            $statusIcon = match($item['photo']['status'] ?? null) {
                                                                'approved' => '✓',
                                                                'rejected' => '✕',
                                                                'pending' => '⏳',
                                                                default => ''
                                                            };
                                                        @endphp
                                                        @if($item['photo'])
                                                            <button
                                                                wire:key="bingo-item-{{ $item['id'] }}-photo-{{ $item['photo']['id'] }}"
                                                                wire:click="selectPhoto({{ $item['photo']['id'] }})"
                                                                class="{{ $statusClass }} rounded-lg shadow w-28 h-28
                                                                   text-green-700 font-semibold flex flex-col justify-center items-center text-center
                                                                   hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 relative cursor-pointer">
                                                                @if($statusIcon)
                                                                    <span class="absolute top-1 right-1 text-lg">{{ $statusIcon }}</span>
                                                                @endif
                                                                <span class="text-sm">{{ $item['label'] }}</span>
                                                            </button>
                                                        @else
                                                            <div
                                                                wire:key="bingo-item-{{ $item['id'] }}-no-photo"
                                                                class="{{ $statusClass }} rounded-lg shadow w-28 h-28
                                                               text-green-700 font-semibold flex flex-col justify-center items-center text-center
                                                               opacity-50 cursor-not-allowed relative">
                                                                <span class="text-sm">{{ $item['label'] }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-600">Geen bingo items gevonden.</p>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">Geen spelers in het spel.</p>
                @endif
            </div>
        </div>

        <!-- Photo Review Modal -->
        @if($selectedPhoto)
            <div class="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center p-4" wire:click="closePhotoModal">
                <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-auto" @click.stop>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-bold">Foto Review - {{ $selectedPhoto['player_name'] }}</h2>
                            <button
                                wire:click="closePhotoModal"
                                class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                        </div>

                        <div class="mb-4">
                            <img
                                src="{{ $selectedPhoto['url'] }}"
                                alt="Foto"
                                class="max-w-full h-auto rounded-lg shadow-lg mx-auto"
                                onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23ddd\' width=\'400\' height=\'300\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'18\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EFoto niet gevonden%3C/text%3E%3C/svg%3E'; console.error('Image failed to load:', '{{ $selectedPhoto['url'] }}');">
                        </div>

                        <div class="flex flex-col gap-2 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">Status:</span>
                                <span class="px-3 py-1 rounded text-sm font-semibold
                                    {{ $selectedPhoto['status'] === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $selectedPhoto['status'] === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $selectedPhoto['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    @if($selectedPhoto['status'] === 'approved')
                                        Goedgekeurd
                                    @elseif($selectedPhoto['status'] === 'rejected')
                                        Afgewezen
                                    @else
                                        In afwachting
                                    @endif
                                </span>
                            </div>
                            @if($selectedPhoto['taken_at'])
                                <div>
                                    <span class="font-semibold">Genomen op:</span>
                                    <span>{{ $selectedPhoto['taken_at']->format('d-m-Y H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        @if($selectedPhoto['status'] === 'pending')
                            <div class="flex gap-3 justify-end">
                                <button
                                    wire:click="rejectPhoto({{ $selectedPhoto['id'] }})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold">
                                    Afwijzen
                                </button>
                                <button
                                    wire:click="approvePhoto({{ $selectedPhoto['id'] }})"
                                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
                                    Goedkeuren
                                </button>
                            </div>
                        @else
                            <div class="flex gap-3 justify-end">
                                <button
                                    wire:click="closePhotoModal"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold">
                                    Sluiten
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- End Game Confirmation Modal -->
        <x-end-game-modal :show="$showEndGameModal" />
    @endif
</div>
