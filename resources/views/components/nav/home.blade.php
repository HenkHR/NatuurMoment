@props([
    'title' => 'Natuurmonumenten',
    'back' => false,
])

<header class="bg-forest-700 shadow-card">
    <div class="max-w-5xl mx-auto h-16 px-4 lg:px-8 flex items-center gap-4">

        <a href="{{ url('/') }}" class="flex items-center">
            <img
                src="{{ asset('images/NM_LOGO.png') }}"
                alt="Natuurmonumenten logo"
                class="h-10 md:h-12 w-auto"
            >
        </a>

        <div class="flex-1"></div>
    </div>
</header>
