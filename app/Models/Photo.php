<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = [
        'game_id',
        'game_player_id',
        'bingo_item_id',
        'path',
        'status',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function gamePlayer()
    {
        return $this->belongsTo(GamePlayer::class); 
    }

    // public function bingoItem()
    // {
    //     return $this->belongsTo(BingoItem::class);
    // }
}