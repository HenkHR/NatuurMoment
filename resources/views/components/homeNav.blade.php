<nav class="bg-green-700 fixed top-0 left-0 right-0 z-50" aria-label="Hoofd navigatie">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo / home link --}}
            <a href="{{ url('/') }}"
               class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white"
               aria-label="Naar startpagina Natuur Monumenten">

                <img src="{{ asset('images/logoNM.png') }}"
                     alt="Natuur Monumenten"
                     class="h-10 w-auto" />

                {{-- Extra tekst alleen voor screenreaders --}}
                <span class="sr-only">Natuur Monumenten — startpagina</span>
            </a>

            {{-- Actieknop --}}
            <a href="{{ route('player.join') }}"
               class="bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm py-2 px-4 rounded shadow-card transition
                      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white"
               aria-label="Join game — doe mee aan een spel">
                Join Game
            </a>

        </div>
    </div>
</nav>
