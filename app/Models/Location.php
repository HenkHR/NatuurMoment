<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'province',
        'duration',
    ];

    public function bingoItems(): HasMany
    {
        return $this->hasMany(LocationBingoItem::class);
    }

    public function routeStops(): HasMany
    {
        return $this->hasMany(LocationRouteStop::class)->orderBy('sequence');
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Location $location) {
            $location->bingoItems()->delete();
            $location->routeStops()->delete();
        });
    }
}
