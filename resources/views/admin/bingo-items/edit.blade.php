<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.bingo-items.index', $bingoItem->location) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
            &larr; Terug naar bingo items
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Bingo item bewerken</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Locatie: {{ $bingoItem->location->name }}</p>

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
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Huidige icon:</p>
                        <div class="flex items-center gap-4">
                            <img src="{{ Storage::url($bingoItem->icon) }}" alt="Huidige icon" class="h-16 w-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            <label class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400 cursor-pointer">
                                <input type="checkbox" name="remove_icon" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Verwijder huidige icon
                            </label>
                        </div>
                    </div>
                @endif

                <input id="icon" name="icon" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100
                    dark:file:bg-indigo-900 dark:file:text-indigo-300" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Max 2MB. Toegestane formaten: jpeg, png, jpg, gif, svg, webp</p>
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
