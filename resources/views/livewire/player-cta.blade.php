@php
    $introTitle = config('cta.intro_title', 'Ontdek wat jij kunt doen!');
    $introText  = config('cta.intro_text',  'Wat kan jij betekenen voor de natuur? Lees het hier.');
@endphp

<div class="w-full">
    <a href="#maincontent"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-white px-3 py-2 rounded shadow">
        Ga naar inhoud
    </a>

<div class="w-full">
    <div class="max-w-5xl mx-auto px-4 md:px-8 mt-4 flex items-center justify-between gap-3">
        <a
            href="{{ route('home') }}"
            class="bg-forest-500 hover:bg-forest-600 text-pure-white font-semibold
                px-4 py-2 rounded-button shadow-card inline-flex items-center gap-2
                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                focus-visible:ring-forest-500 focus-visible:ring-offset-surface-light"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-10.5z"/>
            </svg>
            Home
        </a>

        @if($showMembership)
            <a
                href="{{ config('cta.membership_url', 'https://www.natuurmonumenten.nl/word-lid') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="bg-[#0076A8] hover:brightness-110 text-pure-white font-semibold
                    px-4 py-2 rounded-button shadow-card inline-flex items-center gap-2
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                    focus-visible:ring-[#0076A8] focus-visible:ring-offset-surface-light"
                aria-label="Word lid (opent in nieuw tabblad)"
            >
                Word nu lid! <span class="sr-only">(opent in nieuw tabblad)</span>
            </a>
        @endif
    </div>

    <main id="maincontent" class="flex-1">
        <section class="max-w-5xl mx-auto w-full px-4 md:px-8 pt-6 pb-8">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                {{ $introTitle }}
            </h2>
            <p class="text-sm sm:text-base text-gray-600 mt-1 mb-4">
                {{ $introText }}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                @foreach($cards as $card)
                    @php
                        $title = $card['title'] ?? '';
                        $desc  = $card['description'] ?? '';
                        $img   = $card['image_url'] ?? null;
                        $alt   = $card['image_alt'] ?? $title;
                        $url   = $card['link_url'] ?? null;
                        $btn   = $card['link_label'] ?? 'Bekijk tips';
                    @endphp

                    <article class="bg-white rounded-xl shadow-card overflow-hidden border border-gray-100">

                        <div class="bg-forest-700 text-pure-white px-6 py-4">
                            <h3 class="text-xl sm:text-2xl font-bold leading-tight">
                                {{ $title }}
                            </h3>
                        </div>

                        @if($img)
                            <div class="h-44 sm:h-52 w-full overflow-hidden bg-gray-100">
                                <img
                                    src="{{ $img }}"
                                    alt="{{ $alt }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                    referrerpolicy="no-referrer"
                                >
                            </div>
                        @endif

                        <div class="px-6 py-5">
                            <p class="text-gray-800 text-sm sm:text-base line-clamp-3">
                                {{ $desc }}
                            </p>

                            <div class="mt-4">
                                @if($url)
                                    <a
                                        href="{{ $url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="w-full inline-flex items-center justify-center
                                               bg-action-500 hover:bg-action-600 text-white font-semibold
                                               rounded-button px-4 py-3 shadow-card transition
                                               focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                                               focus-visible:ring-action-500"
                                        aria-label="{{ $btn }}: {{ $title }} (opent in nieuw venster)"
                                    >
                                        {{ $btn }}
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="w-full inline-flex items-center justify-center
                                               bg-gray-300 text-gray-600 font-semibold
                                               rounded-button px-4 py-3"
                                    >
                                        {{ $btn }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</div class="w-full">
