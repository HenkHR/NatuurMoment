@if($locations->count())
    <div class="grid gap-3 sm:gap-4 sm:grid-cols-2">
        @foreach($locations as $location)
            <article class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md flex flex-col" aria-labelledby="loc-{{ $location->id }}-title">
                <div class="bg-green-700 text-white px-4 py-3">
                    <h4 id="loc-{{ $location->id }}-title" class="font-semibold text-base sm:text-lg">{{ $location->name }}</h4>
                </div>

                @if($location->image_path)
                    <div class="h-32 sm:h-40 overflow-hidden">
                        <img
                            src="{{ Storage::url($location->image_path) }}"
                            alt="{{ $location->name }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                @endif

                <div class="flex-1 px-4 pt-3 pb-3 text-xs sm:text-sm flex flex-col">
                    <p class="text-gray-700 leading-relaxed line-clamp-2 min-h-[2.5rem] sm:min-h-[2.75rem]">{{ $location->description }}</p>

                    <div class="flex items-center gap-3 mt-auto pt-2 text-gray-500">
                        @if($location->distance)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                {{ $location->distance }} km
                            </span>
                        @endif
                        @if($location->province)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $location->province }}
                            </span>
                        @endif
                    </div>
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
