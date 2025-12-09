<?php

namespace App\Livewire;

use App\Livewire\Concerns\VerifiesHostAccess;
use App\Models\BingoItem;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\LocationBingoItem;
use App\Models\Photo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class HostLobby extends Component
{
    use VerifiesHostAccess;

    // ============================================
    // PROPERTIES SECTION
    // ============================================

    #[Locked]
    public int $gameId;

    public $pin;

    public $playerCount = 0;

    public $players = [];

    public $timerEnabled = false;

    public $timerDurationMinutes = null;

    // ============================================
    // LIFECYCLE SECTION
    // ============================================

    public function mount($gameId)
    {
        $this->gameId = (int) $gameId;
        $game = Game::findOrFail($this->gameId);

        // Redirect to game if already started
        if ($game->status === 'started') {
            $this->redirect(route('host.game', $gameId), navigate: true);

            return;
        }

        $this->pin = $game->pin;
        $this->timerEnabled = (bool) $game->timer_enabled;
        $this->timerDurationMinutes = $game->timer_duration_minutes;
        $this->loadPlayers();
    }

    // ============================================
    // AUTHORIZATION SECTION
    // ============================================

    // verifyHostAccess() is provided by VerifiesHostAccess trait

    // ============================================
    // DATA LOADING SECTION
    // ============================================

    /**
     * Refresh players list (called by polling)
     */
    #[On('refresh')]
    public function loadPlayers()
    {
        $game = Game::with('players')->findOrFail($this->gameId);
        $this->players = $game->players->map(function ($player) {
            return [
                'id' => $player->id,
                'name' => $player->name,
            ];
        })->toArray();
        $this->playerCount = count($this->players);
    }

    // ============================================
    // PLAYER MANAGEMENT SECTION
    // ============================================

    /**
     * Remove a player from the lobby
     */
    public function removePlayer($playerId)
    {
        $this->verifyHostAccess();

        $player = GamePlayer::findOrFail($playerId);

        // Verify player belongs to this game
        if ($player->game_id !== $this->gameId) {
            abort(403, 'Unauthorized: Player does not belong to this game');
        }

        try {
            DB::transaction(function () use ($player) {
                // Delete associated photos first (cascade)
                Photo::where('game_player_id', $player->id)->delete();
                $player->delete();
            });

            $this->loadPlayers();
            session()->flash('message', 'Speler verwijderd');
        } catch (\Exception $e) {
            Log::error('Failed to remove player', [
                'game_id' => $this->gameId,
                'player_id' => $playerId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Fout bij verwijderen speler. Probeer opnieuw.');
        }
    }

    // ============================================
    // GAME START SECTION
    // ============================================

    /**
     * Start the game (requires at least 1 player, timer already configured at game creation)
     */
    public function startGame()
    {
        $this->verifyHostAccess();

        $game = Game::withCount('players')->findOrFail($this->gameId);

        $freshPlayerCount = $game->players_count;

        if ($freshPlayerCount < 1) {
            session()->flash('error', 'Minstens 1 speler is nodig om het spel te starten!');

            return;
        }

        // Generate bingo items - halt if it fails
        if (! $this->generateBingoItems($game)) {
            return;
        }

        // Calculate timer_ends_at only if timer is enabled
        $updateData = [
            'status' => 'started',
            'started_at' => now(),
        ];

        if ($game->timer_enabled && $game->timer_duration_minutes) {
            $updateData['timer_ends_at'] = now()->addMinutes($game->timer_duration_minutes);
        }

        $game->update($updateData);

        // Redirect naar game pagina van de host
        return redirect()->route('host.game', $game->id);
    }

    /**
     * Generate bingo items for the game
     *
     * @param  Game  $game  The game to generate items for
     * @return bool True if successful, false if failed
     */
    private function generateBingoItems(Game $game): bool
    {
        // Check if bingo items already exist (prevent duplicates)
        if (BingoItem::where('game_id', $game->id)->exists()) {
            return true; // Already exists, not an error
        }

        // Get all location bingo items for this game's location
        $locationBingoItems = LocationBingoItem::where('location_id', $game->location_id)
            ->get();

        if ($locationBingoItems->count() < 9) {
            session()->flash('error', 'Er zijn niet genoeg bingo items voor deze locatie (minimaal 9 nodig)');

            return false;
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

        return true;
    }

    public function render()
    {
        return view('livewire.host-lobby');
    }
}
