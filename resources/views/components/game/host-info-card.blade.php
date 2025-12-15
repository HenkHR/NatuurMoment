@props([
    'items' => [],
])

<section {{ $attributes->merge(['class' => 'bg-pure-white rounded-card overflow-hidden']) }}>
    <ol class="rounded-card overflow-hidden bg-forest m-0 p-0 list-none">
        @foreach($items as $index => $text)
            @php
                $bgClass = match($index) {
                    0, 2, 4, 6 => 'bg-forest',
                    default => 'bg-forest-500',
                };

                $clipPath = match($index) {
                    0 => 'polygon(0 0, 100% 0, 100% 100%, 0 100%)',
                    1 => 'polygon(0 12%, 100% 0, 100% 100%, 0 100%)',
                    2 => 'polygon(0 0, 100% 0, 100% 88%, 0 100%)',
                    default => 'polygon(0 12%, 100% 0, 100% 100%, 0 100%)',
                };

                $overlapClass = $index > 0 ? '-mt-[2px]' : '';
                $textAlignClass = in_array($index, [1, 3, 5]) ? 'text-right' : 'text-left';
            @endphp

            <li class="{{ $overlapClass }}">
                <div class="{{ $bgClass }} text-pure-white" style="clip-path: {{ $clipPath }};">
                    <p class="px-4 py-4 text-base md:text-lg {{ $textAlignClass }}">
                        <span class="sr-only">Stap {{ $index + 1 }}: </span>
                        {{ $text }}
                    </p>
                </div>
            </li>
        @endforeach
    </ol>
</section>
