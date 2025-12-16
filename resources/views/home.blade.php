@extends('layouts.app')

@section('content')
    <a href="#maincontent" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-white px-3 py-2 rounded shadow">
        Ga naar inhoud
    </a>

    <div class="min-h-screen flex flex-col bg-white">

        <header role="banner">
            <x-homeNav aria-label="Hoofd navigatie" />
        </header>

        <section class="w-full bg-white" aria-labelledby="page-title">
            {{-- Hero image --}}
            <div class="h-48 sm:h-56 md:h-80 w-full overflow-hidden">
                <img src="{{ asset('images/heroImage.jpg') }}" alt="Natuurgebied" class="w-full h-full object-cover"/>
            </div>

            {{-- Content card die over image en content valt, rechts uitgelijnd met login button --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 sm:-mt-20 md:-mt-44 mb-4 relative z-20 md:flex md:justify-end md:pr-36 lg:pr-64">
                <div class="bg-sky-600 text-white rounded-xl shadow-xl md:shadow-none px-6 py-5 md:px-8 md:py-6 md:max-w-sm relative">
                    <h1 id="page-title" class="text-2xl sm:text-3xl md:text-4xl font-bold">Tijd om te spelen!</h1>
                    <p class="mt-2 text-sm sm:text-base text-sky-100">
                        NatuurMoment is een interactief groepsspel dat iedereen samen door een natuurgebied laat bewegen.
                        Met hun telefoon als gids, en de natuur als speelveld.
                    </p>

                    {{-- Dropdown uitleg --}}
                    <div x-data="{ open: false }" class="mt-3" x-cloak>
                        <button
                            type="button"
                            @click="open = !open"
                            @mouseup="$el.blur()"
                            :aria-expanded="open.toString()"
                            aria-controls="expansion-details"
                            class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-white bg-white/20 px-3 py-1.5 rounded-full transition hover:bg-white/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-sky-600"
                        >
                            <span x-show="!open">Meer uitleg</span>
                            <span x-show="open">Minder uitleg</span>
                            <svg x-show="!open" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg x-show="open" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </button>

                        <div
                            id="expansion-details"
                            x-show="open"
                            x-transition
                            class="mt-2 text-xs sm:text-sm text-sky-100 bg-sky-700 rounded-lg p-3 space-y-2"
                        >
                            <p>
                                Tijdens de route spelen zij twee spellen tegelijk:
                            </p>
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Foto Bingo</strong> – waarbij ze actief zoeken naar wat er écht is</li>
                                <li><strong>Route Quiz</strong> – met korte vragen over hun directe omgeving</li>
                            </ul>
                            <p>
                                De organisator bepaalt de vorm: competitief met tijdsdruk, of juist als gezamenlijke ontdekkingstocht.
                            </p>
                            <p>
                                Aan de opdrachten zijn korte weetjes gekoppeld. Niet alleen om educatie te stimuleren,
                                maar om nieuwsgierigheid te voeden. Zo eindigt het spel niet buiten,
                                maar zet het iets in beweging dat blijft.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main id="maincontent" role="main" class="flex-1 w-full relative z-5 -mt-4 sm:-mt-6 md:-mt-16">
            <div class="max-w-4xl mx-auto w-full">
                <div class="bg-white px-4 sm:px-6 pt-4 sm:pt-6 pb-5 sm:pb-6">

                    {{-- Zoek + filter rij --}}
                    <div class="flex flex-col gap-3 mb-3 sm:mb-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Zoek een locatie</h2>
                        </div>

                        {{-- AJAX Formulier --}}
                        <div id="location-filter-form" class="flex flex-col gap-4 sm:flex-row sm:items-center" aria-label="Zoek en filter locaties">
                            {{-- Zoekveld --}}
                            <div class="relative flex-1">
                                <label for="search" class="sr-only">Zoeken</label>
                                <input
                                    id="search"
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Zoeken"
                                    class="w-full rounded-full border border-gray-300 py-2.5 pl-10 pr-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-action-500 focus:border-transparent"
                                    aria-describedby="search-help"
                                >
                                <span id="search-help" class="sr-only">Typ trefwoorden om locaties te zoeken</span>
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-5 sm:w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.6 3.6a7.5 7.5 0 0013.05 13.05z" />
                                </svg>
                            </span>
                            </div>

                            {{-- Dropdown --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:w-56">
                                <label for="location" class="sr-only">Filter op locatie</label>
                                <select id="location" name="location" class="w-full rounded-full border border-gray-300 py-2.5 px-4 text-sm sm:text-base bg-white focus:outline-none focus:ring-2 focus:ring-action-500 focus:border-transparent focus-visible:ring-2 focus-visible:ring-action-500">
                                    <option value="">Alle locaties</option>
                                    @foreach($locationOptions as $province)
                                        <option value="{{ $province }}" @selected(isset($selectedProvince) && $selectedProvince === $province)>{{ $province }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Filter-chips --}}
                        <div id="active-filters" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    {{-- Titel sectie --}}
                    <div class="mb-2 sm:mb-3 mt-1 sm:mt-2">
                        <h3 id="results-title" tabindex="-1" class="text-lg sm:text-xl font-semibold text-gray-900">
                            Locaties waar je spellen kunt spelen
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Kies een locatie om te zien waar je wandelingen extra leuk worden.
                        </p>
                    </div>

                    {{-- Results container --}}
                    <div id="results" aria-live="polite" aria-busy="false">
                        @include('partials.location-cards', ['locations' => $locations])
                    </div>

                </div>
            </div>
        </main>

        <footer role="contentinfo" class="mt-6">
            <x-homeFooter />
        </footer>
    </div>

    {{-- AJAX + smooth scroll script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const locationSelect = document.getElementById('location');
            const resultsContainer = document.getElementById('results');
            const filtersContainer = document.getElementById('active-filters');

            const updateResults = (page = 1) => {
                const params = new URLSearchParams();
                if (searchInput.value) params.set('search', searchInput.value);
                if (locationSelect.value) params.set('location', locationSelect.value);
                if (page) params.set('page', page);

                // Let screenreaders know content is updating
                resultsContainer.setAttribute('aria-busy', 'true');

                fetch(`{{ route('home') }}?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        resultsContainer.innerHTML = html;

                        // Mark update complete
                        resultsContainer.setAttribute('aria-busy', 'false');

                        // Update active filter chips
                        filtersContainer.innerHTML = '';
                        if (locationSelect.value) {
                            const chip = document.createElement('div');
                            chip.className = "inline-flex items-center gap-2 bg-gray-100 border border-gray-200 rounded-full px-3 py-1 text-xs text-gray-700";
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.setAttribute('aria-label', `Verwijder filter ${locationSelect.value}`);
                            removeBtn.className = "text-gray-400 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-gray-400 rounded";
                            removeBtn.textContent = '×';

                            const label = document.createElement('span');
                            label.className = "font-medium";
                            label.textContent = locationSelect.value;

                            chip.appendChild(label);
                            chip.appendChild(removeBtn);
                            filtersContainer.appendChild(chip);

                            removeBtn.addEventListener('click', () => {
                                locationSelect.value = '';
                                updateResults(1);
                            });
                        }

                        // Focus a logical place after results update (esp. pagination)
                        const title = document.getElementById('results-title');
                        if (title) title.focus({ preventScroll: true });

                        // Update URL zonder reload
                        const newUrl = `${window.location.pathname}?${params.toString()}`;
                        window.history.replaceState(null, '', newUrl);
                    })
                    .catch(() => {
                        resultsContainer.setAttribute('aria-busy', 'false');
                    });
            };

            // Typen of filter wijzigen => altijd terug naar pagina 1
            searchInput.addEventListener('input', () => updateResults(1));
            locationSelect.addEventListener('change', () => updateResults(1));

            // Pagination clicks (links are rendered inside #results, so delegate)
            resultsContainer.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                // Only intercept pagination links
                const url = new URL(link.href);
                const page = url.searchParams.get('page');
                if (!page) return;

                e.preventDefault();
                updateResults(page);
            });

            // Init filter chip bij load als er al een filter actief is
            if (locationSelect.value || searchInput.value) updateResults(1);
        });
    </script>

@endsection
