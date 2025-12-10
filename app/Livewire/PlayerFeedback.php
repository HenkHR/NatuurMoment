<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\Locked;

class PlayerFeedback extends Component
{
    #[Locked]
    public int $gameId;

    #[Locked]
    public $playerToken;

    public $game = null;
    public ?int $rating = null;
    public ?string $age = null;

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
    }

    /**
     * Set rating value
     */
    public function setRating(int $value)
    {
        $this->rating = $value;
    }

    /**
     * Submit feedback and redirect to home
     */
    public function submitFeedback()
    {
        // Validate rating (1-10)
        if ($this->rating !== null && ($this->rating < 1 || $this->rating > 10)) {
            return;
        }

        // Validate age (0-120, must be numeric)
        if ($this->age !== null && $this->age !== '') {
            if (!is_numeric($this->age) || (int)$this->age < 0 || (int)$this->age > 120) {
                return;
            }
        }

        // Get player to save feedback
        $player = GamePlayer::where('token', $this->playerToken)
            ->where('game_id', $this->gameId)
            ->first();

        if ($player) {
            $player->update([
                'feedback_rating' => $this->rating,
                'feedback_age' => $this->age !== null && $this->age !== '' ? (int)$this->age : null,
            ]);
        }

        // Redirect to home
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.player-feedback');
    }
}

