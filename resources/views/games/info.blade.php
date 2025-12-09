@extends('layouts.game')

@section('title', $game['title'] ?? 'Game info')

@section('content')
    <x-homeNav />

    <div class="flex-1 bg-surface-light pt-16 md:pt-20">
        <div class="max-w-5xl mx-auto w-full px-4 lg:px-8 pb-20">

            <x-ui.breadcrumbs
                :items="$breadcrumbs"
                class="mt-4 mb-4 md:mt-0 md:mb-6"
            />

            <section class="relative bg-forest-700 rounded-b-card overflow-hidden mt-6 md:mt-8">
                <div class="h-56 md:h-72 overflow-hidden">
                    <img
                        src="{{ asset('images/de_tempel.jpg') }}"
                        alt="Natuurgebied Buitenplaats de Tempel"
                        class="w-full h-full object-cover"
                    >
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-deep-black/70 via-deep-black/10 to-transparent"></div>

                <div class="absolute inset-0 flex items-end">
                    <div
                        class="w-full px-4 pb-4 md:px-8 md:pb-6
                               flex flex-col md:flex-row md:items-end md:justify-between gap-4"
                    >
                        <div class="text-pure-white max-w-md">
                            <h1 class="text-2xl md:text-3xl font-semibold">
                                {{ $game['title'] }}
                            </h1>
                        </div>

                        <a
                            href="https://www.natuurmonumenten.nl/natuurgebieden/buitenplaats-de-tempel"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="self-start md:self-auto md:ml-auto bg-sky-500 hover:bg-sky-600
                                   text-pure-white text-sm md:text-base font-semibold
                                   px-4 py-2 rounded-badge shadow-card whitespace-nowrap transition"
                        >
                            {{ $game['location'] }}
                        </a>
                    </div>
                </div>
            </section>

            <main class="mt-6 md:mt-8">
                <x-game.rules-card
                    :rules="$rules"
                    :locationId="$locationId"
                    class="w-full max-w-5xl mx-auto"
                />
            </main>
        </div>
    </div>

    <x-homeFooter />
@endsection
