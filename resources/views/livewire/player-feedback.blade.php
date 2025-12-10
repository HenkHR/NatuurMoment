<div class="p-4 pb-24">
    <div class="bg-[#2E7D32] text-white text-center py-6 rounded-t-lg -mx-4 -mt-4 mb-6">
        <h1 class="text-2xl font-bold">Bedankt voor het spelen!</h1>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-3">Wat vond je van het spel?</label>
            <div class="grid grid-cols-5 gap-2">
                @foreach(range(1, 10) as $num)
                    <button
                        wire:click="setRating({{ $num }})"
                        class="w-10 h-10 rounded-lg font-bold text-white transition
                            {{ $rating === $num ? 'ring-4 ring-yellow-400 scale-110' : '' }}
                            {{ $num <= 2 ? 'bg-red-500 hover:bg-red-600' : '' }}
                            {{ $num >= 3 && $num <= 4 ? 'bg-orange-500 hover:bg-orange-600' : '' }}
                            {{ $num >= 5 && $num <= 6 ? 'bg-yellow-500 hover:bg-yellow-600' : '' }}
                            {{ $num >= 7 && $num <= 8 ? 'bg-lime-500 hover:bg-lime-600' : '' }}
                            {{ $num >= 9 ? 'bg-green-500 hover:bg-green-600' : '' }}">
                        {{ $num }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Hoe oud ben je?</label>
            <input
                type="text"
                wire:model="age"
                placeholder="Leeftijd..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>

        <button
            wire:click="submitFeedback"
            class="w-full py-3 bg-[#0076A8] hover:bg-[#005a82] text-white font-semibold rounded-lg transition">
            Bevestigen
        </button>
    </div>
</div>

