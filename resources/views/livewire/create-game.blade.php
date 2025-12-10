<div class="w-full max-w-md mx-auto px-4 sm:px-0">
    <section class="bg-sky-500 rounded-card shadow-card px-6 py-7 sm:px-8 sm:py-8 text-pure-white">

        <h1 class="text-center text-lg sm:text-2xl font-bold tracking-wide uppercase">
            Lobby aanmaken
        </h1>

        <p class="mt-2 text-center text-sm sm:text-base">
            Creëer een lobby en deel de code met de groep.
        </p>

        <p class="mt-3 text-center text-xs sm:text-sm text-sky-100/90">
            {{ $location->name }}
        </p>

        @if(session()->has('error'))
            <div
                class="mt-4 bg-pure-white/95 text-red-700 text-sm font-medium px-4 py-3 rounded-card"
                role="alert"
                aria-live="assertive"
            >
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-6 space-y-5">

            {{-- Speelduur-blok --}}
            <div>
                <div
                    class="
                        bg-sky-700/40 rounded-card px-4 py-3
                        flex flex-col gap-3
                        sm:flex-row sm:items-center sm:justify-between
                    "
                >
                    <label
                        for="timer-duration"
                        class="text-sm sm:text-base font-semibold"
                    >
                        Speelduur
                    </label>

                    <div
                        class="
                            flex flex-col gap-2 w-full
                            sm:flex-row sm:items-center sm:justify-end sm:w-auto
                        "
                    >
                        <div class="relative w-full sm:w-44">
                            <select
                                wire:model="timerDuration"
                                id="timer-duration"
                                class="
                                    w-full
                                    appearance-none
                                    bg-sky-100 text-sky-900 font-semibold
                                    rounded-full px-4 py-1.5
                                    text-sm sm:text-base
                                    border-0
                                    focus:outline-none
                                    focus:ring-2 focus:ring-offset-2
                                    focus:ring-sky-300 focus:ring-offset-sky-500
                                "
                            >
                                <option value="">Kies speelduur...</option>
                                @foreach(\App\Livewire\CreateGame::TIMER_DURATIONS as $duration)
                                    <option value="{{ $duration }}">{{ $duration }} minuten</option>
                                @endforeach
                                <option value="0">Zonder tijdslimiet</option>
                            </select>
                        </div>

                        {{-- Extra "minuten" tekst alleen op grotere schermen --}}
                        <span class="hidden sm:inline text-sm sm:text-base">
                            minuten
                        </span>
                    </div>
                </div>

                <p class="mt-2 text-xs sm:text-sm text-sky-100/80">
                    Het spel eindigt automatisch na deze tijd, of handmatig zonder limiet.
                </p>
            </div>

            {{-- Volgende-knop --}}
            <button
                type="button"
                wire:click="createGame"
                class="
                    w-full mt-2
                    bg-pure-white text-sky-700
                    font-semibold text-base sm:text-lg
                    uppercase tracking-wide
                    py-3 rounded-button shadow-card
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                    focus-visible:ring-sky-500 focus-visible:ring-offset-sky-500
                "
                aria-label="Ga naar de volgende stap: lobby aanmaken"
            >
                Volgende
            </button>

            {{-- Annuleren-link --}}
            <a
                href="{{ route('home') }}"
                class="
                    block text-center text-xs sm:text-sm text-sky-100/90 underline mt-1
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                    focus-visible:ring-sky-300 focus-visible:ring-offset-sky-500
                "
            >
                Annuleren
            </a>
        </div>

    </section>
</div>
