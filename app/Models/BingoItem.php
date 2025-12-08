<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BingoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'label',
        'points',
        'position',
        'icon_path',
    ];

    protected $casts = [
        'points' => 'integer',
        'position' => 'integer',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
}
