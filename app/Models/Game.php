<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function players()
    {
        return $this->hasMany(GamePlayer::class);
    }
    
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }
    
    public function location()
    {
        return $this->belongsTo(Location::class);
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
