@props(['location'])

<div class="bg-sky-50 border-b border-sky-100">
    <form method="POST" action="{{ route('admin.locations.bingo-scoring-config.update', $location) }}" class="px-6 py-3">
        @csrf
        @method('PATCH')

        <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
            <span class="text-xs font-medium text-sky-700 uppercase tracking-wider">Punten</span>

            {{-- 3 op een rij punten --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-sky-600">3 op een rij</span>
                <input
                    type="number"
                    id="bingo_three_in_row_points"
                    name="bingo_three_in_row_points"
                    min="1"
                    value="{{ old('bingo_three_in_row_points', $location->bingo_three_in_row_points) }}"
                    required
                    class="w-16 py-1 text-sm text-center bg-white border border-sky-200 rounded-md focus:border-sky-400 focus:ring-1 focus:ring-sky-400 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                />
                <x-input-error :messages="$errors->get('bingo_three_in_row_points')" />
            </div>

            {{-- Volle kaart punten --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-sky-600">Volle kaart</span>
                <input
                    type="number"
                    id="bingo_full_card_points"
                    name="bingo_full_card_points"
                    min="1"
                    value="{{ old('bingo_full_card_points', $location->bingo_full_card_points) }}"
                    required
                    class="w-16 py-1 text-sm text-center bg-white border border-sky-200 rounded-md focus:border-sky-400 focus:ring-1 focus:ring-sky-400 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                />
                <x-input-error :messages="$errors->get('bingo_full_card_points')" />
            </div>

            {{-- Save button --}}
            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-sky-700 bg-white border border-sky-200 rounded-md hover:bg-sky-100 transition-colors">
                Opslaan
            </button>
        </div>
    </form>
</div>
