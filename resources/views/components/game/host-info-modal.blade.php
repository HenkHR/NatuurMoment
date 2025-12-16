@props([
    'name',
    'title' => 'Host informatie',
    'items' => [],
    'maxWidth' => '2xl',
])

<x-modal
    :name="$name"
    :maxWidth="$maxWidth"
    focusable
    aria-labelledby="modal-title-{{ $name }}"
>
    <div class="pb-3">
        <h2 id="modal-title-{{ $name }}" class="text-lg sm:text-xl font-bold text-deep-black">
            {{ $title }}
        </h2>
    </div>

    <div class="max-h-[60vh] overflow-y-auto">
        <x-game.host-info-card :items="$items" class="shadow-none" />
    </div>

    <div class="mt-4">
        <button
            type="button"
            x-on:click="$dispatch('close-modal', '{{ $name }}')"
            class="w-full bg-sky-500 text-white font-medium px-6 py-2.5 rounded-full transition
                   hover:bg-sky-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
        >
            Sluiten
        </button>
    </div>
</x-modal>
