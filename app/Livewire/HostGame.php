<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Photo;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HostGame extends Component
{
    #[Locked]
    public $gameId;

    public $players = [];
    public $expandedPlayers = []; // Track which player accordions are open
    public $selectedPhoto = null; // Currently selected photo for review

    //constructor 
    public function mount($gameId)
    {
        $this->gameId = $gameId;
        $game = Game::findOrFail($gameId);
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
            // Calculate score using the same method as approve/reject
            $score = $this->calculatePlayerScore($player->id);
            
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
     */
    public function togglePlayer($playerId)
    {
        if (in_array($playerId, $this->expandedPlayers)) {
            $this->expandedPlayers = array_diff($this->expandedPlayers, [$playerId]);
        } else {
            $this->expandedPlayers[] = $playerId;
        }
    }

    /**
     * Get bingo items for a player with photo status
     */
    public function getPlayerBingoItems($playerId)
    {
        // Get all bingo items for this game
        $bingoItems = DB::table('bingo_items')
            ->where('game_id', $this->gameId)
            ->orderBy('position')
            ->get();
        
        // Get photos for this player
        $photos = Photo::where('game_id', $this->gameId)
            ->where('game_player_id', $playerId)
            ->get()
            ->keyBy('bingo_item_id');
        
        // Map bingo items with photo status
        return $bingoItems->map(function($item) use ($photos) {
            $photo = $photos->get($item->id);
            return [
                'id' => $item->id,
                'label' => $item->label,
                'position' => $item->position,
                'photo' => $photo ? [
                    'id' => $photo->id,
                    'status' => $photo->status,
                    'path' => $photo->path,
                    'url' => asset('storage/' . $photo->path),
                ] : null,
            ];
        });
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
            'url' => asset('storage/' . $photo->path),
            'taken_at' => $photo->taken_at,
        ];
    }

    /**
     * Calculate and update player score based on all approved photos
     * This ensures the score is always accurate and prevents double-counting
     * Also adds bonus points for completed lines (rows, columns, diagonals)
     */
    private function calculatePlayerScore($playerId)
    {
        // Get total points from all approved photos for this player
        $baseScore = Photo::where('photos.game_id', $this->gameId)
            ->where('photos.game_player_id', $playerId)
            ->where('photos.status', 'approved')
            ->join('bingo_items', 'photos.bingo_item_id', '=', 'bingo_items.id')
            ->sum('bingo_items.points');
        
        // Get positions of approved photos
        $approvedPositions = Photo::where('photos.game_id', $this->gameId)
            ->where('photos.game_player_id', $playerId)
            ->where('photos.status', 'approved')
            ->join('bingo_items', 'photos.bingo_item_id', '=', 'bingo_items.id')
            ->pluck('bingo_items.position')
            ->toArray();
        
        // Calculate bonus points for completed lines
        $bonusPoints = $this->calculateLineBonuses($approvedPositions);
        
        $totalScore = ($baseScore ?? 0) + $bonusPoints;
        
        // Update player score
        DB::table('game_players')
            ->where('id', $playerId)
            ->update(['score' => $totalScore]);
        
        return $totalScore;
    }
    
    /**
     * Calculate bonus points for completed lines in the 3x3 bingo grid
     * Grid positions: 0 1 2
     *                 3 4 5
     *                 6 7 8
     * 
     * Lines:
     * - Horizontal: [0,1,2], [3,4,5], [6,7,8]
     * - Vertical: [0,3,6], [1,4,7], [2,5,8]
     * - Diagonal: [0,4,8], [2,4,6]
     */
    private function calculateLineBonuses(array $approvedPositions): int
    {
        $lines = [
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
        
        $bonusPoints = 0;
        
        foreach ($lines as $line) {
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
        $bingoItem = DB::table('bingo_items')
            ->where('id', $photo->bingo_item_id)
            ->first();
        
        // Close photo modal and refresh
        $this->selectedPhoto = null;
        $this->loadPlayers();
        
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
