@props(['message' => 'Geen resultaten gevonden.', 'hasFilters' => false])

<div {{ $attributes->merge(['class' => 'px-6 py-8 text-center']) }}>
    <p class="text-sm font-medium text-deep-black">{{ $message }}</p>
    @if($hasFilters)
        <p class="mt-1 text-sm text-gray-500">Probeer andere zoektermen of filters.</p>
    @endif
</div>
