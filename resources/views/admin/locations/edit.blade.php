<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
            &larr; Terug naar locaties
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Locatie bewerken</h3>

        <form method="POST" action="{{ route('admin.locations.update', $location) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <x-input-label for="name" value="Naam" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $location->name)" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="description" value="Beschrijving" />
                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $location->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.locations.index') }}">
                    <x-secondary-button type="button">Annuleren</x-secondary-button>
                </a>
                <x-primary-button>Bijwerken</x-primary-button>
            </div>
        </form>
    </div>
</x-admin.layout>
