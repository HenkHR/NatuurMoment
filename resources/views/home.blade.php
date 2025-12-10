@extends('layouts.app')

@section('content')
    <a href="#maincontent" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-white px-3 py-2 rounded shadow">
        Ga naar inhoud
    </a>

    <div class="min-h-screen flex flex-col bg-white">

        <header role="banner">
            <x-homeNav aria-label="Hoofd navigatie" />
        </header>

        <section class="relative w-full" aria-labelledby="page-title">
            <div class="h-40 sm:h-52 md:h-64 w-full overflow-hidden brightness-[70%]">
                <img src="{{ asset('images/heroImage.jpg') }}" alt="Wandelend stel in een groen natuurgebied" class="w-full h-full object-cover"/>
            </div>

            <div class="absolute inset-0 flex items-center justify-center px-4">
                <div class="text-center text-white max-w-xl">
                    <h1 id="page-title" class="text-2xl sm:text-3xl md:text-4xl font-semibold tracking-tight">Spellen</h1>
                    <p class="mt-2 text-sm sm:text-base leading-relaxed">
                        De leukste spellen voor tijdens je wandeltocht in één van de natuurgebieden van Natuur Monumenten.
                    </p>
                </div>
            </div>
        </section>

        <main id="maincontent" role="main" class="flex-1 w-full">
            <div class="max-w-4xl mx-auto w-full">
                <div class="bg-white px-4 sm:px-6 py-5 sm:py-6">
                    <x-ui.breadcrumbs :items="$breadcrumbs" class="hidden md:block mt-4 mb-2 md:mb-4"/>

                    {{-- Zoek + filter rij --}}
                    <div class="flex flex-col gap-4 mb-5 sm:mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Zoek een locatie</h2>
                        </div>

                        {{-- AJAX Formulier --}}
                        <div id="location-filter-form" class="flex flex-col gap-3 sm:flex-row sm:items-center" aria-label="Zoek en filter locaties">
                            {{-- Zoekveld --}}
                            <div class="relative flex-1">
                                <label for="search" class="sr-only">Zoeken</label>
                                <input
                                    id="search"
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Zoeken"
                                    class="w-full rounded-full border border-gray-300 py-2.5 pl-10 pr-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                                    aria-describedby="search-help"
                                >
                                <span id="search-help" class="sr-only">Typ trefwoorden om locaties te zoeken</span>
                                <span class="absolute left-3 top-2.5 sm:top-2.5 text-gray-400" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-5 sm:w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" focusable="false" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.6 3.6a7.5 7.5 0 0013.05 13.05z" />
                                </svg>
                            </span>
                            </div>

                            {{-- Dropdown --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:w-56">
                                <label for="location" class="sr-only">Filter op locatie</label>
                                <select id="location" name="location" class="w-full rounded-full border border-gray-300 py-2.5 px-4 text-sm sm:text-base bg-white focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent">
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
                    <div class="mb-3 sm:mb-4 mt-1">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                            Locaties waar je spellen kunt spelen
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Kies een locatie om te zien waar je wandelingen extra leuk worden.
                        </p>
                    </div>

                    {{-- Results container --}}
                    <div id="results">
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

            const updateResults = () => {
                const params = new URLSearchParams();
                if(searchInput.value) params.set('search', searchInput.value);
                if(locationSelect.value) params.set('location', locationSelect.value);

                fetch(`{{ route('home') }}?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        resultsContainer.innerHTML = html;

                        // Update active filter chips
                        filtersContainer.innerHTML = '';
                        if(locationSelect.value) {
                            const chip = document.createElement('div');
                            chip.className = "inline-flex items-center gap-2 bg-gray-100 border border-gray-200 rounded-full px-3 py-1 text-xs text-gray-700";
                            chip.innerHTML = `
                        <span class="font-medium">${locationSelect.value}</span>
                        <button type="button" class="text-gray-400 hover:text-gray-600">&times;</button>
                    `;
                            filtersContainer.appendChild(chip);

                            chip.querySelector('button').addEventListener('click', () => {
                                locationSelect.value = '';
                                updateResults();
                            });
                        }

                        // Smooth scroll naar resultaten
                        resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

                        // Update URL zonder reload
                        const newUrl = `${window.location.pathname}?${params.toString()}`;
                        window.history.replaceState(null, '', newUrl);
                    });
            };

            searchInput.addEventListener('input', () => updateResults());
            locationSelect.addEventListener('change', () => updateResults());

            // Init filter chip bij load als er al een filter actief is
            if(locationSelect.value) updateResults();
        });
    </script>
@endsection
