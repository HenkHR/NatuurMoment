<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;
use Livewire\Attributes\Locked;

class PlayerRouteCheck extends Component
{
    #[Locked]
    public int $gameId;

    public function mount($gameId)
    {
        $this->gameId = (int) $gameId;
    }

    public function checkGameStatus()
    {
        $game = Game::findOrFail($this->gameId);
        
        // Redirect if game is finished
        if ($game->status === 'finished') {
            return redirect()->route('player.finished-leaderboard', $this->gameId);
        }
    }

    public function render()
    {
        return view('livewire.player-route-check');
    }
}

