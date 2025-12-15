@props(['message' => 'Geen resultaten gevonden.', 'hasFilters' => false])

<div {{ $attributes->merge(['class' => 'px-6 py-8 text-center']) }}>
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <h3 class="mt-2 text-sm font-medium text-deep-black">{{ $message }}</h3>
    @if($hasFilters)
        <p class="mt-1 text-sm text-gray-500">Probeer andere zoektermen of filters.</p>
    @endif
</div>
