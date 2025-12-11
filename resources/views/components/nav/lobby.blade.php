@props([
    'title',
    'subtitle' => null,
])

<header
    class="bg-forest-700 text-pure-white shadow-card flex-shrink-0"
    style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 28px), 0 100%);"
>
    <div class="max-w-5xl mx-auto px-4 lg:px-8 pt-8 pb-10 text-center">
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold leading-tight">
            {{ $title }}
        </h1>

        @if($subtitle)
            <p class="text-sm md:text-base font-semibold mt-1">
                {{ $subtitle }}
            </p>
        @endif
    </div>
</header>
