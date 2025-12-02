@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 flex flex-col">

{{--        @php--}}
{{--            // Hero image: gebruik afbeelding van locatie of een algemene fallback--}}
{{--            $heroImage = $selectedLocation?->hero_url ?? asset('images/spellen-hero.jpg');--}}
{{--            $heroTitle = $selectedLocation?->name ?? 'Spellen';--}}
{{--            $heroSubtitle = $selectedLocation--}}
{{--                ? 'De leukste spellen voor tijdens je wandeltocht in ' . $selectedLocation->name--}}
{{--                : 'De leukste spellen voor tijdens je wandeltocht in één van onze natuurgebieden';--}}
{{--        @endphp--}}

        {{-- Hero --}}
        <section class="relative w-full">
            <div class="h-56 w-full overflow-hidden">
{{--                <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ $heroImage }}');"></div>--}}
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-6 flex justify-center">
                <div class="text-center text-white px-6">
                    <h1 class="text-3xl font-semibold tracking-tight">
{{--                        {{ $heroTitle }}--}}
                    </h1>
                    <p class="mt-2 text-sm">
{{--                        {{ $heroSubtitle }}--}}
                    </p>
                </div>
            </div>
        </section>

        {{-- Content --}}
        <main class="flex-1">
            <div class="max-w-4xl mx-auto w-full px-4 pb-10 -mt-4">
                <div class="bg-white rounded-2xl shadow-lg px-5 py-6">
                    {{-- Zoek + filter rij --}}
                    <div class="flex flex-col gap-4 mb-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold">
{{--                                @if($selectedLocation)--}}
{{--                                    Zoek een spel--}}
{{--                                @else--}}
{{--                                    Zoek een locatie--}}
{{--                                @endif--}}
                            </h2>
                        </div>

                        <form method="GET"
                              action="{{ route('home.index') }}"
                              class="flex flex-col gap-3 sm:flex-row sm:items-center">

                            {{-- Zoekveld --}}
                            <div class="relative flex-1">
                                <input
                                    type="text"
                                    name="search"
{{--                                    value="{{ $search }}"--}}
                                    placeholder="Zoeken"
                                    class="w-full rounded-full border border-gray-300 py-2.5 pl-10 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                                >
                                <span class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.6 3.6a7.5 7.5 0 0013.05 13.05z" />
                                </svg>
                            </span>


{{--                                @if($selectedLocation)--}}
{{--                                    <div--}}
{{--                                        class="absolute -bottom-8 left-0 bg-white border border-gray-300 rounded-full px-3 py-1 text-xs flex items-center gap-2 shadow-sm">--}}
{{--                                    <span class="font-medium">--}}
{{--                                        {{ $selectedLocation->name }}--}}
{{--                                    </span>--}}
{{--                                        <button type="submit" name="location" value="" class="text-gray-400 hover:text-gray-600">--}}
{{--                                            &times;--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
                            </div>


                            <div class="flex items-center gap-2 sm:w-48">
                                <select
                                    name="location"
                                    class="w-full rounded-full border border-gray-300 py-2.5 px-4 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                                >
                                    <option value="">Alle locaties</option>
{{--                                    @foreach($locations as $locationOption)--}}
{{--                                        <option--}}
{{--                                            value="{{ $locationOption->id }}"--}}
{{--                                            @selected(optional($selectedLocation)->id === $locationOption->id)--}}
{{--                                        >--}}
{{--                                            {{ $locationOption->name }}--}}
{{--                                        </option>--}}
{{--                                    @endforeach--}}
                                </select>


                                <button type="submit"
                                        class="hidden sm:inline-flex items-center justify-center rounded-full border border-gray-300 w-10 h-10 text-gray-500 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 4h18M6 8h12M9 12h6M11 16h2" />
                                    </svg>
                                </button>
                            </div>

                            <button type="submit"
                                    class="sm:hidden inline-flex justify-center rounded-full bg-green-700 text-white text-sm font-medium py-2.5 px-5">
                                Toepassen
                            </button>
                        </form>
                    </div>

{{--                    @if($selectedLocation)--}}
{{--                        <div class="h-4"></div>--}}
{{--                    @endif--}}

                    {{-- Titel sectie --}}
                    <div class="mb-4 mt-2">
                        <h3 class="text-base font-semibold">
                            Uitgelichte spellen
                        </h3>
                    </div>

                    {{-- Cards --}}
{{--                    @if($games->count())--}}
{{--                        <div class="grid gap-4 sm:grid-cols-2">--}}
{{--                            @foreach($games as $game)--}}
{{--                                <article class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col">--}}
{{--                                    --}}{{-- Card header (groene balk) --}}
{{--                                    <div class="bg-green-700 text-white px-4 py-2">--}}
{{--                                        <h4 class="font-semibold text-sm">--}}
{{--                                            {{ $game->name }}--}}
{{--                                        </h4>--}}
{{--                                        <p class="text-xs opacity-90">--}}
{{--                                            {{ $game->location->name }}--}}
{{--                                        </p>--}}
{{--                                    </div>--}}

{{--                                    @if($game->image_url)--}}
{{--                                        <div class="h-36 w-full overflow-hidden">--}}
{{--                                            <img src="{{ $game->image_url }}"--}}
{{--                                                 alt="{{ $game->name }}"--}}
{{--                                                 class="w-full h-full object-cover">--}}
{{--                                        </div>--}}
{{--                                    @endif--}}

{{--                                    <div class="flex-1 px-4 pt-3 pb-4 text-xs">--}}
{{--                                        <ul class="list-disc pl-4 space-y-1">--}}
{{--                                            @if($game->players_min || $game->players_max)--}}
{{--                                                <li>--}}
{{--                                                    {{ $game->players_min ?? '?' }}–{{ $game->players_max ?? '?' }} spelers--}}
{{--                                                </li>--}}
{{--                                            @endif--}}
{{--                                            @if(!empty($game->needs_materials))--}}
{{--                                                <li>Benodigdheden: {{ $game->needs_materials }}</li>--}}
{{--                                            @else--}}
{{--                                                <li>Geen benodigdheden</li>--}}
{{--                                            @endif--}}
{{--                                            @if($game->organisers_count)--}}
{{--                                                <li>{{ $game->organisers_count }} organisator{{ $game->organisers_count > 1 ? 'en' : '' }}</li>--}}
{{--                                            @endif--}}
{{--                                        </ul>--}}
{{--                                    </div>--}}

{{--                                    <div class="px-4 pb-4">--}}
{{--                                        <a href="{{ route('games.show', $game) }}"--}}
{{--                                           class="inline-flex w-full justify-center rounded-md bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-2">--}}
{{--                                            Spelen--}}
{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                </article>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}

{{--                        <div class="mt-6">--}}
{{--                            {{ $games->links() }}--}}
{{--                        </div>--}}
{{--                    @else--}}
{{--                        <p class="text-sm text-gray-500">--}}
{{--                            Geen spellen gevonden voor deze combinatie van zoekterm en locatie.--}}
{{--                        </p>--}}
{{--                    @endif--}}
                </div>
            </div>
        </main>

    </div>
@endsection
