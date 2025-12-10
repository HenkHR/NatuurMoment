<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.bingo-items.index', $location) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-md transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Terug naar bingo items
        </a>
    </div>

    <div class="bg-pure-white overflow-hidden rounded-card shadow-card p-6">
        <h2 class="text-h2 text-deep-black mb-2">Nieuw bingo item</h2>
        <p class="text-body text-deep-black mb-6">Locatie: {{ $location->name }}</p>

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

            <div class="mb-4">
                <x-input-label for="fact" value="Feitje/Weetje (optioneel)" />
                <textarea id="fact" name="fact" rows="3" class="mt-1 block w-full border-surface-medium bg-pure-white text-deep-black focus:border-action focus:ring-action rounded-input shadow-sm">{{ old('fact') }}</textarea>
                <x-input-error :messages="$errors->get('fact')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="icon" value="Icon afbeelding (optioneel)" />
                <input id="icon" name="icon" type="file" accept="image/*" class="mt-2 block w-full text-sm text-deep-black
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-button file:border-0
                    file:text-sm file:font-semibold
                    file:bg-sky-50 file:text-sky-700
                    hover:file:bg-sky-100" />
                <p class="mt-1.5 text-sm text-gray-500">
                    Formaat: JPEG, PNG, GIF, SVG of WebP. Max 2MB. Aanbevolen: vierkante afbeelding (bijv. 200x200 pixels).
                </p>
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
