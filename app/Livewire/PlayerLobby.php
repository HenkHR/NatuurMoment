<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\On;

class PlayerLobby extends Component
{
    public $gameId;
    public $playerToken;
    public $pin;
    public $playerName;
    public $playerCount = 0;
    public $gameStatus = 'lobby';
    public $players = [];

    //constructor
    public function mount($gameId, $playerToken)
    {
        $this->gameId = $gameId;
        $this->playerToken = $playerToken;
        
        $player = GamePlayer::where('token', $playerToken)->firstOrFail();
        $this->playerName = $player->name;
        
        $game = Game::findOrFail($gameId);
        $this->pin = $game->pin;
        
        $this->checkGameStatus();
    }

    //refresh de status van het spel elke polling interval
    //als het spel gestart is, redirect naar de game pagina van de player
    //de players worden ook geladen in de lijst
    #[On('refresh')]
    public function checkGameStatus()
    {
        $game = Game::with('players')->findOrFail($this->gameId);
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
                'token' => $this->playerToken
            ]);
        }
    }

    public function render()
    {
        return view('livewire.player-lobby');
    }
}