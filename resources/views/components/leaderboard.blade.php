@props(['players' => [], 'showContinueButton' => false, 'isFinished' => false])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-lg p-2 flex flex-col h-full']) }}>
    @if(count($players) >= 1)
        @if($isFinished)
        <!-- Podium for Top 3 -->
            <div class="flex justify-center items-end gap-4 mb-8 flex-shrink-0">
            <!-- 2nd Place (left) -->
            @if(count($players) >= 2)
                <div class="flex flex-col items-center">
                        <span class="font-bold text-gray-800 text-sm truncate w-20 text-center">{{ $players[1]['name'] }}</span>
                        <div class="bg-gray-200 w-20 h-24 rounded-t-lg flex flex-col items-center justify-center">
                        <span class="text-gray-600 font-semibold">{{ $players[1]['score'] }} pt</span>
                            <span class="text-xl">#2</span>
                    </div>
                </div>
            @else
                <div class="w-24"></div>
            @endif

            <!-- 1st Place (center, tallest) -->
            <div class="flex flex-col items-center">
                    <span class="font-bold text-gray-800 truncate w-24 text-center">{{ $players[0]['name'] }}</span>
                    <div class="bg-yellow-100 w-24 h-32 rounded-t-lg flex flex-col items-center justify-center border-2 border-yellow-400">
                    <span class="text-yellow-700 font-bold text-lg">{{ $players[0]['score'] }} pt</span>
                        <span class="text-2xl">#1</span>
                </div>
            </div>

            <!-- 3rd Place (right) -->
            @if(count($players) >= 3)
                <div class="flex flex-col items-center">
                        <span class="font-bold text-gray-800 text-xs truncate w-16 text-center">{{ $players[2]['name'] }}</span>
                        <div class="bg-amber-100 w-16 h-16 rounded-t-lg flex flex-col items-center justify-center">
                        <span class="text-amber-700 font-semibold text-sm">{{ $players[2]['score'] }} pt</span>
                            <span class="text-lg">#3</span>
                    </div>
                </div>
            @else
                <div class="w-20"></div>
            @endif
        </div>
    @endif
    @endif


    <!-- Full Ranking List -->
    {{-- VOLLEDIGE RANGLIJST --}}
    @if(count($players) > 0)
        <div class="flex flex-col flex-1 min-h-0">
            @if($isFinished)
                <h3 class="text-lg font-semibold text-gray-700 mb-3 flex-shrink-0 border-b border-gray-300 pb-2">Volledige ranglijst</h3>
            @endif
            <div class="space-y-3 overflow-y-auto flex-1 min-h-0 pr-1 pb-9">
                @foreach($players as $index => $player)
                    <div class="border border-gray-300 rounded-lg overflow-hidden {{ $index < 3 ? 'bg-gray-50' : 'bg-white' }}">
                        <div class="flex items-center justify-between py-3 px-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : '' }}
                                {{ $index === 1 ? 'bg-gray-300 text-gray-700' : '' }}
                                {{ $index === 2 ? 'bg-amber-600 text-white' : '' }}
                                {{ $index >= 3 ? 'bg-gray-200 text-gray-600' : '' }}">
                                {{ $player['rank'] }}
                            </span>
                                <span class="font-semibold text-md text-gray-800">{{ $player['name'] }}</span>
                            </div>
                            <span class="font-bold text-gray-700">{{ $player['score'] }} punten</span>
                        </div>
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
