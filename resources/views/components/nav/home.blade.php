@props([
    'title' => 'Natuurmonumenten',
    'back' => false,
])

<header class="bg-forest-700 h-14 pl-1 pr-4 shadow-card flex items-center">
    <div class="flex items-center gap-3 w-full">

        @if($back)
            <button type="button" class="text-pure-white text-xl">
                ←
            </button>
        @endif

        <a href="{{ url('/') }}" class="flex items-center">
            <img
                src="{{ asset('images/NM_LOGO.png') }}"
                alt="Natuurmonumenten logo"
                class="h-20 w-auto"
            >
        </a>

    </div>
</header>
