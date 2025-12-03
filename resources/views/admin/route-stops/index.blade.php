<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.index') }}" class="text-sky-600 hover:text-sky-700">
            &larr; Terug naar locaties
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-h2 text-forest-800">Vragen</h2>
            <p class="text-body text-forest-600">Locatie: {{ $location->name }}</p>
        </div>
        <a href="{{ route('admin.locations.route-stops.create', $location) }}">
            <x-primary-button>Nieuwe vraag</x-primary-button>
        </a>
    </div>

    {{-- Desktop: Table --}}
    <div class="hidden md:block bg-pure-white overflow-hidden rounded-card shadow-card">
        <table class="w-full">
            <thead class="bg-forest-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">Naam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">Vraag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">Correct</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">Punten</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-forest-700 uppercase tracking-wider">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-medium">
                @forelse ($routeStops as $routeStop)
                    <tr class="hover:bg-forest-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-forest-600">
                            {{ $routeStop->sequence }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-deep-black">
                            {{ $routeStop->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-forest-600 max-w-xs truncate">
                            {{ Str::limit($routeStop->question_text, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-forest-600">
                            {{ $routeStop->correct_option }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-forest-600">
                            {{ $routeStop->points }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.route-stops.edit', $routeStop) }}" class="p-2 text-sky-600 hover:text-sky-700 hover:bg-sky-50 rounded-button transition-colors" title="Bewerk">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button x-data="" x-on:click="$dispatch('open-modal', 'delete-route-stop-{{ $routeStop->id }}')" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-button transition-colors" title="Verwijder">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-forest-600">
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
            <div class="bg-pure-white rounded-card shadow-card p-4 flex justify-between items-center">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-forest-100 text-forest-700 text-xs font-medium">
                            {{ $routeStop->sequence }}
                        </span>
                        <h3 class="text-base font-medium text-deep-black truncate">{{ $routeStop->name }}</h3>
                    </div>
                    <p class="mt-2 text-sm text-forest-600 line-clamp-2">{{ $routeStop->question_text }}</p>
                    <div class="mt-2 flex gap-4 text-xs text-forest-500">
                        <span>Correct: {{ $routeStop->correct_option }}</span>
                        <span>{{ $routeStop->points }} punten</span>
                    </div>
                </div>
                <div class="flex gap-1 ml-2">
                    <a href="{{ route('admin.route-stops.edit', $routeStop) }}" class="p-2 text-sky-600 hover:text-sky-700 hover:bg-sky-50 rounded-button transition-colors" title="Bewerk">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <button x-data="" x-on:click="$dispatch('open-modal', 'delete-route-stop-{{ $routeStop->id }}')" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-button transition-colors" title="Verwijder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-pure-white rounded-card shadow-card p-4 text-center text-sm text-forest-600">
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

                <h2 class="text-h3 text-forest-800">Vraag verwijderen?</h2>
                <p class="mt-2 text-body text-forest-600">
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
