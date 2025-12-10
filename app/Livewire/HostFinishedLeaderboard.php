<?php

namespace App\Livewire;

use App\Livewire\Concerns\LoadsLeaderboard;
use App\Models\Game;
use Livewire\Component;
use Livewire\Attributes\Locked;

class HostFinishedLeaderboard extends Component
{
    use LoadsLeaderboard;
    
    #[Locked]
    public int $gameId;

    public $game = null;
    public array $leaderboardData = [];

    public function mount($gameId)
    {
        $this->gameId = (int) $gameId;

        // Verify host access
        $hostToken = session('hostToken_'.$gameId);
        if (!$hostToken) {
            return redirect()->route('home')->with('error', 'Geen toegang tot het spel');
        }

        $this->game = Game::where('id', $gameId)
            ->where('host_token', $hostToken)
            ->firstOrFail();

        // If game is not finished, redirect back to game
        if ($this->game->status !== 'finished') {
            return redirect()->route('host.game', $gameId);
        }

        $this->loadLeaderboard();
    }

    /**
     * Refresh leaderboard and check game status
     */
    public function refreshLeaderboard()
    {
        // Verify host access
        $hostToken = session('hostToken_'.$this->gameId);
        if (!$hostToken) {
            return redirect()->route('home')->with('error', 'Geen toegang tot het spel');
        }

        // Refresh game data
        $this->game = Game::where('id', $this->gameId)
            ->where('host_token', $hostToken)
            ->firstOrFail();

        // If game is not finished anymore (shouldn't happen, but safety check)
        if ($this->game->status !== 'finished') {
            return redirect()->route('host.game', $this->gameId);
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
     * Navigate back to home
     */
    public function goHome()
    {
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.host-finished-leaderboard');
    }
}

