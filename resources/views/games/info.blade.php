@extends('layouts.game')

@section('title', $game['title'] ?? 'Game info')

@section('content')
    <div class="flex flex-col min-h-screen">
        
        {{-- NAVBAR --}}
        <x-nav.home />

        <div class="flex-1">
            <section class="relative bg-forest-700">
                {{-- foto --}}
                <div class="h-56 overflow-hidden">
                    <img
                        src="{{ asset('images/de_tempel.jpg') }}"
                        alt="Natuurgebied Buitenplaats de Tempel"
                        class="w-full h-full object-cover"
                    >
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-deep-black/70 via-deep-black/10 to-transparent"></div>

                <div class="absolute bottom-4 left-4 right-4 pr-32">
                    <h1 class="text-pure-white text-2xl font-semibold mb-3">
                        {{ $game['title'] }}
                    </h1>

                    <ul class="text-pure-white text-small space-y-1 list-disc list-inside">
                        <li>{{ $game['players_min'] }}–{{ $game['players_max'] }} spelers</li>
                        <li>{{ $game['needs_materials'] ? 'Benodigdheden nodig' : 'Geen benodigdheden' }}</li>
                        <li>{{ $game['organisers'] }} organisator</li>
                    </ul>
                </div>

                <span
                    class="absolute bottom-3 right-3 bg-sky-500 text-pure-white text-sm font-semibold px-4 py-2 rounded-badge shadow-card whitespace-nowrap"
                >
                    {{ $game['location'] }}
                </span>
            </section>

            <main class="px-4 pt-4 pb-6">
                <x-game.rules-card :rules="$rules" class="mt-4" />
            </main>
        </div>

        <footer class="mt-auto bg-surface-medium rounded-t-[10px] text-center py-4 text-small text-deep-black/60">
            <p>Bezoek de website</p>
            <p class="mt-1">Voorwaarden | Privacy | Cookieverklaring</p>
        </footer>

    </div>
@endsection
