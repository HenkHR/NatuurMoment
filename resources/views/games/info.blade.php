@extends('layouts.game')

@section('title', $game['title'] ?? 'Game info')

@section('content')
    {{-- NAVBAR --}}
    <x-nav.home />

    <div class="flex-1 bg-surface-light">
        <div class="max-w-5xl mx-auto w-full px-4 lg:px-8 pb-12">

        <section class="relative bg-forest-700 rounded-b-card overflow-hidden mt-4">
            <div class="h-56 md:h-72 overflow-hidden">
                <img
                    src="{{ asset('images/de_tempel.jpg') }}"
                    alt="Natuurgebied Buitenplaats de Tempel"
                    class="w-full h-full object-cover"
                >
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-deep-black/70 via-deep-black/10 to-transparent"></div>

            <div class="absolute inset-0 flex items-end">
                <div class="w-full px-4 pb-4 md:px-8 md:pb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div class="text-pure-white max-w-md">
                        <h1 class="text-2xl md:text-3xl font-semibold mb-3">
                            {{ $game['title'] }}
                        </h1>

                        <ul class="text-small md:text-base space-y-1 list-disc list-inside">
                            <li>{{ $game['players_min'] }}–{{ $game['players_max'] }} spelers</li>
                            <li>{{ $game['needs_materials'] ? 'Benodigdheden nodig' : 'Geen benodigdheden' }}</li>
                            <li>{{ $game['organisers'] }} organisator</li>
                        </ul>
                    </div>
                    <a
                        href="https://www.natuurmonumenten.nl/natuurgebieden/buitenplaats-de-tempel"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="self-start md:self-auto md:ml-auto bg-sky-500 hover:bg-sky-600 text-pure-white text-sm md:text-base font-semibold px-4 py-2 rounded-badge shadow-card whitespace-nowrap transition"
                    >
                        {{ $game['location'] }}
                    </a>
                </div>
            </div>
        </section>


            <main class="mt-6">
                <x-game.rules-card :rules="$rules" class="w-full max-w-5xl mx-auto" />
            </main>

        </div>
    </div>

    <footer class="bg-surface-medium mt-auto">
        <div class="max-w-5xl mx-auto w-full px-4 lg:px-8 py-4 text-center text-small text-deep-black/60">
            <p>
                <a
                    href="https://www.natuurmonumenten.nl/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:underline"
                >
                    Bezoek de website
                </a>
            </p>

            <p class="mt-1">Voorwaarden | Privacy | Cookieverklaring</p>
        </div>
    </footer>
@endsection
