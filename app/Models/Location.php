<?php

namespace App\Models;

use App\Constants\GameMode;
use Illuminate\Database\Eloquent\Builder;
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
        'distance',
        'url',
        'game_modes',
        'bingo_three_in_row_points',
        'bingo_full_card_points',
    ];

    protected function casts(): array
    {
        return [
            'game_modes' => 'array',
            'bingo_three_in_row_points' => 'integer',
            'bingo_full_card_points' => 'integer',
        ];
    }

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

    // ============================================================================
    // GAME MODE LOGIC
    // ============================================================================

    public function getHasBingoModeAttribute(): bool
    {
        return in_array(GameMode::BINGO, $this->game_modes ?? []);
    }

    public function getHasVragenModeAttribute(): bool
    {
        return in_array(GameMode::VRAGEN, $this->game_modes ?? []);
    }

    public function getIsBingoModeValidAttribute(): bool
    {
        return $this->has_bingo_mode &&
               ($this->bingo_items_count ?? $this->bingoItems()->count()) >= GameMode::MIN_BINGO_ITEMS;
    }

    public function getIsVragenModeValidAttribute(): bool
    {
        return $this->has_vragen_mode &&
               ($this->route_stops_count ?? $this->routeStops()->count()) >= GameMode::MIN_QUESTIONS;
    }

    public function getHasValidGameModeAttribute(): bool
    {
        // No modes enabled = not valid
        if (!$this->has_bingo_mode && !$this->has_vragen_mode) {
            return false;
        }

        // At least one enabled mode must be valid
        return $this->is_bingo_mode_valid || $this->is_vragen_mode_valid;
    }

    public function getHasIncompleteActiveModeAttribute(): bool
    {
        // Check if any enabled mode lacks sufficient content
        if ($this->has_bingo_mode && !$this->is_bingo_mode_valid) {
            return true;
        }
        if ($this->has_vragen_mode && !$this->is_vragen_mode_valid) {
            return true;
        }
        return false;
    }

    public function scopeWithValidGameModes(Builder $query): Builder
    {
        return $query->withCount(['bingoItems', 'routeStops'])
            ->where(function (Builder $q) {
                // Bingo valid: enabled AND >= MIN_BINGO_ITEMS
                $q->where(function (Builder $bingo) {
                    $bingo->whereJsonContains('game_modes', GameMode::BINGO)
                          ->whereHas('bingoItems', null, '>=', GameMode::MIN_BINGO_ITEMS);
                })
                // OR Vragen valid: enabled AND >= MIN_QUESTIONS
                ->orWhere(function (Builder $vragen) {
                    $vragen->whereJsonContains('game_modes', GameMode::VRAGEN)
                           ->whereHas('routeStops', null, '>=', GameMode::MIN_QUESTIONS);
                });
            });
    }

    protected static function booted(): void
    {
        static::deleting(function (Location $location) {
            $location->bingoItems()->delete();
            $location->routeStops()->delete();
        });
    }
}
