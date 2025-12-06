<div wire:poll.2s="loadPlayers" class="container mx-auto">
    <div class="flex flex-col gap-2 p-4 justify-center items-center w-full">
        <h1>Host Dashboard</h1>
        
        <div class="flex flex-col gap-2 p-4 w-full">
            <h3>Spelers in het spel</h3>
            @if(count($players) > 0)
                <ul class="flex flex-col gap-2">
                    @foreach($players as $player)
                        <li wire:key="player-{{ $player['id'] }}">
                            <button 
                                wire:click="selectPlayer({{ $player['id'] }})"
                                class="w-full text-left flex flex-row justify-between items-center bg-forest-500 text-pure-white rounded-card p-2 hover:bg-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-400 transition cursor-pointer {{ $selectedPlayerId === $player['id'] ? 'ring-2 ring-forest-400' : '' }}">
                                <span class="font-semibold">{{ $player['name'] }}</span>
                                <span class="text-sm">Score: {{ $player['score'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Geen spelers in het spel.</p>
            @endif
        </div>

    </div>
</div>
