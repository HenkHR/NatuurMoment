@php
    $durationLabel = ($timerEnabled && $timerDurationMinutes)
        ? $timerDurationMinutes . ' minuten'
        : 'Geen tijdslimiet';

    $headerSubtitle = trim(($locationName ?? 'Locatie') . ' | ' . $durationLabel);
@endphp

<div
    wire:poll.2s.visible="loadPlayers"
    class="h-screen w-full bg-surface-light flex flex-col overflow-hidden"
    x-data="{
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
    }"
>
    <x-nav.lobby :title="'Host Lobby'" :subtitle="$headerSubtitle" />

    <main class="flex-1 overflow-hidden min-h-0 pb-28">
        <div class="max-w-5xl mx-auto px-4 lg:px-8 pt-6 pb-4 h-full flex flex-col">

            <!-- Floating Flash Messages -->
            @if(session()->has('error'))
                <div 
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 3000)"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg max-w-md mx-4"
                    role="alert"
                    aria-live="assertive"
                >
                    <div class="flex items-center justify-center">
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session()->has('message'))
                <div 
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 3000)"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg max-w-md mx-4"
                    role="status"
                    aria-live="polite"
                >
                    <div class="flex items-center justify-center">
                        <span class="font-medium">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            <div class="flex flex-row items-center justify-between gap-3 mb-5">
                <button
                    type="button"
                    @click="showRoomCodePopup = true"
                    class="
                        w-[45%] sm:w-[180px] md:w-[190px]
                        bg-sky-500 text-pure-white text-sm font-semibold
                        rounded-button px-4 py-2 shadow-card
                        flex items-center justify-center
                        transition
                        hover:bg-sky-600 hover:shadow-lg hover:-translate-y-0.5
                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                        focus-visible:ring-sky-500 focus-visible:ring-offset-surface-light
                    "
                    aria-label="Toon spelcode {{ $pin }}"
                >
                    Code: {{ $pin }}
                </button>

                <button
                    type="button"
                    x-on:click="$dispatch('open-modal', 'rules-modal-host')"
                    class="
                        w-[45%] sm:w-[180px] md:w-[190px]
                        bg-forest-500 text-pure-white text-sm font-semibold
                        rounded-button px-4 py-2 shadow-card
                        flex items-center justify-center
                        transition
                        hover:bg-forest-600 hover:shadow-lg hover:-translate-y-0.5
                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                        focus-visible:ring-forest-500 focus-visible:ring-offset-surface-light
                    "
                    aria-haspopup="dialog"
                >
                    Speluitleg
                </button>
            </div>

            <section class="bg-pure-white shadow-card rounded-card overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="px-4 pt-4 pb-2 flex-shrink-0">
                    <p class="text-sm font-semibold text-deep-black mb-3">
                        Aantal spelers: {{ $playerCount }}
                    </p>
                </div>

                    @if(count($players) > 0)
                    <div class="flex-1 overflow-y-auto min-h-0 px-4 pb-4">
                        <ul class="space-y-3 pr-2 pt-3 pb-3">
                            @foreach($players as $player)
                                <li
                                    wire:key="player-{{ $player['id'] }}"
                                    class="
                                        flex items-center justify-between
                                        bg-surface-medium rounded-card px-4 py-3 shadow-card
                                    "
                                >
                                    <span class="font-semibold text-deep-black">
                                        {{ $player['name'] }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="
                                            playerToDelete = {{ $player['id'] }};
                                            playerToDeleteName = '{{ addslashes($player['name']) }}';
                                            confirmDelete = true;
                                        "
                                        class="
                                            w-9 h-9 sm:w-10 sm:h-10
                                            bg-red-500 hover:bg-red-600
                                            text-pure-white
                                            rounded-full shadow-card
                                            flex items-center justify-center
                                            transition
                                            hover:shadow-lg hover:-translate-y-0.5
                                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                                            focus-visible:ring-red-500 focus-visible:ring-offset-pure-white
                                        "
                                    >
                                        <x-solar-trash-bin-minimalistic-bold
                                            class="w-6 h-6"
                                            aria-hidden="true"
                                        />

                                        <span class="sr-only">Speler verwijderen</span>
                                    </button>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <div class="px-4 pb-4 flex-shrink-0">
                        <p class="text-sm text-deep-black/70 py-4">
                            Nog geen spelers. Deel de PIN met de spelers!
                        </p>
                    </div>
                    @endif
            </section>
        </div>
    </main>

    <footer class="fixed bottom-0 left-0 right-0 bg-sky-500 py-6 pb-safe">
        <div class="max-w-5xl mx-auto px-4 lg:px-8">
            <button
                type="button"
                wire:click="startGame"
                class="
                    w-full
                    bg-pure-white text-sky-700 font-semibold text-sm md:text-base
                    py-3 rounded-card shadow-card
                    transition
                    hover:shadow-lg hover:-translate-y-0.5
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                    focus-visible:ring-sky-500 focus-visible:ring-offset-sky-500
                "
            >
                Start spel
            </button>
        </div>
    </footer>

    {{-- popup voor de roomcode --}}
    <div
        x-show="showRoomCodePopup"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        @click="showRoomCodePopup = false"
    >
        <div
            class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full text-center"
            @click.stop
        >
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Spelcode</h2>
            <p class="text-gray-600 mb-4">Deel deze code met de spelers:</p>

            <div class="bg-sky-500 text-white text-4xl font-bold py-4 px-6 rounded-lg tracking-wider mb-6">
                {{ $pin }}
            </div>

            <button
                type="button"
                @click="copyToClipboard('{{ $pin }}')"
                :class="copied ? 'bg-green-500 text-white' : 'bg-forest-500 hover:bg-forest-600 text-white'"
                class="w-full font-semibold py-3 rounded-lg transition mb-3 flex items-center justify-center gap-2"
            >
                <template x-if="!copied">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Kopieer code
                    </span>
                </template>
                <template x-if="copied">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"></path>
                        </svg>
                        Gekopieerd!
                    </span>
                </template>
            </button>

            <button
                type="button"
                @click="showRoomCodePopup = false"
                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg transition"
            >
                Sluiten
            </button>
        </div>
    </div>

    <div
        x-show="confirmDelete"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        @keyup.escape.window="confirmDelete = false"
    >
        <div
            x-show="confirmDelete"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full"
            @click.stop
        >
            <h3 class="text-lg font-bold text-gray-800 mb-2">Speler verwijderen?</h3>
            <p class="text-gray-600 mb-6">
                Weet je zeker dat je
                <span class="font-semibold" x-text="playerToDeleteName"></span>
                wilt verwijderen uit de lobby?
            </p>

            <div class="flex gap-3">
                <button
                    type="button"
                    @click="confirmDelete = false"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg transition"
                >
                    Annuleren
                </button>
                <button
                    type="button"
                    @click="$wire.removePlayer(playerToDelete); confirmDelete = false"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-lg transition"
                >
                    Verwijderen
                </button>
            </div>
        </div>
    </div>

    <x-game.rules-modal
    name="rules-modal-host"
    :rules="$rules"
    title="Speluitleg"
    maxWidth="2xl"
    />

</div>
 
