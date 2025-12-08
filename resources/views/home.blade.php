@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex flex-col bg-gray-50">

        <x-homeNav />

        {{-- HERO --}}
        <section class="relative w-full">
            <div class="h-40 sm:h-52 md:h-64 w-full bg-cover bg-center"
                 style="background-image: url('{{ asset('images/heroImage.jpg') }}');">
            </div>
            {{-- Tekst gecentreerd --}}
            <div class="absolute inset-0 flex items-center justify-center px-4">
                <div class="text-center text-white max-w-xl">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold tracking-tight">
                        Spellen
                    </h1>
                    <p class="mt-2 text-sm sm:text-base leading-relaxed">
                        De leukste spellen voor tijdens je wandeltocht in één van de natuurgebieden van Natuur Monumenten.
                    </p>
                </div>
            </div>
        </section>

        {{-- INHOUD --}}
        <div class="flex-1 w-full">
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

                        <form method="GET"
                              action="{{ route('home') }}"
                              class="flex flex-col gap-3 sm:flex-row sm:items-center">

                            {{-- Zoekveld (volledige breedte op mobiel) --}}
                            <div class="relative flex-1">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Zoeken"
                                    class="w-full rounded-full border border-gray-300 py-2.5 pl-10 pr-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                                >
                                <span class="absolute left-3 top-2.5 sm:top-2.5 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-5 sm:w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.6 3.6a7.5 7.5 0 0013.05 13.05z" />
                                    </svg>
                                </span>

                                {{-- chip voor gekozen locatie, optioneel (alleen tonen als hij er is) --}}
                                @if($selectedLocation)
                                    <div
                                        class="mt-2 inline-flex items-center gap-2 bg-gray-100 border border-gray-200 rounded-full px-3 py-1 text-xs text-gray-700">
                                        <span class="font-medium">
                                            {{ $selectedLocation->name }}
                                        </span>
                                        <button type="submit" name="location" value="" class="text-gray-400 hover:text-gray-600">
                                            &times;
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Locatie filter dropdown (op mobiel onder het zoekveld) --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:w-56">
                                <select
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
                        <div class="grid gap-3 sm:gap-4 sm:grid-cols-2">
                            @foreach($locations as $location)
                                <article class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col">
                                    <div class="bg-green-700 text-white px-4 py-2">
                                        <h4 class="font-semibold text-sm sm:text-base">
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
                                        <a href="{{ route('games.info') }}"
                                            type="button"
                                            class="inline-flex w-full justify-center rounded-md bg-orange-500 hover:bg-orange-600 text-white text-xs sm:text-sm font-medium py-2.5">
                                            Bekijk spel
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <x-homeFooter />
    </div>
@endsection
