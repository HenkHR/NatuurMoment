<div wire:poll.2s.visible="loadPlayers" class="container mx-auto">
    <div class="flex flex-col gap-2 p-4 justify-center items-center w-full">
        <h1>Host Lobby</h1>
        <h2 class="text-center bg-forest-500 text-pure-white rounded-card p-2" >Game PIN: <span class="font-bold bg-forest-400 text-pure-white rounded-card px-2 py-1">{{ $pin }}</span></h2>
        <p>Spelers in de lobby: {{ $playerCount }}</p>

        @if(session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
        @endif

        <!-- Timer Configuration -->
        <div class="w-full max-w-xs bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="font-semibold text-gray-700 mb-3">Timer instellingen</h3>

            <!-- Timer Toggle -->
            <div class="flex items-center justify-between mb-3">
                <label for="timer-toggle" class="text-gray-600">Timer inschakelen</label>
                <button
                    wire:click="$toggle('timerEnabled')"
                    id="timer-toggle"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $timerEnabled ? 'bg-forest-500' : 'bg-gray-300' }}"
                    role="switch"
                    aria-checked="{{ $timerEnabled ? 'true' : 'false' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $timerEnabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            <!-- Timer Duration Dropdown (only shown when enabled) -->
            @if($timerEnabled)
                <div class="flex items-center justify-between">
                    <label for="timer-duration" class="text-gray-600">Speelduur</label>
                    <select
                        wire:model.live="timerDuration"
                        id="timer-duration"
                        class="rounded-lg border-gray-300 text-gray-700 text-sm focus:ring-forest-500 focus:border-forest-500">
                        <option value="">Kies duur...</option>
                        @foreach(\App\Livewire\HostLobby::TIMER_DURATIONS as $duration)
                            <option value="{{ $duration }}">{{ $duration }} minuten</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-2 p-4 w-full">
            <h3>Wachten op spelers...</h3>
            @if(count($players) > 0)
                <ul class="flex flex-col gap-2">
                    @foreach($players as $player)
                        <li wire:key="player-{{ $player['id'] }}" class="text-left flex flex-row justify-between items-center bg-forest-500 text-pure-white rounded-card p-2 w-full" >
                            <span>{{ $player['name'] }}</span>
                            <button class="bg-red-500 text-pure-white font-semibold text-small p-2 rounded-button text-center shadow-card transition">
                                Verwijder speler
                            </button>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Nog geen spelers. Deel de PIN met de spelers!</p>
            @endif
        </div>

        <button
            wire:click="startGame"
            @if($timerEnabled && !$timerDuration) disabled @endif
            class="w-full max-w-xs bg-action-500 hover:bg-action-600 text-pure-white font-semibold text-small py-3 rounded-button text-center shadow-card transition {{ $timerEnabled && !$timerDuration ? 'opacity-50 cursor-not-allowed' : '' }}">
            Start spel
        </button>
        @if($timerEnabled && !$timerDuration)
            <p class="text-sm text-red-500">Selecteer een speelduur om te starten</p>
        @endif
    </div>
</div>
