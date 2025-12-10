<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\Locked;

class PlayerLeaderboard extends Component
{
    #[Locked]
    public $gameId;
    
    #[Locked]
    public $playerToken;
    
    public $game;
    public array $leaderboardData = [];

    public function mount($gameId, $playerToken)
    {
        $this->gameId = $gameId;
        $this->playerToken = $playerToken;
        
        // Validate player access
        $player = GamePlayer::where('token', $playerToken)
            ->where('game_id', $gameId)
            ->firstOrFail();
        
        $this->game = Game::findOrFail($gameId);
        
        if ($this->game->status !== 'started') {
            $this->redirect(route('player.lobby', $gameId), navigate: true);
            return;
        }
        
        $this->loadLeaderboard();
    }

    public function refreshLeaderboard()
    {
        $this->loadLeaderboard();
    }

    private function loadLeaderboard()
    {
        $this->leaderboardData = GamePlayer::where('game_id', $this->gameId)
            ->orderByDesc('score')
            ->orderBy('name')
            ->get()
            ->map(fn($player, $index) => [
                'rank' => $index + 1,
                'id' => $player->id,
                'name' => $player->name,
                'score' => $player->score ?? 0,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.player-leaderboard');
    }
}
