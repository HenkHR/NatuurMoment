@php use App\Constants\GameMode; @endphp
<x-admin.layout>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-h2 text-deep-black">Locaties</h2>
        <a href="{{ route('admin.locations.create') }}">
            <x-primary-button>Nieuwe locatie</x-primary-button>
        </a>
    </div>

    {{-- Search & Filter Bar (Live filtering) --}}
    <form id="filterForm" method="GET" action="{{ route('admin.locations.index') }}" class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <input
                        type="text"
                        id="searchInput"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Zoek op naam of regio..."
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-deep-black"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <select
                    id="regioSelect"
                    name="regio"
                    class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-deep-black bg-white min-w-[160px]"
                >
                    <option value="">Alle regio's</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" @selected(request('regio') === $province)>
                            {{ $province }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const regioSelect = document.getElementById('regioSelect');
            let debounceTimer;

            // Check if user was searching (store in sessionStorage)
            const wasSearching = sessionStorage.getItem('adminLocationsSearching') === 'true';
            if (wasSearching) {
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
                sessionStorage.removeItem('adminLocationsSearching');
            }

            // Debounced search input
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    sessionStorage.setItem('adminLocationsSearching', 'true');
                    form.submit();
                }, 400);
            });

            // Immediate submit on regio change
            regioSelect.addEventListener('change', () => form.submit());
        });
    </script>

    {{-- Desktop: Table --}}
    <div class="hidden md:block bg-pure-white overflow-hidden rounded-card shadow-card">
        <table class="w-full">
            <thead class="bg-sky-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Naam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Bingo Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Vragen</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-sky-700 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-medium">
                @forelse ($locations as $location)
                    <tr class="hover:bg-sky-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.locations.edit', $location) }}'">
                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-deep-black">
                            {{ $location->name }}
                            {{-- REQ-008: Warning badge when incomplete active modes --}}
                            @if($location->has_incomplete_active_mode)
                                <span class="text-orange-500 ml-1" title="Actieve spelmodus heeft onvoldoende content">⚠️</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($location->has_bingo_mode)
                                <a href="{{ route('admin.locations.bingo-items.index', $location) }}" onclick="event.stopPropagation()" class="inline-flex items-center gap-1.5 px-2 py-2 {{ $location->bingo_items_count < GameMode::MIN_BINGO_ITEMS ? 'text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100' : 'text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100' }} rounded-md transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    {{-- REQ-007: Red text for counts under minimum --}}
                                    <span class="font-semibold">{{ $location->bingo_items_count }}</span>
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($location->has_vragen_mode)
                                <a href="{{ route('admin.locations.route-stops.index', $location) }}" onclick="event.stopPropagation()" class="inline-flex items-center gap-1.5 px-2 py-2 {{ $location->route_stops_count < GameMode::MIN_QUESTIONS ? 'text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100' : 'text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100' }} rounded-md transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{-- REQ-007: Red text for counts under minimum --}}
                                    <span class="font-semibold">{{ $location->route_stops_count }}</span>
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.locations.edit', $location) }}" onclick="event.stopPropagation()" class="p-2 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-button transition-colors" title="Bewerken">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button x-data x-on:click.stop="$dispatch('open-modal', 'delete-location-{{ $location->id }}')" class="p-2 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-button transition-colors" title="Verwijder">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-admin.empty-state
                                :message="$hasFilters ? 'Geen locaties gevonden voor deze filters.' : 'Geen locaties gevonden.'"
                                :hasFilters="$hasFilters"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile: Cards --}}
    <div class="md:hidden space-y-4">
        @forelse ($locations as $location)
            <div class="bg-pure-white rounded-card shadow-card p-4 flex justify-between items-center cursor-pointer hover:bg-sky-50/50 transition-colors" onclick="window.location='{{ route('admin.locations.edit', $location) }}'">
                <div>
                    <h3 class="text-base font-medium text-deep-black">
                        {{ $location->name }}
                        @if($location->has_incomplete_active_mode)
                            <span class="text-orange-500 ml-1" title="Actieve spelmodus heeft onvoldoende content">⚠️</span>
                        @endif
                    </h3>
                    <div class="mt-2 flex gap-2 text-sm">
                        @if($location->has_bingo_mode)
                            <a href="{{ route('admin.locations.bingo-items.index', $location) }}" onclick="event.stopPropagation()" class="inline-flex items-center gap-1.5 px-2 py-2 {{ $location->bingo_items_count < GameMode::MIN_BINGO_ITEMS ? 'text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100' : 'text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100' }} rounded-md transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span class="font-semibold">{{ $location->bingo_items_count }}</span>
                            </a>
                        @endif
                        @if($location->has_vragen_mode)
                            <a href="{{ route('admin.locations.route-stops.index', $location) }}" onclick="event.stopPropagation()" class="inline-flex items-center gap-1.5 px-2 py-2 {{ $location->route_stops_count < GameMode::MIN_QUESTIONS ? 'text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100' : 'text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100' }} rounded-md transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-semibold">{{ $location->route_stops_count }}</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('admin.locations.edit', $location) }}" onclick="event.stopPropagation()" class="p-2 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-button transition-colors" title="Bewerken">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <button x-data x-on:click.stop="$dispatch('open-modal', 'delete-location-{{ $location->id }}')" class="p-2 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-button transition-colors" title="Verwijder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-pure-white rounded-card shadow-card">
                <x-admin.empty-state
                    :message="$hasFilters ? 'Geen locaties gevonden voor deze filters.' : 'Geen locaties gevonden.'"
                    :hasFilters="$hasFilters"
                />
            </div>
        @endforelse
    </div>

    {{-- Modals (shared between desktop and mobile) --}}
    @foreach ($locations as $location)
        <x-modal name="delete-location-{{ $location->id }}" focusable>
            <form method="POST" action="{{ route('admin.locations.destroy', $location) }}">
                @csrf
                @method('DELETE')

                <h2 class="text-h3 text-deep-black">Locatie verwijderen?</h2>
                <p class="mt-2 text-body text-deep-black">
                    Weet je zeker dat je "{{ $location->name }}" wilt verwijderen?
                    Alle gekoppelde bingo items en vragen worden ook verwijderd.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
                    <x-danger-button>Verwijderen</x-danger-button>
                </div>
            </form>
        </x-modal>
    @endforeach

    <div class="mt-6">
        {{ $locations->links('vendor.pagination.admin') }}
    </div>
</x-admin.layout>
