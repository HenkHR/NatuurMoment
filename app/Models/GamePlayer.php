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

    public static function generateToken()
    {
        return Str::random(100);
    }
}