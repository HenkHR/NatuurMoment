<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GamePlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'token',
        'score',
        'feedback_rating',
        'feedback_age',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function routeStopAnswers()
    {
        return $this->hasMany(RouteStopAnswer::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    // ============================================
    // COMPLETION CHECK METHODS
    // ============================================

    /**
     * Check if player has completed all bingo items (9 approved photos)
     * REQ-012: Used for completion redirect logic
     */
    public function hasCompletedBingo(): bool
    {
        return Photo::where('game_player_id', $this->id)
            ->where('status', 'approved')
            ->count() >= 9;
    }

    /**
     * Check if player has answered all route questions for their game
     * REQ-011: Used for redirect to bingo after questions done
     */
    public function hasCompletedQuestions(): bool
    {
        $game = $this->game;
        $totalQuestions = $game->routeStops()->count();

        // If no questions exist, consider "not completed" (nothing to complete)
        if ($totalQuestions === 0) {
            return false;
        }

        $answeredCount = $this->routeStopAnswers()->count();

        return $answeredCount >= $totalQuestions;
    }

    /**
     * Check if player has completed everything (bingo + questions)
     * REQ-012: Used for redirect to leaderboard
     */
    public function hasCompletedAll(): bool
    {
        $game = $this->game;
        $hasQuestions = $game->routeStops()->exists();

        // If no questions exist, only check bingo
        if (!$hasQuestions) {
            return $this->hasCompletedBingo();
        }

        // Both must be complete
        return $this->hasCompletedBingo() && $this->hasCompletedQuestions();
    }

    /**
     * Generate a cryptographically secure player token
     *
     * Uses random_bytes() which is CSPRNG (Cryptographically Secure Pseudo-Random Number Generator)
     * instead of Str::random() which may not be cryptographically secure.
     *
     * @return string 100 character hex token
     */
    public static function generateToken(): string
    {
        // Use cryptographically secure random bytes (50 bytes = 100 hex characters)
        return bin2hex(random_bytes(50));
    }
}