
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginering"
         class="flex justify-center mt-6">
        <ul class="inline-flex items-center gap-1 text-sm">

            {{-- Vorige --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-2 rounded-full border border-gray-200 text-gray-400 cursor-not-allowed">
                        Vorige
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="px-3 py-2 rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Vorige
                    </a>
                </li>
            @endif

            {{-- Pagina nummers --}}
            @foreach ($elements as $element)
                {{-- "..." --}}
                @if (is_string($element))
                    <li>
                        <span class="px-3 py-2 text-gray-400">{{ $element }}</span>
                    </li>
                @endif

                {{-- Pagina links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span
                                    aria-current="page"
                                    class="px-3 py-2 rounded-full bg-orange-500 text-white font-medium">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="px-3 py-2 rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Volgende --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="px-3 py-2 rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Volgende
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-2 rounded-full border border-gray-200 text-gray-400 cursor-not-allowed">
                        Volgende
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif
