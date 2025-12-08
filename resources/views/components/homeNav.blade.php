<nav class="bg-green-700 fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logoNM.png') }}"
                     alt="NatuurMoment logo"
                     class="h-20 w-auto">
            </a>

            <!-- Menu / Button -->
            <a href="{{ route('player.join') }}"
               class="bg-action-500 text-pure-white font-semibold text-sm py-2 px-3 rounded-button shadow-card transition">
                Join Game
            </a>

        </div>
    </div>
</nav>

