<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\BingoItem;
use App\Models\GamePlayer;
use App\Models\RouteStop;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use App\Models\LocationBingoItem;

class HostLobby extends Component
{
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
    public $locationName = null;

    // ============================================
    // LIFECYCLE SECTION
    // ============================================

    public function mount($gameId)
    {
        $this->gameId = (int) $gameId;
        $game = Game::with('location')->findOrFail($this->gameId);

        // Redirect if game is finished
        if ($game->status === 'finished') {
            $this->redirect(route('host.game', $gameId), navigate: true);
            return;
        }

        // Redirect to game if already started
        if ($game->status === 'started') {
            $this->redirect(route('host.game', $gameId), navigate: true);
            return;
        }

        $this->pin = $game->pin;
        $this->timerEnabled = (bool) $game->timer_enabled;
        $this->timerDurationMinutes = $game->timer_duration_minutes;
        $this->locationName = optional($game->location)->name ?? 'Locatie';

        $this->loadPlayers();
    }

    // ============================================
    // AUTHORIZATION SECTION
    // ============================================

    /**
     * Verify that the current session is the game host
     */
    private function verifyHostAccess(): void
    {
        $game = Game::findOrFail($this->gameId);

        if (session("hostToken_{$this->gameId}") !== $game->host_token) {
            abort(403, 'Unauthorized: Not the game host');
        }
    }

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
        
        // Redirect if game is finished
        if ($game->status === 'finished') {
            $this->redirect(route('host.game', $this->gameId), navigate: true);
            return;
        }
        
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

        $player->delete();
        $this->loadPlayers();

        session()->flash('message', 'Speler verwijderd');
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
        if (!$this->generateBingoItems($game)) {
            return;
        }

        // Generate route stops (multiple choice questions)
        $this->generateRouteStops($game);

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
     * @param Game $game The game to generate items for
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

    /**
     * Generate route stops for the game (multiple choice questions)
     *
     * @param Game $game The game to generate route stops for
     * @return void
     */
    private function generateRouteStops(Game $game): void
    {
        // Check if route stops already exist (prevent duplicates)
        if ($game->routeStops()->exists()) {
            return;
        }

        // Get location for this game
        $location = $game->location;

        if (!$location) {
            return;
        }

        // Get all location route stops for this game's location
        $locationRouteStops = $location->routeStops()->orderBy('sequence')->get();

        // Copy each template to a game instance
        foreach ($locationRouteStops as $template) {
            RouteStop::create([
                'game_id' => $game->id,
                'name' => $template->name,
                'question_text' => $template->question_text,
                'option_a' => $template->option_a,
                'option_b' => $template->option_b,
                'option_c' => $template->option_c,
                'option_d' => $template->option_d,
                'correct_option' => $template->correct_option,
                'points' => $template->points,
                'sequence' => $template->sequence,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.host-lobby');
    }
}