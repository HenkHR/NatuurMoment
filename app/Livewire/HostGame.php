<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Photo;
use App\Models\BingoItem;
use App\Models\GamePlayer;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;

class HostGame extends Component
{
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

    #[Locked]
    public $gameId;

    public $players = [];
    public $expandedPlayerId = null; // Track which player accordion is open (only one at a time)
    public $selectedPhoto = null; // Currently selected photo for review
    public $playerBingoItems = []; // Cached bingo items per player [playerId => items]
    public $loadingBingoItems = false; // Loading state for bingo items

    //constructor 
    public function mount($gameId)
    {
        $this->gameId = $gameId;
        $this->loadPlayers();
    }

    //refresh de lijst van players elke polling interval
    #[On('refresh')]
    public function loadPlayers()
    {
        $game = Game::with('players')->findOrFail($this->gameId);
        
        // Get pending photo counts for each player
        $pendingCounts = Photo::where('game_id', $this->gameId)
            ->where('status', 'pending')
            ->select('game_player_id', DB::raw('count(*) as count'))
            ->groupBy('game_player_id')
            ->pluck('count', 'game_player_id')
            ->toArray();
        
        // Calculate scores for each player (includes line bonuses)
        $this->players = $game->players->map(function($player) use ($pendingCounts) {
            // Get score without updating database (read-only for display)
            $score = $this->getPlayerScore($player->id);
            
            return [
                'id' => $player->id,
                'name' => $player->name,
                'score' => $score,
                'pending_photos' => $pendingCounts[$player->id] ?? 0,
            ];
        })->toArray();
    }

    /**
     * Toggle player accordion
     * Only one player can be open at a time
     */
    public function togglePlayer($playerId)
    {
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
        if (!$photoId) {
            return; // No photo to review
        }
        
        $photo = Photo::with('gamePlayer')->findOrFail($photoId);
        
        // Verify photo belongs to this game
        if ($photo->game_id != $this->gameId) {
            abort(403, 'Unauthorized');
        }
        
        $this->selectedPhoto = [
            'id' => $photo->id,
            'player_name' => $photo->gamePlayer->name,
            'bingo_item_id' => $photo->bingo_item_id,
            'status' => $photo->status,
            'url' => $photo->url, // Use the accessor from Photo model
            'taken_at' => $photo->taken_at,
        ];
    }

    /**
     * Get player score without updating database (read-only)
     * Used for display purposes in loadPlayers()
     */
    private function getPlayerScore($playerId): int
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
        
        return (int)(($baseScore ?? 0) + $bonusPoints);
    }

    /**
     * Calculate and update player score based on all approved photos
     * This ensures the score is always accurate and prevents double-counting
     * Also adds bonus points for completed lines (rows, columns, diagonals)
     * 
     * This method should only be called when the score actually changes (approve/reject)
     */
    private function calculatePlayerScore($playerId)
    {
        $totalScore = $this->getPlayerScore($playerId);
        
        // Update player score in database using Eloquent
        GamePlayer::where('id', $playerId)
            ->update(['score' => $totalScore]);
        
        return $totalScore;
    }
    
    /**
     * Calculate bonus points for completed lines in the 3x3 bingo grid
     * 
     * Checks all possible winning lines (defined in BINGO_LINES constant)
     * and awards 1 bonus point for each completed line.
     * 
     * @param array $approvedPositions Array of grid positions (0-8) with approved photos
     * @return int Total bonus points for completed lines
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
        
        return $bonusPoints;
    }

    /**
     * Approve a photo
     */
    public function approvePhoto($photoId)
    {
        $photo = Photo::findOrFail($photoId);
        
        // Verify photo belongs to this game
        if ($photo->game_id != $this->gameId) {
            abort(403, 'Unauthorized');
        }
        
        $photo->update(['status' => 'approved']);
        
        // Recalculate player score based on all approved photos
        $newScore = $this->calculatePlayerScore($photo->game_player_id);
        
        // Get bingo item for message
        $bingoItem = BingoItem::find($photo->bingo_item_id);
        
        // Close photo modal and refresh
        $this->selectedPhoto = null;
        $this->loadPlayers();
        // Refresh bingo items if a player is expanded
        $this->refreshBingoItems();
        
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
        $photo = Photo::findOrFail($photoId);
        
        // Verify photo belongs to this game
        if ($photo->game_id != $this->gameId) {
            abort(403, 'Unauthorized');
        }
        
        $photo->update(['status' => 'rejected']);
        
        // Recalculate player score based on all approved photos
        // This will remove points if the photo was previously approved
        $this->calculatePlayerScore($photo->game_player_id);
        
        // Close photo modal and refresh
        $this->selectedPhoto = null;
        $this->loadPlayers();
        // Refresh bingo items if a player is expanded
        $this->refreshBingoItems();
        
        session()->flash('photo-message', 'Foto afgewezen.');
    }

    /**
     * Close photo modal
     */
    public function closePhotoModal()
    {
        $this->selectedPhoto = null;
    }

    public function render()
    {
        return view('livewire.host-game');
    }
}
