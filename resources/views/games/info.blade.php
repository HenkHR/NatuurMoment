@extends('layouts.app')

@section('title', $game['title'] ?? 'Game info')

@section('content')
    {{-- navbar voor home-achtige schermen --}}
    <x-nav.home />

    {{-- hero met foto + titel + bulletpoints --}}
    <div class="relative">
        <img
            src="{{ asset('images/natuur-avontuur-hero.jpg') }}"
            alt="Natuurgebied"
            class="w-full h-40 object-cover"
        >

        <div class="absolute inset-0 bg-gradient-to-t from-deep-black/70 to-transparent"></div>

        <div class="absolute bottom-3 left-4 right-4 text-pure-white">
            <h1 class="text-h1">{{ $game['title'] }}</h1>

            <div class="mt-2 text-small space-y-1">
                <p>• {{ $game['players_min'] }}–{{ $game['players_max'] }} spelers</p>
                <p>• {{ $game['needs_materials'] ? 'Benodigdheden nodig' : 'Geen benodigdheden' }}</p>
                <p>• {{ $game['organisers'] }} organisator</p>
            </div>

            <span class="inline-block mt-2 bg-sky-500 text-pure-white text-small px-3 py-1 rounded-badge">
                {{ $game['location'] }}
            </span>
        </div>
    </div>

    <main class="px-4 pt-4 pb-6">
        {{-- hier komt zo je spelregels-component --}}
        <x-game.rules-card :rules="$rules" class="mt-4" />

        <footer class="mt-6 text-center text-small text-deep-black/60">
            <p>Bezoek de website</p>
            <p class="mt-1">Voorwaarden | Privacy | Cookieverklaring</p>
        </footer>
    </main>
@endsection
