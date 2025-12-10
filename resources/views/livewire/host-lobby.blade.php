<div wire:poll.2s.visible="loadPlayers" class="container mx-auto" x-data="{
    showRoomCodePopup: true,
    copied: false,
    confirmDelete: false,
    playerToDelete: null,
    playerToDeleteName: '',
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        });
    }
}">

    <!-- Room Code Popup (wegklikbaar) -->
    <div
        x-show="showRoomCodePopup"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        @click="showRoomCodePopup = false">
        <div
            class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full text-center"
            @click.stop>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Room Code</h2>
            <p class="text-gray-600 mb-4">Deel deze code met de spelers:</p>
            <div class="bg-forest-500 text-white text-4xl font-bold py-4 px-6 rounded-lg tracking-wider mb-6">
                {{ $pin }}
            </div>
            <button
                @click="copyToClipboard('{{ $pin }}')"
                :class="copied ? 'bg-green-500 text-white' : 'bg-forest-500 hover:bg-forest-600 text-white'"
                class="w-full font-semibold py-3 rounded-lg transition mb-3 flex items-center justify-center gap-2">
                <template x-if="!copied">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Kopieer code
                    </span>
                </template>
                <template x-if="copied">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Gekopieerd!
                    </span>
                </template>
            </button>
            <button
                @click="showRoomCodePopup = false"
                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg transition">
                Sluiten
            </button>
        </div>
    </div>

    <div class="flex flex-col gap-2 p-4 justify-center items-center w-full">
        <h1 class="text-2xl font-bold">Host Lobby</h1>

        <!-- Room Code Display (always visible, clickable to show popup again) -->
        <button
            @click="showRoomCodePopup = true"
            class="text-center bg-forest-500 text-pure-white rounded-card p-2 hover:bg-forest-600 transition cursor-pointer">
            Game PIN: <span class="font-bold bg-forest-400 text-pure-white rounded-card px-2 py-1">{{ $pin }}</span>
        </button>

        <p class="text-gray-600">Spelers in de lobby: {{ $playerCount }}</p>

        @if(session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded w-full max-w-md">{{ session('error') }}</div>
        @endif

        @if(session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded w-full max-w-md">{{ session('message') }}</div>
        @endif

        <!-- Game Info -->
        <div class="w-full max-w-xs bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Speelduur:</span>
                <span class="font-semibold text-gray-700">
                    @if($timerEnabled && $timerDurationMinutes)
                        {{ $timerDurationMinutes }} minuten
                    @else
                        Zonder tijdslimiet
                    @endif
                </span>
            </div>
        </div>

        <!-- Players List -->
        <div class="flex flex-col gap-2 p-4 w-full max-w-md">
            <h3 class="font-semibold text-gray-700">Wachten op spelers...</h3>
            @if(count($players) > 0)
                <ul class="flex flex-col gap-2">
                    @foreach($players as $player)
                        <li wire:key="player-{{ $player['id'] }}" class="text-left flex flex-row justify-between items-center bg-forest-500 text-pure-white rounded-card p-3 w-full">
                            <span class="font-medium">{{ $player['name'] }}</span>
                            <button
                                @click="playerToDelete = {{ $player['id'] }}; playerToDeleteName = '{{ addslashes($player['name']) }}'; confirmDelete = true"
                                class="bg-red-500 hover:bg-red-600 text-pure-white font-semibold text-sm px-3 py-1 rounded-button text-center shadow-card transition">
                                Verwijderen
                            </button>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-center py-4">Nog geen spelers. Deel de PIN met de spelers!</p>
            @endif
        </div>

        <!-- Start Button -->
        <button
            wire:click="startGame"
            class="w-full max-w-xs bg-action-500 hover:bg-action-600 text-pure-white font-semibold text-small py-3 rounded-button text-center shadow-card transition">
            Start spel
        </button>
    </div>

    <!-- Delete Player Confirmation Modal -->
    <div
        x-show="confirmDelete"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        @keyup.escape.window="confirmDelete = false">
        <div
            x-show="confirmDelete"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full"
            @click.stop>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Speler verwijderen?</h3>
            <p class="text-gray-600 mb-6">
                Weet je zeker dat je <span class="font-semibold" x-text="playerToDeleteName"></span> wilt verwijderen uit de lobby?
            </p>
            <div class="flex gap-3">
                <button
                    @click="confirmDelete = false"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg transition">
                    Annuleren
                </button>
                <button
                    @click="$wire.removePlayer(playerToDelete); confirmDelete = false"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-lg transition">
                    Verwijderen
                </button>
            </div>
        </div>
    </div>
</div>
