<footer class="bg-gray-900 text-gray-300 py-10 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">

            {{-- Info --}}
            <div>
                <h3 class="text-white font-semibold text-lg mb-3">
                    NatuurMoment
                </h3>
                <p class="text-sm leading-relaxed">
                    Spellen voor tijdens je wandeltocht — gemaakt voor iedereen die
                    de natuur op een speelse manier wil beleven.
                </p>
            </div>

            {{-- Navigatie --}}
            <div>
                <h3 class="text-white font-semibold text-lg mb-3">
                    Navigatie
                </h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h3 class="text-white font-semibold text-lg mb-3">
                    Contact
                </h3>
            </div>

        </div>

        <div class="border-t border-gray-700 mt-8 pt-4 text-center text-xs text-gray-500">
            © {{ date('Y') }} NatuurMoment. Alle rechten voorbehouden.
        </div>

    </div>
</footer>

