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

        <form method="POST" action="{{ route('admin.locations.update', $location) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <x-input-label for="name" value="Naam" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $location->name)" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="description" value="Beschrijving" />
                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-surface-medium bg-pure-white text-deep-black focus:border-action focus:ring-action rounded-input shadow-sm">{{ old('description', $location->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="province" value="Provincie/Regio" />
                <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $location->province)" required />
                <x-input-error :messages="$errors->get('province')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="duration" value="Duur (minuten)" />
                <x-text-input id="duration" name="duration" type="number" min="1" class="mt-1 block w-full" :value="old('duration', $location->duration)" required />
                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="image" value="Locatie afbeelding (optioneel)" />

                @if($location->image_path)
                    <div class="mt-2 mb-3">
                        <p class="text-small text-deep-black mb-2">Huidige afbeelding:</p>
                        <div class="flex items-center gap-4">
                            <img src="{{ Storage::url($location->image_path) }}" alt="Huidige afbeelding" class="h-24 w-32 object-cover rounded-lg border border-surface-medium">
                            <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-surface-medium text-red-600 focus:ring-red-500">
                                Verwijder huidige afbeelding
                            </label>
                        </div>
                    </div>
                @endif

                <input id="image" name="image" type="file" accept="image/*" class="mt-1 block w-full text-sm text-deep-black
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-button file:border-0
                    file:text-sm file:font-semibold
                    file:bg-sky-50 file:text-sky-700
                    hover:file:bg-sky-100" />
                <p class="mt-1 text-small text-deep-black">Max 2MB. Toegestane formaten: jpeg, png, jpg, gif, webp</p>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
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
