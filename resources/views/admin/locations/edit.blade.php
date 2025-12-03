<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-md transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Terug naar locaties
        </a>
    </div>

    <div class="bg-pure-white overflow-hidden rounded-card shadow-card p-6">
        <h2 class="text-h2 text-deep-black mb-6">Locatie bewerken</h2>

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
                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-surface-medium bg-pure-white text-deep-black focus:border-action focus:ring-action rounded-input shadow-sm">{{ old('description', $location->description) }}</textarea>
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
