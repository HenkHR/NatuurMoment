<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationBingoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'label',
        'points',
        'icon',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
