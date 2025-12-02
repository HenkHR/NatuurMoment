@props([
    'rules' => [],
    'buttonLabel' => 'Spel spelen',
])

<section {{ $attributes->merge(['class' => 'bg-pure-white rounded-card shadow-card overflow-hidden']) }}>
    <div class="rounded-card overflow-hidden bg-forest">
        @foreach($rules as $index => $rule)
            @php
                $bgClass = match($index) {
                    0, 2 => 'bg-forest',        
                    default => 'bg-forest-500', 
                };

                $clipPath = match($index) {
                    0 => 'polygon(0 0, 100% 0, 100% 100%, 0 100%)',

                    1 => 'polygon(0 12%, 100% 0, 100% 100%, 0 100%)',

                    2 => 'polygon(0 0, 100% 0, 100% 88%, 0 100%)',

                    default => 'polygon(0 12%, 100% 0, 100% 100%, 0 100%)',
                };

                $overlapClass = $index > 0 ? '-mt-[2px]' : '';
            @endphp

            <div class="{{ $overlapClass }}">
                <div
                    class="{{ $bgClass }} text-pure-white"
                    style="clip-path: {{ $clipPath }};"
                >
                    <p class="px-4 py-4 text-small">
                        {{ $rule }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="px-4 py-5 flex justify-center bg-pure-white">
        <button
            type="button"
            class="w-full max-w-xs bg-action-500 hover:bg-action-600 text-pure-white font-semibold text-small py-3 rounded-button shadow-card transition"
        >
            {{ $buttonLabel }}
        </button>
    </div>
</section>
