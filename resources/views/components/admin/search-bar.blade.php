@props(['name' => 'search', 'placeholder' => 'Zoeken...', 'value' => null])

<div class="relative">
    <input
        type="text"
        name="{{ $name }}"
        value="{{ $value ?? request($name) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 focus:outline-none text-deep-black']) }}
    >
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>
</div>
