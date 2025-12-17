@props(['name' => 'regio', 'value' => null, 'provinces' => null])

@php
    $provinces = $provinces ?? config('provinces', []);
    $selectedValue = $value ?? request($name);
@endphp

<select
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'rounded-lg border border-gray-300 px-4 py-2 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 focus:outline-none text-deep-black bg-white min-w-[160px]']) }}
>
    <option value="">Alle regio's</option>
    @foreach($provinces as $province)
        <option value="{{ $province }}" @selected($selectedValue === $province)>
            {{ $province }}
        </option>
    @endforeach
</select>
