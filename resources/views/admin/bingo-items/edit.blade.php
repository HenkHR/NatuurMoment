<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.bingo-items.index', $bingoItem->location) }}" class="text-sky-600 hover:text-sky-700">
            &larr; Terug naar bingo items
        </a>
    </div>

    <div class="bg-pure-white overflow-hidden rounded-card shadow-card p-6">
        <h2 class="text-h2 text-deep-black mb-2">Bingo item bewerken</h2>
        <p class="text-body text-deep-black mb-6">Locatie: {{ $bingoItem->location->name }}</p>

        <form method="POST" action="{{ route('admin.bingo-items.update', $bingoItem) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <x-input-label for="label" value="Label" />
                <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label', $bingoItem->label)" required autofocus />
                <x-input-error :messages="$errors->get('label')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="points" value="Punten" />
                <x-text-input id="points" name="points" type="number" min="1" class="mt-1 block w-full" :value="old('points', $bingoItem->points)" required />
                <x-input-error :messages="$errors->get('points')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="icon" value="Icon afbeelding (optioneel)" />

                @if($bingoItem->icon)
                    <div class="mt-2 mb-3">
                        <p class="text-small text-deep-black mb-2">Huidige icon:</p>
                        <div class="flex items-center gap-4">
                            <img src="{{ Storage::url($bingoItem->icon) }}" alt="Huidige icon" class="h-16 w-16 object-cover rounded-icon border border-surface-medium">
                            <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_icon" value="1" class="rounded border-surface-medium text-red-600 focus:ring-red-500">
                                Verwijder huidige icon
                            </label>
                        </div>
                    </div>
                @endif

                <input id="icon" name="icon" type="file" accept="image/*" class="mt-1 block w-full text-sm text-deep-black
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-button file:border-0
                    file:text-sm file:font-semibold
                    file:bg-sky-50 file:text-sky-700
                    hover:file:bg-sky-100" />
                <p class="mt-1 text-small text-deep-black">Max 2MB. Toegestane formaten: jpeg, png, jpg, gif, svg, webp</p>
                <x-input-error :messages="$errors->get('icon')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.locations.bingo-items.index', $bingoItem->location) }}">
                    <x-secondary-button type="button">Annuleren</x-secondary-button>
                </a>
                <x-primary-button>Bijwerken</x-primary-button>
            </div>
        </form>
    </div>
</x-admin.layout>
