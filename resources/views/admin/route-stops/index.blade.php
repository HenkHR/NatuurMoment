<x-admin.layout>
    <div class="mb-6">
        <a href="{{ route('admin.locations.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
            &larr; Terug naar locaties
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Vragen</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Locatie: {{ $location->name }}</p>
        </div>
        <a href="{{ route('admin.locations.route-stops.create', $location) }}">
            <x-primary-button>Nieuwe vraag</x-primary-button>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Naam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vraag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Correct</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Punten</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse ($routeStops as $routeStop)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $routeStop->sequence }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $routeStop->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                            {{ Str::limit($routeStop->question_text, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $routeStop->correct_option }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $routeStop->points }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.route-stops.edit', $routeStop) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Bewerk</a>
                            <button x-data="" x-on:click="$dispatch('open-modal', 'delete-route-stop-{{ $routeStop->id }}')" class="text-red-600 hover:text-red-900 dark:text-red-400">Verwijder</button>
                        </td>
                    </tr>

                    <x-modal name="delete-route-stop-{{ $routeStop->id }}" focusable>
                        <form method="POST" action="{{ route('admin.route-stops.destroy', $routeStop) }}" class="p-6">
                            @csrf
                            @method('DELETE')

                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Vraag verwijderen?</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Weet je zeker dat je "{{ $routeStop->name }}" wilt verwijderen?
                            </p>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button x-on:click="$dispatch('close')">Annuleren</x-secondary-button>
                                <x-danger-button>Verwijderen</x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Geen vragen gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $routeStops->links() }}
    </div>
</x-admin.layout>
