@props([
  'name',
  'rules' => [],
  'title' => 'Speluitleg',
  'maxWidth' => '2xl',
])

<x-modal :name="$name" :maxWidth="$maxWidth" focusable>
    <div class="flex items-center justify-between border-b border-surface-medium pb-3">
        <h2 class="text-lg sm:text-xl font-bold text-deep-black">
            {{ $title }}
        </h2>

        <button
            type="button"
            x-on:click="$dispatch('close-modal', '{{ $name }}')"
            class="bg-surface-medium text-deep-black font-semibold px-4 py-2 rounded-button shadow-card transition
                   hover:shadow-lg hover:-translate-y-0.5
                   focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                   focus-visible:ring-forest-500 focus-visible:ring-offset-pure-white"
        >
            Sluiten
        </button>
    </div>

    <div class="mt-4 max-h-[70vh] overflow-y-auto">
        <div class="rounded-card overflow-hidden">
            <x-game.rules-card
                :rules="$rules"
                :showActions="false"
                class="shadow-none rounded-none"
            />
        </div>
    </div>
</x-modal>
