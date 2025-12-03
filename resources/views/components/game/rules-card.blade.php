@props([
    'rules' => [],
    'buttonLabel' => 'Spel spelen',
])

<section {{ $attributes->merge(['class' => 'bg-pure-white rounded-card shadow-card overflow-hidden']) }}>
    <div class="rounded-card overflow-hidden bg-forest">
        @foreach($rules as $index => $rule)
            @php
                // kleuren per regel (1 & 3 donker, 2 & 4 licht)
                $bgClass = match($index) {
                    0, 2 => 'bg-forest',
                    default => 'bg-forest-500',
                };

                // clip-path vormen
                $clipPath = match($index) {
                    0 => 'polygon(0 0, 100% 0, 100% 100%, 0 100%)',
                    1 => 'polygon(0 12%, 100% 0, 100% 100%, 0 100%)',
                    2 => 'polygon(0 0, 100% 0, 100% 88%, 0 100%)',
                    default => 'polygon(0 12%, 100% 0, 100% 100%, 0 100%)',
                };

                // overlap om naden te voorkomen
                $overlapClass = $index > 0 ? '-mt-[2px]' : '';

                // ⭐ tekst uitlijning — hier komt jouw nieuwe code ⭐
                $textAlignClass = in_array($index, [1, 3]) ? 'text-right' : 'text-left';
            @endphp

            <div class="{{ $overlapClass }}">
                <div
                    class="{{ $bgClass }} text-pure-white"
                    style="clip-path: {{ $clipPath }};"
                >
                    <p class="px-4 py-4 text-small {{ $textAlignClass }}">
                        {{ $rule }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="px-4 py-5 flex justify-center bg-pure-white">
    <form action="{{ route('play.create', 1) }}" method="POST" class="w-full max-w-xs bg-action-500 hover:bg-action-600 text-pure-white font-semibold text-small py-3 rounded-button text-center shadow-card transition">
                @csrf
                <button type="submit">{{ $buttonLabel }}</button>
            </form>

        
    </div>
</section>
