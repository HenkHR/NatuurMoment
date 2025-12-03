<div wire:poll.2s="loadPlayers">
    <div>
        <h1>Host Lobby</h1>
        <h2>Game PIN: {{ $pin }}</h2>
        <p>Spelers in de lobby: {{ $playerCount }}</p>
        
        @if(session()->has('error'))
            <div>{{ session('error') }}</div>
        @endif
        
        <div>
            <h3>Wachten op spelers...</h3>
            @if(count($players) > 0)
                <ul>
                    @foreach($players as $player)
                        <li>{{ $player['name'] }}</li>
                    @endforeach
                </ul>
            @else
                <p>Nog geen spelers. Deel de PIN met de spelers!</p>
            @endif
        </div>
        
        <button wire:click="startGame">
            Start spel
        </button>
    </div>
</div>