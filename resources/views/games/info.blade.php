@extends('layouts.game')

@section('title', $game['title'] ?? 'Game info')

@section('content')
    <a href="#maincontent"
       class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[100] focus:bg-white focus:text-black focus:px-4 focus:py-2 focus:rounded focus:shadow
              focus:outline-none focus:ring-2 focus:ring-green-700">
        Ga naar hoofdinhoud
    </a>

    <div class="flex-1 bg-surface-light">

        <header role="banner">
            <x-homeNav aria-label="Hoofd navigatie" />
        </header>

        <section class="relative bg-forest-700 overflow-hidden mt-6 md:mt-8" aria-labelledby="game-title">
            <div class="h-56 md:h-72 lg:h-80 overflow-hidden">
                <img
                    src="{{ $location->image_path ? Storage::url($location->image_path) : asset('images/locatie.png') }}"
                    alt="{{ $location->name }}"
                    class="w-full h-full object-cover"
                >
            </div>

            <div class="absolute inset-0 bg-gradient-to-t from-deep-black/70 via-deep-black/10 to-transparent"></div>

            <div class="absolute inset-0 flex items-end">
                <div class="w-full max-w-5xl mx-auto px-4 pb-4 md:px-8 md:pb-6
                            flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                    <div class="text-pure-white max-w-md">
                        <h1 id="game-title" class="text-2xl md:text-3xl font-semibold">
                            {{ $game['title'] }}
                        </h1>
                    </div>

                    @if($location->url)
                        <a
                            href="{{ $location->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="self-start md:self-auto md:ml-auto bg-sky-500 hover:bg-sky-600
                               text-pure-white text-sm md:text-base font-semibold
                               px-4 py-2 rounded-badge shadow-card whitespace-nowrap transition
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700
                               inline-flex items-center gap-2"
                            aria-label="Bezoek {{ $location->name }} op Natuurmonumenten (opent in nieuw venster)">
                            {{ $location->name }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <div class="max-w-5xl mx-auto px-4 md:px-8">
            <div class="mt-4 mb-4 md:mt-6 md:mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-white bg-sky-500 hover:bg-sky-600 px-4 py-2 rounded-[10px] transition shadow-sm
                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-green-700"
                    aria-label="Terug naar alle locaties"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Alle locaties
                </a>

                <button
                    type="button"
                    x-data
                    x-on:click="$dispatch('open-modal', 'host-info-modal')"
                    class="w-full sm:w-auto bg-forest-500 text-pure-white text-sm font-semibold
                        rounded-button px-4 py-2 shadow-card
                        inline-flex items-center justify-center gap-2
                        transition hover:bg-forest-600 hover:shadow-lg hover:-translate-y-0.5
                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                        focus-visible:ring-forest-500"
                    aria-haspopup="dialog"
                    aria-label="Host informatie bekijken"
                >
                    <span>Organisator info</span>
                    <svg class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>

            <main id="maincontent" role="main" class="mt-4 md:mt-0">
                <x-game.rules-card
                    :rules="$rules"
                    :locationId="$locationId"
                    class="w-full"
                />
            </main>
        </div>

        <x-game.host-info-modal
            name="host-info-modal"
            title="Hoe werkt het?"
            :items="config('host.info')"
            maxWidth="2xl"
        />
    </div>

    <footer role="contentinfo" class="mt-6">
        <x-homeFooter />
    </footer>
@endsection
