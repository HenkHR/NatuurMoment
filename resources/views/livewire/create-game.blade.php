<div class="w-full max-w-md mx-auto px-4 sm:px-0 mt-8 sm:mt-10">
    <section class="bg-sky-500 rounded-card shadow-card px-6 py-7 sm:px-8 sm:py-8 text-pure-white">

        <h1 class="text-center text-lg sm:text-2xl font-bold tracking-wide uppercase">
            Lobby aanmaken
        </h1>

        <p class="mt-2 text-center text-xs sm:text-sm text-sky-100/90">
            {{ $location->name }}
        </p>

        <p class="mt-2 text-center text-sm sm:text-base">
            Creëer een lobby en deel de code met de groep.
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

        <div class="mt-6 space-y-6">

            <div>
                <div class="bg-sky-700/40 rounded-card px-4 py-4 sm:py-5">
                    <label
                        for="timer-duration"
                        class="block text-center text-sm sm:text-base font-semibold tracking-wide"
                    >
                        Speelduur
                    </label>

                    <div class="mt-4">
                        <select
                            wire:model="timerDuration"
                            id="timer-duration"
                            aria-describedby="timer-duration-help"
                            class="
                                w-full
                                appearance-none
                                bg-sky-100 text-sky-900 font-semibold
                                rounded-full px-4 pr-10 py-1.5
                                text-sm sm:text-base
                                border-0
                                focus:outline-none
                                focus:ring-2 focus:ring-offset-2
                                focus:ring-sky-300 focus:ring-offset-sky-500
                            "
                        >
                            <option value="0">Geen tijdslimiet</option>

                            @foreach(\App\Livewire\CreateGame::TIMER_DURATIONS as $duration)
                                <option value="{{ $duration }}">{{ $duration }} minuten</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p
                    id="timer-duration-help"
                    class="mt-4 text-xs sm:text-sm text-sky-100/80 text-center"
                >
                    Kies hoe lang het spel duurt. Zonder tijdslimiet kan het spel handmatig worden beëindigd.
                </p>

            </div>

            <button
                type="button"
                wire:click="createGame"
                class="
                    w-full
                    bg-pure-white text-sky-700
                    font-semibold text-base sm:text-lg
                    uppercase tracking-wide
                    py-3 rounded-button shadow-card
                    transition
                    hover:shadow-lg hover:-translate-y-0.5
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                    focus-visible:ring-sky-500 focus-visible:ring-offset-sky-500
                "
                aria-label="Ga naar de volgende stap: lobby aanmaken"
            >
                Volgende
            </button>

            <a
                href="{{ route('home') }}"
                class="
                    block text-center text-xs sm:text-sm text-pure-white underline underline-offset-2
                    transition
                    hover:underline-offset-4 hover:text-sky-100
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                    focus-visible:ring-sky-300 focus-visible:ring-offset-sky-500
                "
            >
                Annuleren
            </a>
        </div>

    </section>
</div>
