@props([
    'name',
    'title' => 'Host informatie',
    'items' => [],
    'maxWidth' => '2xl',
])

<x-modal :name="$name" :maxWidth="$maxWidth">
    <div class="flex items-center justify-between gap-4">
        <h2 class="text-lg sm:text-xl font-bold text-deep-black">
            {{ $title }}
        </h2>

        <button
            type="button"
            x-on:click="$dispatch('close-modal', '{{ $name }}')"
            class="
                inline-flex items-center justify-center
                w-10 h-10
                rounded-full
                text-red-600 hover:text-red-700
                hover:bg-red-50
                transition
                focus-visible:outline-none
                focus-visible:ring-2
                focus-visible:ring-red-600
                focus-visible:ring-offset-2
            "
            aria-label="Sluiten"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="mt-4 max-h-[70vh] overflow-y-auto">
        <x-game.host-info-card :items="$items" class="shadow-none" />
    </div>
</x-modal>
