<x-admin.layout>
    <h2 class="text-h2 text-deep-black mb-4">{{ $location->name }}</h2>

    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.locations.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-md transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Terug naar locaties
        </a>
        <a href="{{ route('admin.locations.route-stops.create', $location) }}">
            <x-primary-button>Nieuwe vraag</x-primary-button>
        </a>
    </div>

    {{-- Desktop: Table --}}
    <div class="hidden md:block bg-pure-white overflow-hidden rounded-card shadow-card">
        <table class="w-full">
            <thead class="bg-sky-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Afbeelding</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Naam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Vraag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Correct</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Punten</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-sky-700 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-medium">
                @forelse ($routeStops as $routeStop)
                    <tr class="hover:bg-sky-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.route-stops.edit', $routeStop) }}'">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-deep-black">
                            {{ $routeStop->sequence }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($routeStop->image_path)
                                <img src="{{ Storage::url($routeStop->image_path) }}" alt="{{ $routeStop->name }}" class="h-12 w-16 object-cover rounded-lg">
                            @else
                                <div class="h-12 w-16 bg-surface-light rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-surface-medium" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-deep-black">
                            {{ $routeStop->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-deep-black max-w-xs truncate">
                            {{ Str::limit($routeStop->question_text, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-deep-black">
                            {{ $routeStop->correct_option }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-deep-black">
                            {{ $routeStop->points }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.route-stops.edit', $routeStop) }}" onclick="event.stopPropagation()" class="p-2 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-button transition-colors" title="Bewerken">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button x-data x-on:click.stop="$dispatch('open-modal', 'delete-route-stop-{{ $routeStop->id }}')" class="p-2 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-button transition-colors" title="Verwijder">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-deep-black">
                            Geen vragen gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile: Cards --}}
    <div class="md:hidden space-y-4">
        @forelse ($routeStops as $routeStop)
            <div class="bg-pure-white rounded-card shadow-card p-4 flex justify-between items-center cursor-pointer hover:bg-sky-50/50 transition-colors" onclick="window.location='{{ route('admin.route-stops.edit', $routeStop) }}'">
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-medium text-deep-black truncate">{{ $routeStop->name }}</h3>
                    <p class="text-sm text-deep-black line-clamp-1 mt-1">{{ $routeStop->question_text }}</p>
                    <p class="text-xs text-surface-dark mt-2">Antwoord <span class="font-semibold">{{ $routeStop->correct_option }}</span> · <span class="font-semibold">{{ $routeStop->points }}</span> {{ $routeStop->points === 1 ? 'punt' : 'punten' }}</p>
                </div>
                <div class="flex gap-1 flex-shrink-0 ml-2">
                    <a href="{{ route('admin.route-stops.edit', $routeStop) }}" onclick="event.stopPropagation()" class="p-2 text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-button transition-colors" title="Bewerken">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <button x-data x-on:click.stop="$dispatch('open-modal', 'delete-route-stop-{{ $routeStop->id }}')" class="p-2 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-button transition-colors" title="Verwijder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-pure-white rounded-card shadow-card p-4 text-center text-sm text-deep-black">
                Geen vragen gevonden.
            </div>
        @endforelse
    </div>

    {{-- Modals (shared between desktop and mobile) --}}
    @foreach ($routeStops as $routeStop)
        <x-modal name="delete-route-stop-{{ $routeStop->id }}" focusable>
            <form method="POST" action="{{ route('admin.route-stops.destroy', $routeStop) }}">
                @csrf
                @method('DELETE')

                <h2 class="text-h3 text-deep-black">Vraag verwijderen?</h2>
                <p class="mt-2 text-body text-deep-black">
                    Weet je zeker dat je "{{ $routeStop->name }}" wilt verwijderen?
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
                    <x-danger-button>Verwijderen</x-danger-button>
                </div>
            </form>
        </x-modal>
    @endforeach

    <div class="mt-4">
        {{ $routeStops->links() }}
    </div>
</x-admin.layout>
