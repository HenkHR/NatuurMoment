@php
    use App\Constants\GameMode;
    $currentModes = old('game_modes', $location->game_modes ?? []);
    $bingoEnabled = in_array(GameMode::BINGO, $currentModes);
    $vragenEnabled = in_array(GameMode::VRAGEN, $currentModes);
@endphp

<div class="mb-6 pt-4 border-t border-surface-medium">
    <h3 class="text-lg font-semibold text-deep-black mb-2">Spelmodi</h3>
    <p class="text-sm text-gray-600 mb-4">
        @if(!isset($location) || !$location->exists)
            Nieuwe locaties hebben standaard alle spelmodi uitgeschakeld.
        @else
            Selecteer welke spelmodi beschikbaar zijn voor deze locatie.
        @endif
    </p>

    <div class="space-y-4">
        {{-- Bingo Mode Toggle --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox"
                           name="game_modes[]"
                           value="bingo"
                           {{ $bingoEnabled ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-600"></div>
                </label>
                <div>
                    <span class="text-sm font-medium text-deep-black">Bingo modus</span>
                    <p class="text-xs text-gray-500">
                        @if(isset($location) && $location->exists)
                            {{ $location->bingo_items_count ?? 0 }}/{{ GameMode::MIN_BINGO_ITEMS }} items
                        @else
                            Minimaal {{ GameMode::MIN_BINGO_ITEMS }} bingo items vereist
                        @endif
                    </p>
                </div>
            </div>

            @if(isset($location) && $location->exists && $bingoEnabled)
                @if($location->is_bingo_mode_valid)
                    <span class="text-green-600 text-xl" title="Valide: voldoende bingo items">✓</span>
                @else
                    <span class="text-orange-500 text-xl" title="Onvoldoende bingo items">⚠️</span>
                @endif
            @endif
        </div>

        {{-- Vragen Mode Toggle --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox"
                           name="game_modes[]"
                           value="vragen"
                           {{ $vragenEnabled ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-600"></div>
                </label>
                <div>
                    <span class="text-sm font-medium text-deep-black">Vragen modus</span>
                    <p class="text-xs text-gray-500">
                        @if(isset($location) && $location->exists)
                            {{ $location->route_stops_count ?? 0 }}/{{ GameMode::MIN_QUESTIONS }} vragen
                        @else
                            Minimaal {{ GameMode::MIN_QUESTIONS }} vraag vereist
                        @endif
                    </p>
                </div>
            </div>

            @if(isset($location) && $location->exists && $vragenEnabled)
                @if($location->is_vragen_mode_valid)
                    <span class="text-green-600 text-xl" title="Valide: voldoende vragen">✓</span>
                @else
                    <span class="text-orange-500 text-xl" title="Onvoldoende vragen">⚠️</span>
                @endif
            @endif
        </div>
    </div>

    <x-input-error :messages="$errors->get('game_modes')" class="mt-2" />
</div>
