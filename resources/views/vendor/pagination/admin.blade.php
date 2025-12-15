@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginering" class="flex items-center justify-between">
        <div class="text-sm text-gray-600">
            Toont {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} van {{ $paginator->total() }} resultaten
        </div>

        <ul class="inline-flex items-center gap-1 text-sm">
            {{-- Vorige --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed bg-white">
                        Vorige
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-sky-50 hover:border-sky-300 transition bg-white">
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
                                <span aria-current="page" class="px-3 py-2 rounded-lg bg-sky-600 text-white font-medium">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-sky-50 hover:border-sky-300 transition bg-white">
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
                       class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-sky-50 hover:border-sky-300 transition bg-white">
                        Volgende
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed bg-white">
                        Volgende
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
