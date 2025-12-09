@extends('layouts.app')

@section('content')
    {{-- Skip link voor keyboard- en screenreadergebruikers --}}
    <a href="#maincontent" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-white px-3 py-2 rounded shadow">
        Ga naar inhoud
    </a>

    <div class="min-h-screen flex flex-col bg-gray-50">

        {{-- Header / navigatie landmark --}}
        <header role="banner">
            <x-homeNav aria-label="Hoofd navigatie" />
        </header>

        {{-- HERO --}}
        <section class="relative w-full" aria-labelledby="page-title">
            {{-- Gebruik <img> zodat we alt-tekst kunnen geven (toegankelijker dan alleen CSS background-image) --}}
            <div class="h-40 sm:h-52 md:h-64 w-full overflow-hidden">
                <img
                    src="{{ asset('images/heroImage.jpg') }}"
                    alt="Wandelend stel in een groen natuurgebied"
                    class="w-full h-full object-cover"
                />
            </div>

            {{-- Tekst gecentreerd; h1 heeft id voor aria-labelledby --}}
            <div class="absolute inset-0 flex items-center justify-center px-4">
                <div class="text-center text-white max-w-xl">
                    <h1 id="page-title" class="text-2xl sm:text-3xl md:text-4xl font-semibold tracking-tight">
                        Spellen
                    </h1>
                    <p class="mt-2 text-sm sm:text-base leading-relaxed">
                        De leukste spellen voor tijdens je wandeltocht in één van de natuurgebieden van Natuur Monumenten.
                    </p>
                </div>
            </div>
        </section>

        {{-- INHOUD als main landmark --}}
        <main id="maincontent" role="main" class="flex-1 w-full">
            <div class="max-w-4xl mx-auto w-full ">
                <div class="bg-white rounded-2xl shadow-md sm:shadow-lg px-4 sm:px-6 py-5 sm:py-6">

                    {{-- Zoek + filter rij --}}
                    <div class="flex flex-col gap-4 mb-5 sm:mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                                Zoek een locatie
                            </h2>
                            @if($selectedLocation)
                                <p class="text-xs sm:text-sm text-gray-500">
                                    Gefilterd op: <span class="font-medium text-gray-700">{{ $selectedLocation->name }}</span>
                                </p>
                            @endif
                        </div>

                        {{-- Formulier: labels toegevoegd (visueel verborgen maar zichtbaar voor assistive tech) --}}
                        <form method="GET"
                              action="{{ route('home') }}"
                              class="flex flex-col gap-3 sm:flex-row sm:items-center"
                              aria-label="Zoek en filter locaties">

                            {{-- Zoekveld (volledige breedte op mobiel) --}}
                            <div class="relative flex-1">
                                <label for="search" class="sr-only">Zoeken</label>
                                <input
                                    id="search"
                                    type="text"
                                    name="search"
                                    value="{{ old('search', $search) }}"
                                    placeholder="Zoeken"
                                    class="w-full rounded-full border border-gray-300 py-2.5 pl-10 pr-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                                    aria-describedby="search-help"
                                >

                                <span id="search-help" class="sr-only">Typ trefwoorden om locaties te zoeken</span>

                                {{-- Zoek-icoon is decoratief voor screenreaders --}}
                                <span class="absolute left-3 top-2.5 sm:top-2.5 text-gray-400" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-5 sm:w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" focusable="false" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.6 3.6a7.5 7.5 0 0013.05 13.05z" />
                                    </svg>
                                </span>

                                {{-- chip voor gekozen locatie, optioneel (alleen tonen als hij er is) --}}
                                @if($selectedLocation)
                                    <div
                                        class="mt-2 inline-flex items-center gap-2 bg-gray-100 border border-gray-200 rounded-full px-3 py-1 text-xs text-gray-700"
                                        role="status"
                                        aria-live="polite"
                                    >
                                        <span class="font-medium">
                                            {{ $selectedLocation->name }}
                                        </span>
                                        {{-- Zorg dat de knop een duidelijke aria-label heeft en geen lege value opstelt --}}
                                        <button type="submit" name="location" value=""
                                                class="text-gray-400 hover:text-gray-600"
                                                aria-label="Verwijder locatiefilter {{ $selectedLocation->name }}">
                                            &times;
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Locatie filter dropdown (op mobiel onder het zoekveld) --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:w-56">
                                <label for="location" class="sr-only">Filter op locatie</label>
                                <select
                                    id="location"
                                    name="location"
                                    class="w-full rounded-full border border-gray-300 py-2.5 px-4 text-sm sm:text-base bg-white focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                                >
                                    <option value="">Alle locaties</option>
                                    @foreach($locationOptions as $locationOption)
                                        <option
                                            value="{{ $locationOption->id }}"
                                            @selected(optional($selectedLocation)->id === $locationOption->id)
                                        >
                                            {{ $locationOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Mobiel: duidelijke primaire knop onder de filters --}}
                            <button type="submit"
                                    class="sm:hidden inline-flex justify-center rounded-full bg-green-700 hover:bg-green-800 text-white text-sm font-medium py-2.5 px-5">
                                Filters toepassen
                            </button>
                        </form>
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

                    {{-- Cards: single column op mobiel, 2 kolommen op grotere schermen --}}
                    @if($locations->count())
                        <div class="grid gap-3 sm:gap-4 sm:grid-cols-2" id="results" aria-live="polite">
                            @foreach($locations as $location)
                                <article class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col"
                                         aria-labelledby="loc-{{ $location->id }}-title">
                                    <div class="bg-green-700 text-white px-4 py-2">
                                        <h4 id="loc-{{ $location->id }}-title" class="font-semibold text-sm sm:text-base">
                                            {{ $location->name }}
                                        </h4>
                                    </div>

                                    <div class="flex-1 px-4 pt-3 pb-3 text-xs sm:text-sm">
                                        @if($location->description)
                                            <p class="text-gray-700 leading-relaxed">
                                                {{ $location->description }}
                                            </p>
                                        @else
                                            <p class="text-gray-500">
                                                Hier kun je verschillende spellen spelen tijdens je wandeling.
                                            </p>
                                        @endif
                                    </div>

                                    <div class="px-4 pb-4">
                                        {{-- Gebruik een duidelijke linktekst (context inbegrepen) en geen type="button" op <a> --}}
                                        <a href="{{ route('games.info', $location->id) }}"
                                           class="inline-flex w-full justify-center rounded-md bg-orange-500 hover:bg-orange-600 text-white text-xs sm:text-sm font-medium py-2.5"
                                           aria-label="Bekijk spel bij {{ $location->name }}">
                                            Bekijk spel
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-600">Geen locaties gevonden.</p>
                    @endif
                </div>
            </div>
        </main>

        {{-- Footer landmark --}}
        <footer role="contentinfo" class="mt-6">
            <x-homeFooter />
        </footer>
    </div>
@endsection
