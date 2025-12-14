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
        'is_correct',
        'score_awarded',
        'answered_at',
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
