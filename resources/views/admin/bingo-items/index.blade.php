<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
            &larr; Terug naar locaties
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Bingo Items</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Locatie: {{ $location->name }}</p>
        </div>
        <a href="{{ route('admin.locations.bingo-items.create', $location) }}">
            <x-primary-button>Nieuw bingo item</x-primary-button>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Label</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Punten</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Icon</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse ($bingoItems as $bingoItem)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $bingoItem->label }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $bingoItem->points }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($bingoItem->icon)
                                <img src="{{ Storage::url($bingoItem->icon) }}" alt="{{ $bingoItem->label }}" class="h-10 w-10 object-cover rounded">
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.bingo-items.edit', $bingoItem) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Bewerk</a>
                            <button x-data="" x-on:click="$dispatch('open-modal', 'delete-bingo-item-{{ $bingoItem->id }}')" class="text-red-600 hover:text-red-900 dark:text-red-400">Verwijder</button>
                        </td>
                    </tr>

                    <x-modal name="delete-bingo-item-{{ $bingoItem->id }}" focusable>
                        <form method="POST" action="{{ route('admin.bingo-items.destroy', $bingoItem) }}" class="p-6">
                            @csrf
                            @method('DELETE')

                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bingo item verwijderen?</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Weet je zeker dat je "{{ $bingoItem->label }}" wilt verwijderen?
                            </p>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
                                <x-danger-button>Verwijderen</x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Geen bingo items gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bingoItems->links() }}
    </div>
</x-admin.layout>
