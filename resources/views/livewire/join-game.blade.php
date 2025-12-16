<div class="w-full max-w-md mx-auto px-4 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -mt-8">

    {{-- STAP 1: Roomcode invoeren --}}
    @if($step === 1)
        <section
            class="bg-sky-500 rounded-card shadow-card px-6 py-7 sm:px-8 sm:py-8 text-pure-white"
        >
            <h1 class="text-center text-lg sm:text-2xl font-bold tracking-wide">
                Roomcode
            </h1>

            <p class="mt-2 text-center text-sm sm:text-base">
                Vul hier de roomcode in die je van de organisator hebt gekregen.
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

            <form
                wire:submit="checkPin"
                class="mt-6 space-y-6"
            >
                <div>
                    <label
                        for="pin"
                        class="block text-center text-sm sm:text-base font-semibold tracking-wide"
                    >
                        Voer je 6-cijferige code in
                    </label>

                    <input
                        type="text"
                        id="pin"
                        wire:model="pin"
                        maxlength="6"
                        autocomplete="off"
                        placeholder="6-cijferige code"
                        inputmode="numeric"
                        class="
                            mt-3
                            w-full
                            bg-sky-100 text-sky-900 font-semibold
                            rounded-full px-4 py-2
                            text-base
                            border-0
                            placeholder:text-sky-500
                            focus:outline-none
                            focus:ring-2 focus:ring-offset-2
                            focus:ring-sky-300 focus:ring-offset-sky-500
                        "
                    >

                    @error('pin')
                        <p class="mt-2 text-xs sm:text-sm text-red-900 text-center">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="submit"
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
                >
                    Volgende
                </button>
            </form>
        </section>
    @endif

    {{-- STAP 2: PIN tonen + gebruikersnaam invoeren --}}
    @if($step === 2)
        <div class="space-y-6">

            <section
                class="bg-sky-500 rounded-card shadow-card px-6 py-5 sm:px-8 sm:py-6 text-pure-white"
            >
                <p class="text-center text-sm sm:text-base">
                    Game PIN:
                    <span class="font-bold">{{ $pin }}</span>
                </p>

                <button
                    type="button"
                    wire:click="backToPin"
                    class="
                        mt-4 w-full
                        bg-pure-white text-sky-700
                        font-semibold text-sm sm:text-base
                        py-2.5 rounded-button shadow-card
                        transition
                        hover:shadow-lg hover:-translate-y-0.5
                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                        focus-visible:ring-sky-500 focus-visible:ring-offset-sky-500
                    "
                >
                    Andere PIN gebruiken
                </button>
            </section>

            <section
                class="bg-sky-500 rounded-card shadow-card px-6 py-7 sm:px-8 sm:py-8 text-pure-white"
            >
                <h2 class="text-center text-lg sm:text-2xl font-bold tracking-wide">
                    Gebruikersnaam
                </h2>

                <p class="mt-2 text-center text-sm sm:text-base">
                    Voer hier je gebruikersnaam in om mee te doen met het spel.
                </p>

                <form
                    wire:submit.prevent="join"
                    class="mt-6 space-y-6"
                    novalidate
                >
                    <div>
                        <label
                            for="name"
                            class="block text-center text-sm sm:text-base font-semibold tracking-wide"
                        >
                            Gebruikersnaam
                        </label>

                        <input
                            type="text"
                            id="name"
                            wire:model.defer="name"
                            maxlength="20"
                            placeholder="Gebruikersnaam"
                            inputmode="text"
                            autocomplete="name"
                            class="
                    mt-3
                    w-full
                    bg-sky-100 text-sky-900 font-semibold
                    rounded-full px-4 py-2
                    text-base
                    border-0
                    placeholder:text-sky-500
                    focus:outline-none
                    focus:ring-2 focus:ring-offset-2
                    focus:ring-sky-300 focus:ring-offset-sky-500
                "
                        >

                        {{-- Error message --}}
                        @error('name')
                        <p
                            class="mt-2 text-xs sm:text-sm text-red-900 text-center"
                            aria-live="polite"
                        >
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="
                w-full
                bg-pure-white text-sky-700
                font-semibold text-base sm:text-lg
                py-3 rounded-button shadow-card
                transition
                hover:shadow-lg hover:-translate-y-0.5
                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                focus-visible:ring-sky-500 focus-visible:ring-offset-sky-500
            "
                    >
                        Meedoen met het spel
                    </button>
                </form>
            </section>
        </div>
    @endif

</div>
