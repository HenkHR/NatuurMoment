<div wire:poll.2s="loadPlayers" class="container mx-auto">
    <div class="flex flex-col gap-2 p-4 justify-center items-center w-full">
        <h1>Host Lobby</h1>
        <h2 class="text-center bg-forest-500 text-pure-white rounded-card p-2" >Game PIN: <span class="font-bold bg-forest-400 text-pure-white rounded-card px-2 py-1">{{ $pin }}</span></h2>
        <p>Spelers in de lobby: {{ $playerCount }}</p>
        
        @if(session()->has('error'))
            <div>{{ session('error') }}</div>
        @endif
        
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
        
        <button wire:click="startGame" class="w-full max-w-xs bg-action-500 hover:bg-action-600 text-pure-white font-semibold text-small py-3 rounded-button text-center shadow-card transition">
            Start spel
        </button>
    </div>
</div>