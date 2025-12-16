<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.route-stops.index', $routeStop->location) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 rounded-md transition-colors text-sm font-medium shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Terug naar vragen
        </a>
    </div>

    <div class="bg-pure-white overflow-hidden rounded-card shadow-card p-6">
        <h2 class="text-h2 text-deep-black mb-2">Vraag bewerken</h2>
        <p class="text-body text-deep-black mb-6">Locatie: {{ $routeStop->location->name }}</p>

        <form method="POST" action="{{ route('admin.route-stops.update', $routeStop) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="name" value="Naam" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $routeStop->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="sequence" value="Volgorde" />
                    <x-text-input id="sequence" name="sequence" type="number" min="0" class="mt-1 block w-full" :value="old('sequence', $routeStop->sequence)" required />
                    <x-input-error :messages="$errors->get('sequence')" class="mt-2" />
                </div>
            </div>

            <div class="mb-4">
                <x-input-label for="question_text" value="Vraagtekst" />
                <textarea id="question_text" name="question_text" rows="3" class="mt-1 block w-full border-surface-medium bg-pure-white text-deep-black focus:border-action focus:ring-action rounded-input shadow-sm" required>{{ old('question_text', $routeStop->question_text) }}</textarea>
                <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="option_a" value="Antwoord A" />
                    <x-text-input id="option_a" name="option_a" type="text" class="mt-1 block w-full" :value="old('option_a', $routeStop->option_a)" required />
                    <x-input-error :messages="$errors->get('option_a')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="option_b" value="Antwoord B" />
                    <x-text-input id="option_b" name="option_b" type="text" class="mt-1 block w-full" :value="old('option_b', $routeStop->option_b)" required />
                    <x-input-error :messages="$errors->get('option_b')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="option_c" value="Antwoord C (optioneel)" />
                    <x-text-input id="option_c" name="option_c" type="text" class="mt-1 block w-full" :value="old('option_c', $routeStop->option_c)" />
                    <x-input-error :messages="$errors->get('option_c')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="option_d" value="Antwoord D (optioneel)" />
                    <x-text-input id="option_d" name="option_d" type="text" class="mt-1 block w-full" :value="old('option_d', $routeStop->option_d)" />
                    <x-input-error :messages="$errors->get('option_d')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="correct_option" value="Correct antwoord" />
                    <select id="correct_option" name="correct_option" class="mt-1 block w-full border-surface-medium bg-pure-white text-deep-black focus:border-action focus:ring-action rounded-input shadow-sm" required>
                        <option value="">Selecteer...</option>
                        <option value="A" {{ old('correct_option', $routeStop->correct_option) === 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('correct_option', $routeStop->correct_option) === 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ old('correct_option', $routeStop->correct_option) === 'C' ? 'selected' : '' }}>C</option>
                        <option value="D" {{ old('correct_option', $routeStop->correct_option) === 'D' ? 'selected' : '' }}>D</option>
                    </select>
                    <x-input-error :messages="$errors->get('correct_option')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="points" value="Punten" />
                    <x-text-input id="points" name="points" type="number" min="1" class="mt-1 block w-full" :value="old('points', $routeStop->points)" required />
                    <x-input-error :messages="$errors->get('points')" class="mt-2" />
                </div>
            </div>

            <div class="mb-6">
                <x-input-label for="image" value="Vraag afbeelding" />

                @if($routeStop->image_path)
                    <div class="mt-2 mb-3">
                        <p class="text-small text-deep-black mb-2">Huidige afbeelding:</p>
                        <img src="{{ Storage::url($routeStop->image_path) }}" alt="Huidige afbeelding" class="h-24 w-32 object-cover rounded-lg border border-surface-medium">
                    </div>
                @endif

                <input id="image" name="image" type="file" accept="image/*" class="mt-2 block w-full text-sm text-deep-black
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-button file:border file:border-sky-100
                    file:text-sm file:font-semibold
                    file:bg-sky-50 file:text-sky-700 file:shadow-none
                    hover:file:bg-sky-100" {{ $routeStop->image_path ? '' : 'required' }} />
                <p class="mt-1.5 text-sm text-gray-500">
                    Formaat: JPEG, PNG, GIF of WebP. Max 2MB. Aanbevolen: vierkante afbeelding (bijv. 400x400 pixels).
                </p>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.locations.route-stops.index', $routeStop->location) }}">
                    <x-secondary-button type="button">Annuleren</x-secondary-button>
                </a>
                <x-primary-button>Bijwerken</x-primary-button>
            </div>
        </form>
    </div>
</x-admin.layout>
