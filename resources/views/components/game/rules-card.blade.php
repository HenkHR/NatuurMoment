@props([
    'rules' => [],
    'locationId',
])

<section
    {{ $attributes->merge(['class' => 'bg-pure-white rounded-card shadow-card overflow-hidden']) }}
    aria-labelledby="rules-heading"
>
    <h2 id="rules-heading" class="sr-only">
        Spelregels voor {{ $rulesTitle ?? 'dit spel' }}
    </h2>

    <ol class="rounded-card overflow-hidden bg-forest m-0 p-0 list-none">
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

                $textAlignClass = in_array($index, [1, 3]) ? 'text-right' : 'text-left';
            @endphp

            <li class="{{ $overlapClass }}">
                <div
                    class="{{ $bgClass }} text-pure-white"
                    style="clip-path: {{ $clipPath }};"
                >
                    <p class="px-4 py-4 text-base md:text-lg {{ $textAlignClass }}">
                        <span class="sr-only">Regel {{ $index + 1 }}: </span>
                        {{ $rule }}
                    </p>
                </div>
            </li>
        @endforeach
    </ol>

    {{-- knoppen --}}
    <div class="px-4 py-5 flex flex-col items-center gap-3 bg-pure-white">
        <form
            action="{{ route('play.create', $locationId) }}"
            method="POST"
            class="w-full max-w-xs"
        >
            @csrf
            <button
                type="submit"
                class="w-full bg-action-500 hover:bg-action-600 text-pure-white font-semibold text-small py-3 rounded-button text-center shadow-card transition"
            >
                Spel aanmaken
            </button>
        </form>

        <a
            href="{{ route('player.join') }}"
            class="w-full max-w-xs bg-sky-500 hover:bg-sky-600 text-pure-white font-semibold text-small py-3 rounded-button text-center shadow-card transition"
        >
            Meedoen met een spel
        </a>
    </div>
</section>
