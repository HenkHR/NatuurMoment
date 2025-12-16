<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 rounded-md transition-colors text-sm font-medium shadow-sm">
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
                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-surface-medium bg-pure-white text-deep-black focus:border-action focus:ring-action rounded-input shadow-sm" required>{{ old('description', $location->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="province" value="Provincie" />
                <select id="province" name="province" class="mt-1 block w-full border-surface-medium bg-pure-white text-deep-black focus:border-action focus:ring-action rounded-input shadow-sm" required>
                    <option value="">Selecteer een provincie</option>
                    @foreach(config('provinces') as $province)
                        <option value="{{ $province }}" {{ old('province', $location->province) == $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('province')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="distance" value="Afstand (km)" />
                <x-text-input id="distance" name="distance" type="number" min="0.1" step="0.1" class="mt-1 block w-full" :value="old('distance', $location->distance)" required />
                <x-input-error :messages="$errors->get('distance')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="image" value="Locatie afbeelding" />

                @if($location->image_path)
                    <div class="mt-2 mb-3">
                        <p class="text-small text-deep-black mb-2">Huidige afbeelding:</p>
                        <img src="{{ Storage::url($location->image_path) }}" alt="Huidige afbeelding" class="h-24 w-32 object-cover rounded-lg border border-surface-medium">
                    </div>
                @endif

                <input id="image" name="image" type="file" accept="image/*" class="mt-2 block w-full text-sm text-deep-black
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-button file:border file:border-sky-100
                    file:text-sm file:font-semibold
                    file:bg-sky-50 file:text-sky-700 file:shadow-none
                    hover:file:bg-sky-100" {{ $location->image_path ? '' : 'required' }} />
                <p class="mt-1.5 text-sm text-gray-500">
                    Formaat: JPEG, PNG, GIF of WebP. Max 2MB. Aanbevolen: minimaal 1200x400 pixels.
                </p>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            {{-- Game Mode Toggles --}}
            @include('admin.locations._game-mode-toggles', ['location' => $location])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.locations.index') }}">
                    <x-secondary-button type="button">Annuleren</x-secondary-button>
                </a>
                <x-primary-button>Bijwerken</x-primary-button>
            </div>
        </form>
    </div>
</x-admin.layout>
