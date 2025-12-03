<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.bingo-items.index', $location) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
            &larr; Terug naar bingo items
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Nieuw bingo item</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Locatie: {{ $location->name }}</p>

        <form method="POST" action="{{ route('admin.locations.bingo-items.store', $location) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <x-input-label for="label" value="Label" />
                <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label')" required autofocus />
                <x-input-error :messages="$errors->get('label')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="points" value="Punten" />
                <x-text-input id="points" name="points" type="number" min="1" class="mt-1 block w-full" :value="old('points', 1)" required />
                <x-input-error :messages="$errors->get('points')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="icon" value="Icon afbeelding (optioneel)" />
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
                <a href="{{ route('admin.locations.bingo-items.index', $location) }}">
                    <x-secondary-button type="button">Annuleren</x-secondary-button>
                </a>
                <x-primary-button>Opslaan</x-primary-button>
            </div>
        </form>
    </div>
</x-admin.layout>
