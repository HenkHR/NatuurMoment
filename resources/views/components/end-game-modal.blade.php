@props(['show' => false])

@if($show)
<div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-xl">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex flex-col gap-2">
                    <h3 class="text-xl font-semibold text-gray-900">Spel afronden</h3>
                    <p class="text-gray-600 text-sm">Weet je zeker dat je het spel wilt afronden?</p>
                </div>
            </div>


            <div class="flex gap-3 justify-between">
                <button
                    wire:click="cancelEndGame"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-small transition flex-1">
                    Annuleren
                </button>
                <button
                    wire:click="endGame"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-small transition flex-1">
                    Afronden
                </button>
            </div>
        </div>
    </div>
</div>
@endif
