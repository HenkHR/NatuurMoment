<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteStop extends Model
{
    // ============================================
    // CONFIG SECTION
    // ============================================

    protected $fillable = [
        'game_id',
        'name',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'points',
        'sequence',
        'image_path',
    ];

    protected $casts = [
        'points' => 'integer',
        'sequence' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS SECTION
    // ============================================

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(RouteStopAnswer::class);
    }

    // ============================================
    // QUERY HELPERS SECTION
    // ============================================

    /**
     * Get the next unlocked question for a player (sequential unlock)
     * Returns null if all questions are answered
     */
    public static function getNextUnlocked(int $gameId, int $playerId): ?RouteStop
    {
        return static::where('game_id', $gameId)
            ->orderBy('sequence')
            ->get()
            ->first(function ($stop) use ($playerId) {
                return !$stop->isAnsweredBy($playerId);
            });
    }

    /**
     * Check if this question is unlocked for a player (previous questions answered)
     */
    public function isUnlockedFor(int $playerId): bool
    {
        // First question always unlocked
        if ($this->sequence === 1) {
            return true;
        }

        // Check if all previous questions are answered
        return !static::where('game_id', $this->game_id)
            ->where('sequence', '<', $this->sequence)
            ->whereDoesntHave('answers', function ($query) use ($playerId) {
                $query->where('game_player_id', $playerId);
            })
            ->exists();
    }

    /**
     * Check if this route stop has been answered by a specific player
     */
    public function isAnsweredBy(int $gamePlayerId): bool
    {
        return $this->answers()
            ->where('game_player_id', $gamePlayerId)
            ->exists();
    }

    /**
     * Get available options (only non-null ones)
     * Returns array like ['A' => 'Answer text A', 'B' => 'Answer text B', ...]
     */
    public function getAvailableOptions(): array
    {
        $options = [
            'A' => $this->option_a,
            'B' => $this->option_b,
        ];

        if ($this->option_c !== null) {
            $options['C'] = $this->option_c;
        }

        if ($this->option_d !== null) {
            $options['D'] = $this->option_d;
        }

        return $options;
    }
}
