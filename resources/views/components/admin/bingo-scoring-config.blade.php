@props(['location'])

<div class="mt-8 bg-pure-white rounded-card shadow-card p-6">
    <h3 class="text-lg font-semibold text-deep-black mb-4">Bingo Punten Configuratie</h3>

    <form method="POST" action="{{ route('admin.locations.bingo-scoring-config.update', $location) }}">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-xl">
            {{-- 3 op een rij punten --}}
            <div>
                <x-input-label for="bingo_three_in_row_points" value="3 op een rij" />
                <div class="mt-1 flex items-center gap-2">
                    <x-text-input
                        id="bingo_three_in_row_points"
                        name="bingo_three_in_row_points"
                        type="number"
                        min="1"
                        class="w-24"
                        :value="old('bingo_three_in_row_points', $location->bingo_three_in_row_points ?? 50)"
                        required
                    />
                    <span class="text-sm text-surface-dark">punten</span>
                </div>
                <x-input-error :messages="$errors->get('bingo_three_in_row_points')" class="mt-2" />
            </div>

            {{-- Volle kaart punten --}}
            <div>
                <x-input-label for="bingo_full_card_points" value="Volle kaart" />
                <div class="mt-1 flex items-center gap-2">
                    <x-text-input
                        id="bingo_full_card_points"
                        name="bingo_full_card_points"
                        type="number"
                        min="1"
                        class="w-24"
                        :value="old('bingo_full_card_points', $location->bingo_full_card_points ?? 100)"
                        required
                    />
                    <span class="text-sm text-surface-dark">punten</span>
                </div>
                <x-input-error :messages="$errors->get('bingo_full_card_points')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <x-primary-button>Opslaan</x-primary-button>
        </div>
    </form>
</div>
