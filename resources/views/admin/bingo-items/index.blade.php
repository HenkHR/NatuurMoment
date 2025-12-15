<x-admin.layout>
    <h2 class="text-h2 text-deep-black mb-4">{{ $location->name }}</h2>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <a href="{{ route('admin.locations.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 rounded-md transition-colors text-sm font-medium shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Terug naar locaties
        </a>
        <a href="{{ route('admin.locations.bingo-items.create', $location) }}">
            <x-primary-button>Nieuw bingo item</x-primary-button>
        </a>
    </div>

    {{-- Desktop: Table --}}
    <div class="hidden md:block bg-pure-white overflow-hidden rounded-card shadow-card">
        <table class="w-full">
            <thead class="bg-sky-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Label</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Punten</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Feitje</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Icon</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-sky-700 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-medium">
                @forelse ($bingoItems as $bingoItem)
                    <tr class="hover:bg-sky-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.bingo-items.edit', $bingoItem) }}'">
                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-deep-black">
                            {{ $bingoItem->label }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-deep-black">
                            {{ $bingoItem->points }}
                        </td>
                        <td class="px-6 py-4 text-sm text-deep-black max-w-xs">
                            @if($bingoItem->fact)
                                <span class="line-clamp-2" title="{{ $bingoItem->fact }}">{{ $bingoItem->fact }}</span>
                            @else
                                <span class="text-surface-medium">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-forest-600">
                            @if($bingoItem->icon)
                                <img src="{{ Storage::url($bingoItem->icon) }}" alt="{{ $bingoItem->label }}" class="h-10 w-10 object-cover rounded-icon">
                            @else
                                <span class="text-surface-medium">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.bingo-items.edit', $bingoItem) }}" onclick="event.stopPropagation()" class="p-2 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-button transition-colors" title="Bewerken">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button x-data x-on:click.stop="$dispatch('open-modal', 'delete-bingo-item-{{ $bingoItem->id }}')" class="p-2 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-button transition-colors" title="Verwijder">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-deep-black">
                            Geen bingo items gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile: Cards --}}
    <div class="md:hidden space-y-4">
        @forelse ($bingoItems as $bingoItem)
            <div class="bg-pure-white rounded-card shadow-card p-4 flex justify-between items-center cursor-pointer hover:bg-sky-50/50 transition-colors" onclick="window.location='{{ route('admin.bingo-items.edit', $bingoItem) }}'">
                <div class="flex items-center gap-3">
                    @if($bingoItem->icon)
                        <img src="{{ Storage::url($bingoItem->icon) }}" alt="{{ $bingoItem->label }}" class="h-12 w-12 object-cover rounded-icon">
                    @else
                        <div class="h-12 w-12 bg-surface-light rounded-icon flex items-center justify-center">
                            <span class="text-surface-medium text-xs">Geen</span>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-base font-medium text-deep-black">{{ $bingoItem->label }}</h3>
                        <p class="text-sm text-deep-black">{{ $bingoItem->points }} punten</p>
                        @if($bingoItem->fact)
                            <p class="text-xs text-surface-dark mt-1 line-clamp-1">{{ $bingoItem->fact }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('admin.bingo-items.edit', $bingoItem) }}" onclick="event.stopPropagation()" class="p-2 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-button transition-colors" title="Bewerken">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <button x-data x-on:click.stop="$dispatch('open-modal', 'delete-bingo-item-{{ $bingoItem->id }}')" class="p-2 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-button transition-colors" title="Verwijder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-pure-white rounded-card shadow-card p-4 text-center text-sm text-deep-black">
                Geen bingo items gevonden.
            </div>
        @endforelse
    </div>

    {{-- Modals (shared between desktop and mobile) --}}
    @foreach ($bingoItems as $bingoItem)
        <x-modal name="delete-bingo-item-{{ $bingoItem->id }}" focusable>
            <form method="POST" action="{{ route('admin.bingo-items.destroy', $bingoItem) }}">
                @csrf
                @method('DELETE')

                <h2 class="text-h3 text-deep-black">Bingo item verwijderen?</h2>
                <p class="mt-2 text-body text-deep-black">
                    Weet je zeker dat je "{{ $bingoItem->label }}" wilt verwijderen?
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
                    <x-danger-button>Verwijderen</x-danger-button>
                </div>
            </form>
        </x-modal>
    @endforeach

    <div class="mt-6">
        {{ $bingoItems->links('vendor.pagination.admin') }}
    </div>
</x-admin.layout>
