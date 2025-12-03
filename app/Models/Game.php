<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'pin',
        'status',
        'host_token',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function players()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public static function generatePin()
    {
        do {
            $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('pin', $pin)->exists());
        
        return $pin;
    }

    public static function generateHostToken()
    {
        return Str::random(100);
    }
}