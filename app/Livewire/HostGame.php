<?php

namespace App\Livewire;

use App\Livewire\Concerns\LoadsLeaderboard;
use App\Models\Game;
use App\Models\Photo;
use App\Models\BingoItem;
use App\Models\GamePlayer;
use App\Models\RouteStopAnswer;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HostGame extends Component
{
    use LoadsLeaderboard;
    /**
     * Bingo grid lines for bonus point calculation
     * 
     * Defines all possible winning lines in a 3x3 bingo grid.
     * Grid positions are numbered as follows:
     *   0 1 2
     *   3 4 5
     *   6 7 8
     * 
     * Contains:
     * - 3 horizontal rows: [0,1,2], [3,4,5], [6,7,8]
     * - 3 vertical columns: [0,3,6], [1,4,7], [2,5,8]
     * - 2 diagonals: [0,4,8], [2,4,6]
     */
    private const BINGO_LINES = [
        // Horizontal rows
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        // Vertical columns
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        // Diagonals
        [0, 4, 8],
        [2, 4, 6],
    ];

    // ============================================
    // CONFIG SECTION
    // ============================================

    private const BINGO_ITEM_COUNT = 9;
    private const FULL_CARD_BONUS = 5;

    // ============================================
    // PROPERTIES SECTION
    // ============================================

    #[Locked]
    public int $gameId;

    public $players = [];
    public $expandedPlayerId = null;
    public $selectedPhoto = null;
    public $playerBingoItems = [];
    public $loadingBingoItems = false;

    // Timer & End Game properties
    public $game = null;
    public ?int $timeRemaining = null;
    public bool $showEndGameModal = false;

    // ============================================
    // LIFECYCLE SECTION
    // ============================================

    public function mount($gameId)
    {
        $this->gameId = (int) $gameId;
        $this->loadGame();
        $this->loadPlayers();
    }

    // ============================================
    // AUTHORIZATION SECTION
    // ============================================

    /**
     * Verify that the current session is the game host
     * Called before any host-only actions
     */
    private function verifyHostAccess(): void
    {
        if (!$this->game) {
            $this->game = Game::findOrFail($this->gameId);
        }

        // Session key uses 'hostToken_' (set in GameController::store)
        if (session("hostToken_{$this->gameId}") !== $this->game->host_token) {
            abort(403, 'Unauthorized: Not the game host');
        }
    }

    /**
     * Load game data including timer info
     */
    private function loadGame()
    {
        $this->game = Game::findOrFail($this->gameId);

        // If game is finished, redirect to finished leaderboard
        if ($this->game->status === 'finished') {
            return redirect()->route('host.finished-leaderboard', $this->gameId);
        }
    }

    // ============================================
    // DATA LOADING SECTION
    // ============================================

    /**
     * Refresh players and check auto-end conditions
     */
    #[On('refresh')]
    public function loadPlayers()
    {
        // Refresh game data
        $this->game = Game::with('players')->findOrFail($this->gameId);

        // If already finished, redirect to finished leaderboard
        if ($this->game->status === 'finished') {
            return redirect()->route('host.finished-leaderboard', $this->gameId);
        }

        // Check auto-end conditions
        if ($this->checkAutoEnd()) {
            return;
        }

        // Update time remaining for server sync
        if ($this->game->timer_enabled && $this->game->timer_ends_at) {
            $this->timeRemaining = max(0, now()->diffInSeconds($this->game->timer_ends_at, false));
        }

        // Get pending photo counts for each player
        $pendingCounts = Photo::where('game_id', $this->gameId)
            ->where('status', 'pending')
            ->select('game_player_id', DB::raw('count(*) as count'))
            ->groupBy('game_player_id')
            ->pluck('count', 'game_player_id')
            ->toArray();

        // Prefetch all approved photos with bingo item data for all players in one query
        $allApprovedPhotos = Photo::where('photos.game_id', $this->gameId)
            ->where('photos.status', 'approved')
            ->join('bingo_items', 'photos.bingo_item_id', '=', 'bingo_items.id')
            ->select('photos.game_player_id', 'bingo_items.points', 'bingo_items.position')
            ->get()
            ->groupBy('game_player_id');

        // Pre-calculate scores for all players
        $playerScores = [];
        $playersCompleted = [];
        foreach ($allApprovedPhotos as $playerId => $photos) {
            $baseScore = $photos->sum('points');
            $approvedPositions = $photos->pluck('position')->toArray();
            $bonusPoints = $this->calculateLineBonuses($approvedPositions);
            $playerScores[$playerId] = (int)(($baseScore ?? 0) + $bonusPoints);
            $playersCompleted[$playerId] = count($approvedPositions) >= self::BINGO_ITEM_COUNT;
        }

        // REQ-013: Get route question counts for progress display
        $totalRouteQuestions = $this->game->routeStops()->count();
        $routeAnswerCounts = [];
        $routeAnswerScores = [];
        if ($totalRouteQuestions > 0) {
            $routeAnswerCounts = RouteStopAnswer::whereIn('game_player_id', $this->game->players->pluck('id'))
                ->select('game_player_id', DB::raw('count(*) as count'))
                ->groupBy('game_player_id')
                ->pluck('count', 'game_player_id')
                ->toArray();
            
            // Get route stop answer scores for all players
            $routeAnswerScores = RouteStopAnswer::whereIn('game_player_id', $this->game->players->pluck('id'))
                ->select('game_player_id', DB::raw('sum(score_awarded) as total_score'))
                ->groupBy('game_player_id')
                ->pluck('total_score', 'game_player_id')
                ->toArray();
        }

        // Calculate scores for each player (includes line bonuses and route stop answers)
        $this->players = $this->game->players->map(function($player) use ($pendingCounts, $playerScores, $playersCompleted, $totalRouteQuestions, $routeAnswerCounts, $routeAnswerScores) {
            $photoScore = $playerScores[$player->id] ?? 0;
            $routeScore = (int)($routeAnswerScores[$player->id] ?? 0);
            $score = $photoScore + $routeScore;
            $answeredQuestions = $routeAnswerCounts[$player->id] ?? 0;

            return [
                'id' => $player->id,
                'name' => $player->name,
                'score' => $score,
                'pending_photos' => $pendingCounts[$player->id] ?? 0,
                'completed' => $playersCompleted[$player->id] ?? false,
                // REQ-013: Route question progress
                'route_questions_total' => $totalRouteQuestions,
                'route_questions_answered' => $answeredQuestions,
                'route_questions_percentage' => $totalRouteQuestions > 0
                    ? round(($answeredQuestions / $totalRouteQuestions) * 100)
                    : null,
            ];
        })->toArray();
    }

    /**
     * Check if game should auto-end (timer expired or all players done)
     */
    private function checkAutoEnd(): bool
    {
        // Check timer expiry
        if ($this->game->timer_enabled
            && $this->game->timer_ends_at
            && now()->isAfter($this->game->timer_ends_at)
        ) {
            $this->endGame();
            return true;
        }

        // Early return: no players means nothing to check
        $totalPlayers = $this->game->players->count();
        if ($totalPlayers === 0) {
            return false;
        }

        // Count players who completed bingo (9 approved photos)
        $bingoCompletedPlayers = Photo::where('game_id', $this->gameId)
            ->where('status', 'approved')
            ->select('game_player_id', DB::raw('count(*) as count'))
            ->groupBy('game_player_id')
            ->havingRaw('count(*) >= ?', [self::BINGO_ITEM_COUNT])
            ->pluck('game_player_id')
            ->toArray();

        $hasRouteQuestions = $this->game->routeStops()->exists();

        // If no route questions, just check bingo completion
        if (!$hasRouteQuestions) {
            if (count($bingoCompletedPlayers) >= $totalPlayers) {
                $this->endGame();
                return true;
            }
            return false;
        }

        // REQ-012: Check if all players completed BOTH bingo AND questions
        $totalRouteQuestions = $this->game->routeStops()->count();

        // Batch query: count answers per player who completed bingo (prevents N+1)
        $fullyCompletedCount = RouteStopAnswer::whereIn('game_player_id', $bingoCompletedPlayers)
            ->select('game_player_id', DB::raw('count(*) as count'))
            ->groupBy('game_player_id')
            ->havingRaw('count(*) >= ?', [$totalRouteQuestions])
            ->count();

        if ($fullyCompletedCount >= $totalPlayers) {
            $this->endGame();
            return true;
        }

        return false;
    }

    /**
     * Open first pending photo for a player when clicking the player
     * If no pending photos, do nothing
     * Also collapses any expanded bingo cards
     */
    public function togglePlayer($playerId)
    {
        $this->verifyHostAccess();

        // Collapse any expanded bingo cards
        $this->expandedPlayerId = null;
        $this->playerBingoItems = [];

        // Get the first pending photo for this player
        $firstPendingPhoto = $this->getFirstPendingPhoto($playerId);
        
        if ($firstPendingPhoto) {
            $this->selectPhoto($firstPendingPhoto->id);
        }
    }

    /**
     * Toggle bingo card for a player
     * Only one player can be open at a time
     */
    public function toggleBingoCard($playerId)
    {
        $this->verifyHostAccess();

        // If clicking the same player, close it
        if ($this->expandedPlayerId === $playerId) {
            $this->expandedPlayerId = null;
            $this->playerBingoItems = [];
        } else {
            // Open new player and close any other
            $this->expandedPlayerId = $playerId;
            $this->loadPlayerBingoItems($playerId);
        }
    }

    /**
     * Get the first pending photo for a player
     */
    private function getFirstPendingPhoto($playerId)
    {
        return Photo::where('game_id', $this->gameId)
            ->where('game_player_id', $playerId)
            ->where('status', 'pending')
            ->orderBy('taken_at', 'asc')
            ->first();
    }

    /**
     * Load bingo items for a specific player (with prefetching to avoid N+1)
     */
    public function loadPlayerBingoItems($playerId)
    {
        // If already loaded, don't reload
        if (isset($this->playerBingoItems[$playerId])) {
            return;
        }

        $this->loadingBingoItems = true;

        try {
            // Prefetch all bingo items for this game (only once)
            $bingoItems = BingoItem::where('game_id', $this->gameId)
                ->orderBy('position')
                ->get()
                ->keyBy('id');

            // Prefetch all photos for this player in one query
            $photos = Photo::where('game_id', $this->gameId)
                ->where('game_player_id', $playerId)
                ->get()
                ->keyBy('bingo_item_id');

            // Map bingo items with photo status
            $this->playerBingoItems[$playerId] = $bingoItems->map(function($item) use ($photos) {
                $photo = $photos->get($item->id);
                return [
                    'id' => $item->id,
                    'label' => $item->label,
                    'position' => $item->position,
                    'photo' => $photo ? [
                        'id' => $photo->id,
                        'status' => $photo->status,
                        'path' => $photo->path,
                        'url' => $photo->url,
                    ] : null,
                ];
            })->values()->toArray();
        } finally {
            $this->loadingBingoItems = false;
        }
    }

    /**
     * Refresh bingo items for the currently expanded player
     */
    public function refreshBingoItems()
    {
        if ($this->expandedPlayerId) {
            // Clear cache and reload
            unset($this->playerBingoItems[$this->expandedPlayerId]);
            $this->loadPlayerBingoItems($this->expandedPlayerId);
        }
    }

    /**
     * Get bingo items for a player with photo status (computed property)
     * Uses cached data if available
     */
    public function getPlayerBingoItems($playerId)
    {
        // If not cached, load it
        if (!isset($this->playerBingoItems[$playerId])) {
            $this->loadPlayerBingoItems($playerId);
        }

        return collect($this->playerBingoItems[$playerId] ?? []);
    }

    /**
     * Select photo for review
     */
    public function selectPhoto($photoId)
    {
        $this->verifyHostAccess();

        if (!$photoId) {
            return; // No photo to review
        }

        $photo = Photo::with(['gamePlayer', 'bingoItem'])->findOrFail($photoId);

        // Verify photo belongs to this game
        if ($photo->game_id !== $this->gameId) {
            abort(403, 'Unauthorized');
        }
        
        // Get pending photo count for this player
        $pendingCount = Photo::where('game_id', $this->gameId)
            ->where('game_player_id', $photo->game_player_id)
            ->where('status', 'pending')
            ->count();
        
        $this->selectedPhoto = [
            'id' => $photo->id,
            'player_name' => $photo->gamePlayer->name,
            'bingo_item_id' => $photo->bingo_item_id,
            'bingo_item_label' => $photo->bingoItem->label ?? '',
            'status' => $photo->status,
            'url' => $photo->url, // Use the accessor from Photo model
            'taken_at' => $photo->taken_at,
            'pending_count' => $pendingCount,
        ];
    }

    /**
     * Calculate player total score (bingo points + line bonuses + route stop answers)
     *
     * @param int $playerId Player ID to calculate score for
     * @param bool $persist If true, also updates the database
     * @return int Calculated total score
     */
    private function calculatePlayerScore(int $playerId, bool $persist = false): int
    {
        // Get all approved photos with their bingo item points and positions in one query
        $approvedPhotos = Photo::where('photos.game_id', $this->gameId)
            ->where('photos.game_player_id', $playerId)
            ->where('photos.status', 'approved')
            ->join('bingo_items', 'photos.bingo_item_id', '=', 'bingo_items.id')
            ->select('bingo_items.points', 'bingo_items.position')
            ->get();

        // Calculate base score from points
        $baseScore = $approvedPhotos->sum('points');

        // Get positions for line bonus calculation
        $approvedPositions = $approvedPhotos->pluck('position')->toArray();

        // Calculate bonus points for completed lines
        $bonusPoints = $this->calculateLineBonuses($approvedPositions);

        // Calculate score from route stop answers
        $routeScore = RouteStopAnswer::where('game_player_id', $playerId)
            ->sum('score_awarded') ?? 0;

        $totalScore = (int)(($baseScore ?? 0) + $bonusPoints + $routeScore);

        // Optionally persist to database
        if ($persist) {
            GamePlayer::where('id', $playerId)->update(['score' => $totalScore]);
        }

        return $totalScore;
    }
    
    /**
     * Calculate bonus points for completed lines in the 3x3 bingo grid
     *
     * Checks all possible winning lines (defined in BINGO_LINES constant)
     * and awards 1 bonus point for each completed line.
     * Also awards FULL_CARD_BONUS points when all 9 positions are filled.
     *
     * @param array $approvedPositions Array of grid positions (0-8) with approved photos
     * @return int Total bonus points for completed lines and full card
     */
    private function calculateLineBonuses(array $approvedPositions): int
    {
        $bonusPoints = 0;

        foreach (self::BINGO_LINES as $line) {
            // Check if all positions in this line are approved
            $lineCompleted = true;
            foreach ($line as $position) {
                if (!in_array($position, $approvedPositions)) {
                    $lineCompleted = false;
                    break;
                }
            }

            if ($lineCompleted) {
                $bonusPoints += 1;
            }
        }

        // Full card bonus: award extra points when all 9 positions are filled
        if (count(array_unique($approvedPositions)) >= self::BINGO_ITEM_COUNT) {
            $bonusPoints += self::FULL_CARD_BONUS;
        }

        return $bonusPoints;
    }

    /**
     * Approve a photo
     */
    public function approvePhoto($photoId)
    {
        $this->verifyHostAccess();

        $photo = Photo::with(['gamePlayer', 'bingoItem'])->findOrFail($photoId);

        // Verify photo belongs to this game
        if ($photo->game_id !== $this->gameId) {
            abort(403, 'Unauthorized');
        }
        
        $playerId = $photo->game_player_id;
        $photo->update(['status' => 'approved']);
        
        // Recalculate player score based on all approved photos
        $newScore = $this->calculatePlayerScore($playerId, persist: true);
        
        // Get bingo item for message
        $bingoItem = $photo->bingoItem;
        
        // Refresh players and bingo items
        $this->loadPlayers();
        $this->refreshBingoItems();
        
        // Auto-open next pending photo for the same player
        $nextPendingPhoto = $this->getFirstPendingPhoto($playerId);
        if ($nextPendingPhoto) {
            $this->selectPhoto($nextPendingPhoto->id);
        } else {
            // No more pending photos, close modal
            $this->selectedPhoto = null;
        }
        
        $message = 'Foto goedgekeurd!';
        if ($bingoItem && $bingoItem->points > 0) {
            $message .= " ({$bingoItem->points} punt(en))";
        }
        session()->flash('photo-message', $message);
    }

    /**
     * Reject a photo
     */
    public function rejectPhoto($photoId)
    {
        $this->verifyHostAccess();

        $photo = Photo::with(['gamePlayer', 'bingoItem'])->findOrFail($photoId);

        // Verify photo belongs to this game
        if ($photo->game_id !== $this->gameId) {
            abort(403, 'Unauthorized');
        }
        
        $playerId = $photo->game_player_id;
        $photo->update(['status' => 'rejected']);
        
        // Recalculate player score based on all approved photos
        // This will remove points if the photo was previously approved
        $this->calculatePlayerScore($playerId, persist: true);
        
        // Refresh players and bingo items
        $this->loadPlayers();
        $this->refreshBingoItems();
        
        // Auto-open next pending photo for the same player
        $nextPendingPhoto = $this->getFirstPendingPhoto($playerId);
        if ($nextPendingPhoto) {
            $this->selectPhoto($nextPendingPhoto->id);
        } else {
            // No more pending photos, close modal
            $this->selectedPhoto = null;
        }
        
        session()->flash('photo-message', 'Foto afgewezen.');
    }

    /**
     * Close photo modal
     */
    public function closePhotoModal()
    {
        $this->selectedPhoto = null;
    }

    // ============================================
    // END GAME SECTION
    // ============================================

    /**
     * Show confirmation modal for ending game
     */
    public function confirmEndGame()
    {
        $this->verifyHostAccess();
        $this->showEndGameModal = true;
    }

    /**
     * Cancel end game (close modal)
     */
    public function cancelEndGame()
    {
        $this->showEndGameModal = false;
    }

    /**
     * End the game and show leaderboard
     */
    public function endGame()
    {
        try {
            // Use transaction for safety
            DB::transaction(function () {
                $game = Game::lockForUpdate()->findOrFail($this->gameId);

                // Prevent double-ending
                if ($game->status === 'finished') {
                    return;
                }

                // Update all player scores one final time
                foreach ($game->players as $player) {
                    $this->calculatePlayerScore($player->id, persist: true);
                }

                // Mark game as finished
                $game->update([
                    'status' => 'finished',
                    'finished_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to end game', [
                'game_id' => $this->gameId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Fout bij het afronden van het spel. Probeer opnieuw.');
            $this->showEndGameModal = false;
            return;
        }

        // Redirect to finished leaderboard
        $this->showEndGameModal = false;
        $this->game = Game::findOrFail($this->gameId);
        return redirect()->route('host.finished-leaderboard', $this->gameId);
    }

    // ============================================
    // RENDER SECTION
    // ============================================

    public function render()
    {
        return view('livewire.host-game');
    }
}
