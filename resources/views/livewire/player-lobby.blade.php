<div wire:poll.2s="checkGameStatus">
    <div>
        <h1>Player Lobby</h1>
        <h2>Game PIN: {{ $pin }}</h2>
        <h3>Welkom, {{ $playerName }}!</h3>
        <p>Spelers in de lobby: {{ $playerCount }}</p>
        <div>
            <h4>Spelers die meedoen:</h4>
            @if(count($players) > 0)
                <ul>
                @foreach($players as $player)
                    <li wire:key="player-{{ $player['id'] }}">
                        {{ $player['name'] }}
                        @if($player['name'] === $playerName)
                            (jij)
                        @endif
                    </li>
                @endforeach
                </ul>
            @else
                <p>Nog geen spelers...</p>
            @endif
        </div>
        
        <div>
            <p>Wachten op de host om het spel te starten...</p>
            <div>Laden...</div>
        </div>
    </div>
</div>