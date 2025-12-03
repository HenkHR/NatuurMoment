<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function bingoItems()
    {
        return $this->hasMany(LocationBingoItem::class);
    }

    public function routeStops()
    {
        return $this->hasMany(LocationRouteStop::class);
    }
}
