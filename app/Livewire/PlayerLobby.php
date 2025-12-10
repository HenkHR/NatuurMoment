<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;

class PlayerLobby extends Component
{
    #[Locked]
    public int $gameId;

    #[Locked]
    public $playerToken;

    public $pin;
    public $playerName;
    public $playerCount = 0;
    public $gameStatus = 'lobby';
    public $players = [];
    public $locationName = 'Locatie'; 

    //constructor
    public function mount($gameId, $playerToken)
    {
        $this->gameId = (int) $gameId;
        $this->playerToken = $playerToken;
        
        //Kijk of de lokaal opgeslagen token bij de game id hoort
        $player = GamePlayer::where('token', $playerToken)
        ->where('game_id', $gameId)  
        ->firstOrFail();

        $this->playerName = $player->name;
        
        $game = Game::with('location')->findOrFail($gameId);
        
        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('player.finished-leaderboard', $gameId)->with('error', 'Het spel is al beëindigd');
        }
        
        $this->pin = $game->pin;
        
        $this->locationName = optional($game->location)->name ?? 'Locatie';
        
        $this->checkGameStatus();
    }

    //refresh de status van het spel elke polling interval
    //als het spel gestart is, redirect naar de game pagina van de player
    //de players worden ook geladen in de lijst
    #[On('refresh')]
    public function checkGameStatus()
    {
        // Check if this player still exists (may have been removed by host)
        $player = GamePlayer::where('token', $this->playerToken)
            ->where('game_id', $this->gameId)
            ->first();

        if (!$player) {
            // Player was removed by host, redirect to home
            session()->flash('message', 'Je bent verwijderd uit de lobby door de host.');
            return redirect()->route('home');
        }

        $game = Game::with('players')->findOrFail($this->gameId);
        
        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('player.finished-leaderboard', $this->gameId)->with('error', 'Het spel is al beëindigd');
        }
        
        $this->players = $game->players->map(function($player) {
            return [
                'id' => $player->id,
                'name' => $player->name,
            ];
        })->toArray();
        $this->playerCount = count($this->players);
        $this->gameStatus = $game->status;

        if ($this->gameStatus === 'started') {
            // Redirect naar game pagina van de player
            return redirect()->route('player.game', [
                'game' => $this->gameId,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.player-lobby');
    }
}