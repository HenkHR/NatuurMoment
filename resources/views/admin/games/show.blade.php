<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.games.index') }}" class="text-sky-600 hover:text-sky-700">
            &larr; Terug naar games
        </a>
    </div>

    <div class="bg-pure-white overflow-hidden rounded-card shadow-card p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-h2 text-forest-800">Game Details</h2>
                <p class="text-3xl font-mono font-bold text-action mt-2">{{ $game->pin }}</p>
            </div>
            <div>
                @switch($game->status)
                    @case('lobby')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-badge bg-action-100 text-action-700">
                            Lobby
                        </span>
                        @break
                    @case('started')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-badge bg-forest-100 text-forest-700">
                            Gestart
                        </span>
                        @break
                    @case('finished')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-badge bg-surface-medium text-deep-black">
                            Afgelopen
                        </span>
                        @break
                @endswitch
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border-t border-surface-medium pt-4">
                <dt class="text-sm font-medium text-forest-600">Locatie</dt>
                <dd class="mt-1 text-sm text-deep-black">{{ $game->location->name ?? 'Onbekend' }}</dd>
            </div>

            <div class="border-t border-surface-medium pt-4">
                <dt class="text-sm font-medium text-forest-600">Aangemaakt</dt>
                <dd class="mt-1 text-sm text-deep-black">{{ $game->created_at->format('d-m-Y H:i:s') }}</dd>
            </div>

            @if ($game->started_at)
                <div class="border-t border-surface-medium pt-4">
                    <dt class="text-sm font-medium text-forest-600">Gestart op</dt>
                    <dd class="mt-1 text-sm text-deep-black">{{ $game->started_at->format('d-m-Y H:i:s') }}</dd>
                </div>
            @endif

            @if ($game->finished_at)
                <div class="border-t border-surface-medium pt-4">
                    <dt class="text-sm font-medium text-forest-600">Afgelopen op</dt>
                    <dd class="mt-1 text-sm text-deep-black">{{ $game->finished_at->format('d-m-Y H:i:s') }}</dd>
                </div>
            @endif
        </dl>

        <div class="mt-6 pt-6 border-t border-surface-medium flex justify-end">
            <button x-data="" x-on:click="$dispatch('open-modal', 'delete-game-{{ $game->id }}')">
                <x-danger-button>Game verwijderen</x-danger-button>
            </button>
        </div>
    </div>

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
</x-admin.layout>
