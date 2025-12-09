<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\BingoItem;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use App\Models\LocationBingoItem;

class HostLobby extends Component
{
    // ============================================
    // CONFIG SECTION
    // ============================================

    public const TIMER_DURATIONS = [15, 30, 45, 60, 90, 120];

    // ============================================
    // PROPERTIES SECTION
    // ============================================

    #[Locked]
    public $gameId;

    public $pin;
    public $playerCount = 0;
    public $players = [];

    public bool $timerEnabled = false;
    public ?int $timerDuration = null;

    //constructor 
    public function mount($gameId)
    {
        $this->gameId = $gameId;
        $game = Game::findOrFail($gameId);
        
        // Redirect to game if already started
        if ($game->status === 'started') {
            $this->redirect(route('host.game', $gameId), navigate: true);
            return;
        }
        
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
        $game = Game::withCount('players')->findOrFail($this->gameId);

        $freshPlayerCount = $game->players_count;

        if ($freshPlayerCount < 1) {
            session()->flash('error', 'Minstens 1 speler is nodig om het spel te starten!');
            return;
        }

        $this->generateBingoItems($game);

        // Calculate timer_ends_at if timer is enabled
        $timerEndsAt = null;
        if ($this->timerEnabled && $this->timerDuration) {
            $timerEndsAt = now()->addMinutes($this->timerDuration);
        }

        $game->update([
            'status' => 'started',
            'started_at' => now(),
            'timer_enabled' => $this->timerEnabled,
            'timer_duration_minutes' => $this->timerEnabled ? $this->timerDuration : null,
            'timer_ends_at' => $timerEndsAt,
        ]);
        
        // Redirect naar game pagina van de host
        return redirect()->route('host.game', $game->id);
    }

    private function generateBingoItems(Game $game): void
    {
        // Check if bingo items already exist (prevent duplicates)
        if (BingoItem::where('game_id', $game->id)->exists()) {
            return;
        }

        // Get all location bingo items for this game's location
        $locationBingoItems = LocationBingoItem::where('location_id', $game->location_id)
            ->get();

        if ($locationBingoItems->count() < 9) {
            session()->flash('error', 'Er zijn niet genoeg bingo items voor deze locatie (minimaal 9 nodig)');
            return;
        }

        // Randomly select 9 items
        $selectedItems = $locationBingoItems->random(9);

        // Create bingo items with random positions
        $positions = range(0, 8);
        shuffle($positions);

        foreach ($selectedItems as $index => $locationItem) {
            BingoItem::create([
                'game_id' => $game->id,
                'label' => $locationItem->label,
                'points' => $locationItem->points,
                'position' => $positions[$index],
                'icon_path' => $locationItem->icon,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.host-lobby');
    }
}