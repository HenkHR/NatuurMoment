@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-action text-start text-base font-medium text-forest-800 bg-forest-50 focus:outline-none focus:text-forest-900 focus:bg-forest-100 focus:border-action-600 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-forest-600 hover:text-forest-800 hover:bg-surface-light hover:border-forest-300 focus:outline-none focus:text-forest-800 focus:bg-surface-light focus:border-forest-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
