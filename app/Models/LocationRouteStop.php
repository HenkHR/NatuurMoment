<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationRouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'name',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'points',
        'sequence',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'sequence' => 'integer',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
