@props(['show' => false])

@if($show)
<div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-xl">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Spel afronden</h3>
                    <p class="text-gray-600 text-sm">Weet je zeker dat je het spel wilt afronden?</p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6">
                <p class="text-amber-800 text-sm">
                    <strong>Let op:</strong> Alle spelers zullen hun huidige punten behouden en het leaderboard wordt getoond.
                </p>
            </div>

            <div class="flex gap-3 justify-end">
                <button
                    wire:click="cancelEndGame"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                    Annuleren
                </button>
                <button
                    wire:click="endGame"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                    Spel afronden
                </button>
            </div>
        </div>
    </div>
</div>
@endif
