<footer role="contentinfo" aria-label="Voettekst" class="bg-gray-200 text-gray-900 py-10 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Voor schermlezers: korte kop zodat footer makkelijk te vinden is --}}
        <h2 class="sr-only">Voettekst</h2>

        <div class="md:items-center md:justify-between gap-4">

            {{-- Copyright / credit --}}
            <div class="text-center text-xs text-gray-700">
                <p>
                    &copy;
                    <time datetime="{{ date('Y') }}">{{ date('Y') }}</time>
                    NatuurMoment. Mede mogelijk gemaakt door Natuur Monumenten.
                </p>
            </div>

        </div>
    </div>
</footer>
