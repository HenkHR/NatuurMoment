@if($locations->count())
    <div class="grid gap-3 sm:gap-4 sm:grid-cols-2">
        @foreach($locations as $location)
            <article class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col" aria-labelledby="loc-{{ $location->id }}-title">
                <div class="bg-green-700 text-white px-4 py-2">
                    <h4 id="loc-{{ $location->id }}-title" class="font-semibold text-sm sm:text-base">{{ $location->name }}</h4>
                </div>

                <div class="flex-1 px-4 pt-3 pb-3 text-xs sm:text-sm">
                    @if($location->description)
                        <p class="text-gray-700 leading-relaxed">{{ $location->description }}</p>
                    @else
                        <p class="text-gray-500">Hier kun je verschillende spellen spelen tijdens je wandeling.</p>
                    @endif
                </div>

                <div class="px-4 pb-4">
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
