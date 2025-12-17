@props([
  'name',
  'rules' => [],
  'title' => 'Speluitleg',
  'maxWidth' => '2xl',
])

<<<<<<< Updated upstream
<x-modal
    :name="$name"
    :maxWidth="$maxWidth"
    focusable
    aria-labelledby="rules-modal-title-{{ $name }}"
>
    <div class="pb-3">
        <h2 id="rules-modal-title-{{ $name }}" class="text-lg sm:text-xl font-bold text-deep-black">
=======
<x-modal :name="$name" :maxWidth="$maxWidth" focusable>
    <div class="border-b border-surface-medium pb-3">
        <h2 class="text-lg sm:text-xl font-bold text-deep-black">
>>>>>>> Stashed changes
            {{ $title }}
        </h2>
    </div>

<<<<<<< Updated upstream
    <div class="max-h-[60vh] overflow-y-auto">
=======
    <div class="mt-4 max-h-[60vh] overflow-y-auto">
>>>>>>> Stashed changes
        <div class="rounded-card overflow-hidden">
            <x-game.rules-card
                :rules="$rules"
                :showActions="false"
                class="shadow-none rounded-none"
            />
        </div>
    </div>

<<<<<<< Updated upstream
    <div class="mt-4">
        <button
            type="button"
            x-on:click="$dispatch('close-modal', '{{ $name }}')"
            class="w-full bg-surface-medium text-deep-black font-semibold px-4 py-2 rounded-button shadow-card transition
                   hover:shadow-lg hover:-translate-y-0.5
=======
    <div class="mt-4 pt-3 border-t border-surface-medium">
        <button
            type="button"
            x-on:click="$dispatch('close-modal', '{{ $name }}')"
            class="w-full bg-forest-500 text-pure-white font-semibold px-4 py-3 rounded-button shadow-card transition
                   hover:bg-forest-600 hover:shadow-lg hover:-translate-y-0.5
>>>>>>> Stashed changes
                   focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                   focus-visible:ring-forest-500 focus-visible:ring-offset-pure-white"
        >
            Sluiten
        </button>
    </div>
</x-modal>
