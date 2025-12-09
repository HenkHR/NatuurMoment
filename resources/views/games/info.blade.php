@extends('layouts.game')

@section('title', $game['title'] ?? 'Game info')

@section('content')
    {{-- Skip-link voor toetsenbordgebruikers --}}
    <a href="#maincontent"
       class="absolute top-2 left-2 z-[100] bg-white text-black px-4 py-2 rounded shadow
              -translate-y-20 focus:translate-y-0 transition-transform
              focus:outline-none focus:ring-2 focus:ring-green-700">
        Ga naar hoofdinhoud
    </a>

    <div class="flex-1 bg-surface-light min-h-screen">

        {{-- Header / navigatie landmark --}}
        <header role="banner">
            <x-homeNav aria-label="Hoofd navigatie" />
        </header>

        {{-- HERO --}}
        <section class="relative bg-forest-700 overflow-hidden mt-6 md:mt-8" aria-labelledby="game-title">
            <div class="h-56 md:h-72 lg:h-80 overflow-hidden">
                <img
                    src="{{ asset('images/locatie.png') }}"
                    alt="Natuurgebied Buitenplaats de Tempel"
                    class="w-full h-full object-cover"
                >
            </div>

            <div class="absolute inset-0 bg-gradient-to-t from-deep-black/70 via-deep-black/10 to-transparent"></div>

            <div class="absolute inset-0 flex items-end">
                <div class="w-full max-w-5xl mx-auto px-4 pb-4 md:px-8 md:pb-6
                            flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                    {{-- H1 titel --}}
                    <div class="text-pure-white max-w-md">
                        <h1 id="game-title" class="text-2xl md:text-3xl font-semibold">
                            {{ $game['title'] }}
                        </h1>
                    </div>

                    {{-- Externe link: duidelijk label, focusable --}}
                    <a
                        href="https://www.natuurmonumenten.nl/natuurgebieden/buitenplaats-de-tempel"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="self-start md:self-auto md:ml-auto bg-sky-500 hover:bg-sky-600
                               text-pure-white text-sm md:text-base font-semibold
                               px-4 py-2 rounded-badge shadow-card whitespace-nowrap transition
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700"
                        aria-label="Bezoek {{ $game['location'] }} op Natuur Monumenten (opent in nieuw venster)">
                        {{ $game['location'] }}
                    </a>
                </div>
            </div>
        </section>

        {{-- Hoofdcontent --}}
        <div class="max-w-5xl mx-auto w-full px-4 lg:px-8 pb-20">
            {{-- Breadcrumbs met ARIA --}}
            <x-ui.breadcrumbs
                :items="$breadcrumbs"
                class="mt-4 mb-4 md:mt-6 md:mb-8"
                aria-label="Breadcrumb navigatie"
            />

            <main id="maincontent" role="main" class="mt-4 md:mt-0">
                <x-game.rules-card
                    :rules="$rules"
                    :locationId="$locationId"
                    class="w-full max-w-5xl mx-auto"
                />
            </main>
        </div>

        {{-- Footer landmark --}}
        <footer role="contentinfo" class="mt-6">
            <x-homeFooter />
        </footer>
    </div>
@endsection
