<div
    wire:poll.5s.visible="loadPlayers"
    class="h-screen w-full bg-white flex flex-col overflow-hidden">

        <!-- Game View -->
        <div class="flex flex-col flex-1 overflow-hidden min-h-0">

            <!-- Header -->
            <div class="w-full px-4 pt-6 pb-8 bg-forest-700 flex-shrink-0" style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), 0 100%);">
                <div class="container max-w-md mx-auto px-4 flex flex-col justify-between relative">
                    <h1 class="text-4xl font-bold text-[#FFFFFF] mb-2 text-left">Spelers</h1>
                    <!-- Timer (right) -->
                    @if($game && $game->timer_enabled && $game->timer_ends_at)
                        <div class="absolute top-0 right-0">
                            <x-game-timer :timerEndsAt="$game->timer_ends_at->toIso8601String()" />
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-row justify-between items-center px-4 py-4 flex-shrink-0 container mx-auto max-w-lg">
                <button
                    wire:click="confirmEndGame"
                    class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg font-medium transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                    </svg>
                    Spel afronden
                </button>

                <button
                    type="button"
                    x-data
                    x-on:click="$dispatch('open-modal', 'rules-modal-bingo')"
                    class="text-center bg-forest-700 hover:bg-forest-600 text-white rounded-lg font-semibold transition px-4 py-2"
                    aria-haspopup="dialog"
                >
                    Speluitleg
                </button>
            </div>

            @if (session('photo-message'))
                <div class="bg-green-500 text-white px-4 py-2 rounded mb-4 mx-4 flex-shrink-0">
                    {{ session('photo-message') }}
                </div>
            @endif

            <!-- Content Section -->
            <section class="flex-1 overflow-hidden min-h-0 px-4 pb-4">
                <div class="container mx-auto max-w-lg h-full flex flex-col">
                    <div class="flex flex-row gap-2 justify-between border-b border-gray-300 pb-2 flex-shrink-0 mb-3">
                        <h2 class="text-xl font-semibold">Spelers</h2>
                        <span class="text-lg text-gray-500">Roomcode: {{ $game->pin }}</span>
                    </div>
                    
                    @if(count($players) > 0)
                        <div class="flex-1 overflow-y-auto min-h-0 pr-2">
                            <div class="space-y-3 pt-2 pb-2">
                        @foreach($players as $player)
                            <div wire:key="player-{{ $player['id'] }}" class="bg-pure-white shadow-card rounded-card overflow-hidden">
                                <!-- Player Header (Accordion Toggle) -->
                                <button
                                    wire:click="togglePlayer({{ $player['id'] }})"
                                    class="w-full text-left flex flex-row justify-between items-center {{ $expandedPlayerId === $player['id'] ? 'bg-sky-500 text-white' : 'bg-surface-medium text-deep-black' }} px-4 py-3 rounded-card focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-pure-white transition">

                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold {{ $expandedPlayerId === $player['id'] ? 'text-white' : 'text-deep-black' }}">{{ $player['name'] }}</span>
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
                                        <span class="text-sm font-semibold {{ $expandedPlayerId === $player['id'] ? 'text-white shadow-none' : 'bg-sky-500 text-white' }} rounded-badge px-2 py-1 shadow-card">Score: {{ $player['score'] }}</span>
                                        <svg class="w-5 h-5 transform transition-transform {{ $expandedPlayerId === $player['id'] ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>

                                </button>

                                <!-- Player Bingo Card (Accordion Content) -->  
                                @if($expandedPlayerId === $player['id'])
                                    <div class="p-4 bg-surface-light flex flex-col items-center justify-center" wire:poll.5s.visible="refreshBingoItems">
                                        <h3 class="text-lg font-semibold mb-3 text-center">Bingokaart</h3>

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
                                            <div class="flex flex-col content-center items-center justify-center w-full max-w-md">
                                                <div class="grid grid-cols-3 gap-3 mx-auto bg-[#e0e0e0] p-2 rounded-lg w-full max-w-md">
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
                                                                class="{{ $statusClass }} rounded-lg shadow w-auto aspect-square
                                                                   text-green-700 font-semibold flex flex-col justify-center items-center text-center
                                                                   hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-gray-300 relative cursor-pointer">
                                                                @if($statusIcon)
                                                                    <span class="absolute top-1 right-1 text-lg">{{ $statusIcon }}</span>
                                                                @endif
                                                                <span class="text-xs">{{ $item['label'] }}</span>
                                                            </button>
                                                        @else
                                                            <div
                                                                wire:key="bingo-item-{{ $item['id'] }}-no-photo"
                                                                class="{{ $statusClass }} rounded-lg shadow w-auto aspect-square
                                                               text-green-700 font-semibold flex flex-col justify-center items-center text-center
                                                               opacity-50 cursor-not-allowed relative">
                                                                <span class="text-xs">{{ $item['label'] }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
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
                        </div>
                    @else
                        <div class="flex-shrink-0">
                            <p class="text-gray-600">Geen spelers in het spel.</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Photo Review Modal -->
        @if($selectedPhoto)
            <div class="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center p-4" wire:click="closePhotoModal">
                <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-auto" @click.stop>
                    <div class="p-6">
                        <div class="flex flex-row justify-between items-center mb-4 relative">
                            <div class="flex flex-col justify-between w-full">
                                <h2 class="text-2xl font-bold">Foto Review</h2>
                                <p class="text-md text-gray-500">{{ $selectedPhoto['bingo_item_label'] }}</p>
                            </div>
                            <button
                                wire:click="closePhotoModal"
                                class="absolute top-2 right-0 text-white text-2xl bg-red-500 hover:bg-red-600 rounded-full p-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
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
                        </div>

                        <div class="flex gap-3 justify-between w-full">
                                <button
                                    wire:click="approvePhoto({{ $selectedPhoto['id'] }})"
                                @if($selectedPhoto['status'] === 'approved') disabled @endif
                                class="bg-green-500 hover:bg-green-600 disabled:bg-green-300 disabled:cursor-not-allowed disabled:opacity-50 text-white px-4 py-2 flex-1 rounded-lg font-semibold transition">
                                    Goedkeuren
                                </button>
                                <button
                                wire:click="rejectPhoto({{ $selectedPhoto['id'] }})"
                                @if($selectedPhoto['status'] === 'rejected') disabled @endif
                                class="bg-red-500 hover:bg-red-600 disabled:bg-red-300 disabled:cursor-not-allowed disabled:opacity-50 text-white px-4 py-2 flex-1 rounded-lg font-semibold transition">
                                Afwijzen
                                </button>
                            </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- End Game Confirmation Modal -->
        <x-end-game-modal :show="$showEndGameModal" />

        <x-game.rules-modal
        name="rules-modal-bingo"
        :rules="config('game.rules')"
        title="Speluitleg"
        maxWidth="2xl"
        />
</div>
