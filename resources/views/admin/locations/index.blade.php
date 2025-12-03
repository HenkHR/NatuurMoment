<x-admin.layout>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Locaties</h3>
        <a href="{{ route('admin.locations.create') }}">
            <x-primary-button>Nieuwe locatie</x-primary-button>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Naam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bingo Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vragen</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse ($locations as $location)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $location->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('admin.locations.bingo-items.index', $location) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                {{ $location->bingo_items_count }} items
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('admin.locations.route-stops.index', $location) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                {{ $location->route_stops_count }} vragen
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.locations.edit', $location) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Bewerk</a>
                            <button x-data="" x-on:click="$dispatch('open-modal', 'delete-location-{{ $location->id }}')" class="text-red-600 hover:text-red-900 dark:text-red-400">Verwijder</button>
                        </td>
                    </tr>

                    <x-modal name="delete-location-{{ $location->id }}" focusable>
                        <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" class="p-6">
                            @csrf
                            @method('DELETE')

                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Locatie verwijderen?</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Weet je zeker dat je "{{ $location->name }}" wilt verwijderen?
                                Alle gekoppelde bingo items en vragen worden ook verwijderd.
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
                            Geen locaties gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $locations->links() }}
    </div>
</x-admin.layout>
