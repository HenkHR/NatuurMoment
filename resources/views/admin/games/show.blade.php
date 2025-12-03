<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.games.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
            &larr; Terug naar games
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Game Details</h3>
                <p class="text-3xl font-mono font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ $game->pin }}</p>
            </div>
            <div>
                @switch($game->status)
                    @case('lobby')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            Lobby
                        </span>
                        @break
                    @case('started')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Gestart
                        </span>
                        @break
                    @case('finished')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            Afgelopen
                        </span>
                        @break
                @endswitch
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Locatie</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $game->location->name ?? 'Onbekend' }}</dd>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aangemaakt</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $game->created_at->format('d-m-Y H:i:s') }}</dd>
            </div>

            @if ($game->started_at)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gestart op</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $game->started_at->format('d-m-Y H:i:s') }}</dd>
                </div>
            @endif

            @if ($game->finished_at)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Afgelopen op</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $game->finished_at->format('d-m-Y H:i:s') }}</dd>
                </div>
            @endif
        </dl>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
            <button x-data="" x-on:click="$dispatch('open-modal', 'delete-game-{{ $game->id }}')" class="text-red-600 hover:text-red-900 dark:text-red-400">
                <x-danger-button>Game verwijderen</x-danger-button>
            </button>
        </div>
    </div>

    <x-modal name="delete-game-{{ $game->id }}" focusable>
        <form method="POST" action="{{ route('admin.games.destroy', $game) }}" class="p-6">
            @csrf
            @method('DELETE')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Game verwijderen?</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Weet je zeker dat je game "{{ $game->pin }}" wilt verwijderen?
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
                <x-danger-button>Verwijderen</x-danger-button>
            </div>
        </form>
    </x-modal>
</x-admin.layout>
