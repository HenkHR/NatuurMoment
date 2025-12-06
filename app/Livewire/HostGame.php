<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;

class HostGame extends Component
{
    #[Locked]
    public $gameId;

    public $players = [];
    public $selectedPlayerId = null;

    //constructor 
    public function mount($gameId)
    {
        $this->gameId = $gameId;
        $game = Game::findOrFail($gameId);
        $this->loadPlayers();
    }

    //refresh de lijst van players elke polling interval
    #[On('refresh')]
    public function loadPlayers()
    {
        $game = Game::with('players')->findOrFail($this->gameId);
        $this->players = $game->players->map(function($player) {
            return [
                'id' => $player->id,
                'name' => $player->name,
                'score' => $player->score ?? 0,
            ];
        })->toArray();
    }

    //handle click op een player
    public function selectPlayer($playerId)
    {
        $this->selectedPlayerId = $playerId;
        // Hier kun je later meer functionaliteit toevoegen, zoals het tonen van de bingo kaart van de speler
    }

    public function render()
    {
        return view('livewire.host-game');
    }
}
