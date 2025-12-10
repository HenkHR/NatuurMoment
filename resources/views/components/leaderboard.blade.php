@props(['players' => [], 'showContinueButton' => false])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-lg p-4 sm:p-6']) }}>
    <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-800 mb-4 sm:mb-6">Eindstand</h2>

    @if(count($players) >= 1)
        <div class="flex justify-center items-end gap-3 sm:gap-4 mb-4 sm:mb-6 h-40 sm:h-48">

            <!-- 2nd Place (left) -->
            @if(count($players) >= 2)
                <div class="flex flex-col items-center">
                    <!-- medaille -->
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gray-300 flex items-center justify-center mb-1 shadow">
                        <span class="text-lg sm:text-xl">🥈</span>
                    </div>

                    <!-- blok (middelste hoogte) -->
                    <div class="bg-gray-200 w-16 h-14 sm:w-20 sm:h-18 rounded-t-lg flex flex-col items-center justify-center">
                    <span class="font-bold text-gray-800 text-[10px] sm:text-xs truncate w-14 sm:w-16 text-center">
                        {{ $players[1]['name'] }}
                    </span>
                        <span class="text-gray-600 font-semibold text-[11px] sm:text-sm">
                        {{ $players[1]['score'] }} pt
                    </span>
                    </div>
                </div>
            @else
                <div class="w-16"></div>
            @endif


            <!-- 1st Place (center, tallest) -->
            <div class="flex flex-col items-center">
                <!-- medaille -->
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-yellow-400 flex items-center justify-center mb-1 shadow ring-2 ring-yellow-300">
                    <span class="text-xl sm:text-2xl">🥇</span>
                </div>

                <!-- blok (hoogste) -->
                <div class="bg-yellow-100 w-20 h-20 sm:w-24 sm:h-24 rounded-t-lg flex flex-col items-center justify-center border-2 border-yellow-400">
                <span class="font-bold text-gray-800 text-xs sm:text-sm truncate w-18 sm:w-20 text-center">
                    {{ $players[0]['name'] }}
                </span>
                    <span class="text-yellow-700 font-bold text-sm sm:text-base">
                    {{ $players[0]['score'] }} pt
                </span>
                </div>
            </div>


            <!-- 3rd Place (right) -->
            @if(count($players) >= 3)
                <div class="flex flex-col items-center">
                    <!-- medaille -->
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-amber-600 flex items-center justify-center mb-1 shadow">
                        <span class="text-base sm:text-lg">🥉</span>
                    </div>

                    <!-- blok (laagste) -->
                    <div class="bg-amber-100 w-14 h-12 sm:w-18 sm:h-14 rounded-t-lg flex flex-col items-center justify-center">
                    <span class="font-bold text-gray-800 text-[9.5px] sm:text-xs truncate w-12 sm:w-14 text-center">
                        {{ $players[2]['name'] }}
                    </span>
                        <span class="text-amber-700 font-semibold text-[11px] sm:text-sm">
                        {{ $players[2]['score'] }} pt
                    </span>
                    </div>
                </div>
            @else
                <div class="w-14"></div>
            @endif

        </div>
    @endif


    <!-- Full Ranking List -->
    {{-- VOLLEDIGE RANGLIJST --}}
    @if(count($players) > 0)
        <div class="border-t border-gray-200 pt-3 sm:pt-4">
            <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3">
                Volledige ranglijst
            </h3>

            <div class="space-y-2">
                @foreach($players as $index => $player)
                    <div
                        class="flex items-center justify-between gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg
                            {{ $index < 3 ? 'bg-gray-50' : 'bg-white border border-gray-100' }}
                            text-xs sm:text-sm md:text-base"
                    >
                        {{-- links: rank + naam --}}
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <span
                                class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center
                                    text-[11px] sm:text-sm font-bold
                                    {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : '' }}
                                    {{ $index === 1 ? 'bg-gray-300 text-gray-700' : '' }}
                                    {{ $index === 2 ? 'bg-amber-600 text-white' : '' }}
                                    {{ $index >= 3 ? 'bg-gray-200 text-gray-600' : '' }}"
                            >
                                {{ $player['rank'] }}
                            </span>
                            <span class="font-medium text-gray-800 truncate max-w-[10rem] sm:max-w-[14rem] md:max-w-[18rem]">
                                {{ $player['name'] }}
                            </span>
                        </div>

                        {{-- rechts: punten, altijd op dezelfde hoogte als naam --}}
                        <span class="font-bold text-gray-700 text-xs sm:text-sm md:text-base whitespace-nowrap">
                            {{ $player['score'] }} punten
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-center text-gray-500 py-8 text-sm sm:text-base">
            Geen spelers gevonden.
        </p>
    @endif

    @if($showContinueButton)
        <div class="mt-4 sm:mt-6 text-center">
            {{ $slot }}
        </div>
    @endif
</div>
