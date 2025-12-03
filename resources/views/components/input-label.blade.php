@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-deep-black']) }}>
    {{ $value ?? $slot }}
</label>
