<div wire:poll.2s.visible="checkGameStatus" class="container mx-auto">
    <div class="flex flex-col gap-2 p-4 justify-center items-center w-full">
        <h1>Player Lobby</h1>
        <h2 class="text-center bg-forest-500 text-pure-white rounded-card p-2" >Game PIN: <span class="font-bold bg-forest-400 text-pure-white rounded-card px-2 py-1">{{ $pin }}</span></h2>
        <p>Spelers in de lobby: {{ $playerCount }}</p>
        <div class="flex flex-col gap-2 p-4 w-full">
            <h4>Spelers die meedoen:</h4>
            @if(count($players) > 0)
                <ul class="flex flex-col gap-2">
                @foreach($players as $player)
                    <li wire:key="player-{{ $player['id'] }}" class="text-left flex flex-row gap-2 items-center bg-forest-500 text-pure-white rounded-card p-2 w-full" >
                        <span>{{ $player['name'] }}</span>
                        @if($player['name'] === $playerName)
                            <span class="font-bold">(jij)</span>
                        @endif
                    </li>
                @endforeach
                </ul>
            @else
                <p>Nog geen spelers...</p>
            @endif
        </div>
        
        <div class="flex flex-col gap-2 p-4 w-full text-center">
            <p>Wachten op de host om het spel te starten...</p>
        </div>
    </div>
</div>