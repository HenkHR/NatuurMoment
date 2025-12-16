<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class PlayerFeedback extends Component
{
    #[Locked]
    public int $gameId;

    #[Locked]
    public $playerToken;

    public $game = null;
    
    #[Validate('required|integer|min:1|max:5')]
    public ?int $rating = null;
    
    #[Validate('required|integer|min:1|max:99')]
    public ?int $age = null;

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
     * Updated age property - ensure it's an integer or null
     */
    public function updatedAge($value)
    {
        if ($value === '' || $value === null) {
            $this->age = null;
        } else {
            $this->age = (int) $value;
        }
    }

    /**
     * Get custom validation messages
     */
    protected function messages(): array
    {
        return [
            'rating.required' => 'Selecteer een beoordeling van 1 tot 5 sterren.',
            'rating.integer' => 'Beoordeling moet een getal zijn.',
            'rating.min' => 'Beoordeling moet minimaal 1 ster zijn.',
            'rating.max' => 'Beoordeling mag maximaal 5 sterren zijn.',
            'age.required' => 'Leeftijd is verplicht.',
            'age.integer' => 'Leeftijd moet een getal zijn.',
            'age.min' => 'Leeftijd moet minimaal 1 zijn.',
            'age.max' => 'Leeftijd mag maximaal 99 zijn.',
        ];
    }

    /**
     * Submit feedback and redirect to home
     */
    public function submitFeedback()
    {
        // Validate the form with custom messages
        $this->validate();

        // Get player to save feedback
        $player = GamePlayer::where('token', $this->playerToken)
            ->where('game_id', $this->gameId)
            ->first();

        if ($player) {
            $player->update([
                'feedback_rating' => $this->rating,
                'feedback_age' => $this->age,
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

