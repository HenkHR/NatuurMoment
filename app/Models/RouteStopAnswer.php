<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStopAnswer extends Model
{
    // ============================================
    // CONFIG SECTION
    // ============================================

    protected $fillable = [
        'game_player_id',
        'route_stop_id',
        'chosen_option',
        'answered_at',
        // Note: is_correct and score_awarded are computed fields.
        // They're included for test compatibility, but security is enforced
        // in PlayerRouteQuestion::submitAnswer() which validates chosen_option
        // and calculates is_correct/score_awarded server-side before saving.
        'is_correct',
        'score_awarded',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score_awarded' => 'integer',
        'answered_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS SECTION
    // ============================================

    public function gamePlayer(): BelongsTo
    {
        return $this->belongsTo(GamePlayer::class);
    }

    public function routeStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class);
    }
}
