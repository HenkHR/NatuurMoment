<?php

namespace App\Livewire;

use App\Livewire\Concerns\LoadsLeaderboard;
use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\Locked;

class PlayerFinishedLeaderboard extends Component
{
    use LoadsLeaderboard;
    
    #[Locked]
    public int $gameId;

    #[Locked]
    public $playerToken;

    public $game = null;
    public array $leaderboardData = [];

    public function mount($gameId, $playerToken)
    {
        $this->gameId = (int) $gameId;
        $this->playerToken = $playerToken;

        // Validate player access
        $player = GamePlayer::where('token', $playerToken)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $this->game = Game::findOrFail($gameId);

        // If game is not finished, redirect back to game
        if ($this->game->status !== 'finished') {
            return redirect()->route('player.game', $gameId);
        }

        $this->loadLeaderboard();
    }

    /**
     * Refresh leaderboard and check game status
     */
    public function refreshLeaderboard()
    {
        // Refresh game data
        $this->game = Game::findOrFail($this->gameId);

        // If game is not finished anymore (shouldn't happen, but safety check)
        if ($this->game->status !== 'finished') {
            return redirect()->route('player.game', $this->gameId);
        }

        $this->loadLeaderboard();
    }

    /**
     * Load leaderboard data sorted by score
     */
    private function loadLeaderboard()
    {
        $this->leaderboardData = $this->loadLeaderboardData($this->gameId);
    }

    /**
     * Navigate to feedback form
     */
    public function showFeedbackForm()
    {
        return redirect()->route('player.feedback', [
            'game' => $this->gameId
        ]);
    }

    public function render()
    {
        return view('livewire.player-finished-leaderboard');
    }
}

