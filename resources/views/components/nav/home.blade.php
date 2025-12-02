@props([
    'title' => 'Natuurmonumenten',
    'back' => false,
])

<header class="bg-sky-500 px-4 py-3 flex items-center gap-3 shadow-card">

    {{-- optionele terug-knop links --}}
    @if($back)
        <button type="button" class="text-pure-white text-xl">
            {{-- hier later een icoon (heroicon / svg) --}}
            ←
        </button>
    @endif

    {{-- logo / tekst --}}
    <div class="flex items-center gap-2">
        {{-- placeholder voor logo-blokje --}}
        <div class="w-8 h-8 rounded-icon bg-pure-white/10 flex items-center justify-center">
            <span class="text-pure-white text-xs font-semibold">Nm</span>
        </div>

        <span class="text-pure-white font-semibold text-small tracking-wide">
            {{ $title }}
        </span>
    </div>
</header>
