<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.bingo-items.index', $location) }}" class="text-sky-600 hover:text-sky-700">
            &larr; Terug naar bingo items
        </a>
    </div>

    <div class="bg-pure-white overflow-hidden rounded-card shadow-card p-6">
        <h2 class="text-h2 text-forest-800 mb-2">Nieuw bingo item</h2>
        <p class="text-body text-forest-600 mb-6">Locatie: {{ $location->name }}</p>

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
                <input id="icon" name="icon" type="file" accept="image/*" class="mt-1 block w-full text-sm text-forest-600
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-button file:border-0
                    file:text-sm file:font-semibold
                    file:bg-forest-50 file:text-forest-700
                    hover:file:bg-forest-100" />
                <p class="mt-1 text-small text-forest-600">Max 2MB. Toegestane formaten: jpeg, png, jpg, gif, svg, webp</p>
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
