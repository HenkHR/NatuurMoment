@props([
    /**
     * Voorbeeld:
     * [
     *   ['label' => 'Home', 'url' => route('home')],
     *   ['label' => 'Games', 'url' => route('games.index')],
     *   ['label' => 'Natuur Avontuur']
     * ]
     */
    'items' => [],
])

<nav
    aria-label="Breadcrumb"
    {{ $attributes->merge(['class' => 'mb-3']) }}
>
    <ol class="flex flex-wrap items-center text-xs md:text-small text-deep-black/60 gap-2">
        @foreach ($items as $index => $item)
            <li class="flex items-center gap-2">
                @if (!empty($item['url']) && $index !== count($items) - 1)
                    <a
                        href="{{ $item['url'] }}"
                        class="hover:underline text-deep-black/70"
                    >
                        {{ $item['label'] }}
                    </a>
                @else
                    <span
                        class="text-deep-black font-medium"
                        aria-current="page"
                    >
                        {{ $item['label'] }}
                    </span>
                @endif

                @if ($index < count($items) - 1)
                    <span aria-hidden="true">/</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
