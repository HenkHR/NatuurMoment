<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;
use Livewire\Attributes\On;

class HostLobby extends Component
{
    public $gameId;
    public $pin;
    public $playerCount = 0;
    public $players = [];

    //constructor 
    public function mount($gameId)
    {
        $this->gameId = $gameId;
        $game = Game::findOrFail($gameId);
        $this->pin = $game->pin;
        $this->loadPlayers();
    }

    //refresh de lijst van players in de lobby elke polling interval
    #[On('refresh')]
    public function loadPlayers()
    {
        $game = Game::with('players')->findOrFail($this->gameId);
        $this->players = $game->players->map(function($player) {
            return [
                'id' => $player->id,
                'name' => $player->name,
            ];
        })->toArray();
        $this->playerCount = count($this->players);
    }

    //start het spel als er minstens 1 player in de lobby is, spel functionaliteit komt nog
    public function startGame()
    {
        $game = Game::findOrFail($this->gameId);
        
        if ($this->playerCount < 1) {
            session()->flash('error', 'Need at least 1 player to start!');
            return;
        }
        
        $game->update([
            'status' => 'started',
            'started_at' => now(),
        ]);
        
        // Redirect naar game pagina van de host
        return redirect()->route('host.game', $game->id);
    }

    public function render()
    {
        return view('livewire.host-lobby');
    }
}