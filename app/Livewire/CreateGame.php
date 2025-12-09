<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Location;
use Livewire\Component;
use Livewire\Attributes\Locked;

class CreateGame extends Component
{
    public const TIMER_DURATIONS = [15, 30, 45, 60, 90, 120];

    #[Locked]
    public $locationId;

    public $location = null;
    public $timerDuration = null;

    public function mount($locationId)
    {
        $this->locationId = $locationId;
        $this->location = Location::findOrFail($locationId);
    }

    public function createGame()
    {
        // Validate timer duration is selected (can be 0 for no limit)
        if ($this->timerDuration === null || $this->timerDuration === '') {
            session()->flash('error', 'Selecteer een speelduur');
            return;
        }

        $duration = (int) $this->timerDuration;

        // Validate timer duration is valid (0 = no limit, or one of the predefined durations)
        if ($duration !== 0 && !in_array($duration, self::TIMER_DURATIONS)) {
            session()->flash('error', 'Ongeldige speelduur');
            return;
        }

        // Create game with timer configuration
        $timerEnabled = $duration > 0;

        $game = Game::create([
            'location_id' => $this->locationId,
            'pin' => Game::generatePin(),
            'status' => 'lobby',
            'host_token' => Game::generateHostToken(),
            'timer_enabled' => $timerEnabled,
            'timer_duration_minutes' => $timerEnabled ? $duration : null,
        ]);

        // Store host token in session
        session(['hostToken_' . $game->id => $game->host_token]);

        // Redirect to lobby
        return redirect()->route('host.lobby', $game->id);
    }

    public function render()
    {
        return view('livewire.create-game');
    }
}
