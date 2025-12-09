<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full">
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">Spel Aanmaken</h1>
        <p class="text-gray-600 text-center mb-6">{{ $location->name }}</p>

        @if(session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">
            <div>
                <label for="timer-duration" class="block text-sm font-medium text-gray-700 mb-2">
                    Speelduur
                </label>
                <select
                    wire:model="timerDuration"
                    id="timer-duration"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-forest-500 focus:border-forest-500">
                    <option value="">Kies speelduur...</option>
                    @foreach(\App\Livewire\CreateGame::TIMER_DURATIONS as $duration)
                        <option value="{{ $duration }}">{{ $duration }} minuten</option>
                    @endforeach
                    <option value="0">Zonder tijdslimiet</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Het spel eindigt automatisch na deze tijd, of handmatig zonder limiet</p>
            </div>

            <button
                wire:click="createGame"
                class="w-full bg-forest-500 hover:bg-forest-600 text-white font-semibold py-3 rounded-lg transition shadow-md">
                Aanmaken
            </button>

            <a href="{{ route('home') }}" class="block text-center text-gray-500 hover:text-gray-700 text-sm">
                Annuleren
            </a>
        </div>
    </div>
</div>
