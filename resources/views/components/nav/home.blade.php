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

        <a href="{{ route('player.join') }}"
           class="bg-action-500 text-pure-white font-semibold text-small py-2 px-3 rounded-button text-center shadow-card transition">
            Join Game
        </a>

    </div>
</header>
