<x-admin.layout>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-h2 text-forest-800">Games</h2>
    </div>

    <div class="bg-pure-white overflow-x-auto rounded-card shadow-card">
        <table class="w-full min-w-[600px]">
            <thead class="bg-forest-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">PIN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">Locatie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-forest-700 uppercase tracking-wider">Aangemaakt</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-forest-700 uppercase tracking-wider">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-medium">
                @forelse ($games as $game)
                    <tr class="hover:bg-forest-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-deep-black">
                            {{ $game->pin }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-forest-600">
                            {{ $game->location->name ?? 'Onbekend' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @switch($game->status)
                                @case('lobby')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-badge bg-action-100 text-action-700">
                                        Lobby
                                    </span>
                                    @break
                                @case('started')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-badge bg-forest-100 text-forest-700">
                                        Gestart
                                    </span>
                                    @break
                                @case('finished')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-badge bg-surface-medium text-deep-black">
                                        Afgelopen
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-forest-600">
                            {{ $game->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.games.show', $game) }}" class="p-2 text-sky-600 hover:text-sky-700 hover:bg-sky-50 rounded-button transition-colors" title="Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button x-data="" x-on:click="$dispatch('open-modal', 'delete-game-{{ $game->id }}')" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-button transition-colors" title="Verwijder">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <x-modal name="delete-game-{{ $game->id }}" focusable>
                        <form method="POST" action="{{ route('admin.games.destroy', $game) }}">
                            @csrf
                            @method('DELETE')

                            <h2 class="text-h3 text-forest-800">Game verwijderen?</h2>
                            <p class="mt-2 text-body text-forest-600">
                                Weet je zeker dat je game "{{ $game->pin }}" wilt verwijderen?
                            </p>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
                                <x-danger-button>Verwijderen</x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-forest-600">
                            Geen games gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $games->links() }}
    </div>
</x-admin.layout>
