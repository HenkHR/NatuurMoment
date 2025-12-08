<?php

namespace App\Models;

use App\Traits\PhotoStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use PhotoStorage;

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

    public function bingoItem()
    {
        return $this->belongsTo(BingoItem::class);
    }
    
    /**
     * Get the full URL to the photo
     * Prefers cloud storage (R2) if available, falls back to local storage
     */
    public function getUrlAttribute(): string
    {
        // First, try cloud storage (R2) if configured and file exists
        if (config("filesystems.disks.photos.driver") === 's3') {
            try {
                if (Storage::disk('photos')->exists($this->path)) {
                    $baseUrl = config("filesystems.disks.photos.url");
                    if ($baseUrl) {
                        // Construct URL directly using AWS_URL
                        $baseUrl = rtrim($baseUrl, '/');
                        $path = ltrim($this->path, '/');
                        return $baseUrl . '/' . $path;
                    }
                    return Storage::disk('photos')->url($this->path);
                }
            } catch (\Exception $e) {
                // Fall through to local storage if cloud check fails
                \Log::warning('Failed to check cloud storage', [
                    'path' => $this->path,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fall back to local storage
        if (Storage::disk('public')->exists($this->path)) {
            return asset('storage/' . $this->path);
        }
        
        // If neither exists, return local URL anyway (might be a new upload)
        return asset('storage/' . $this->path);
    }
}