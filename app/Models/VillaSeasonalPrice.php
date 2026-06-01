<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillaSeasonalPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'season_id',
        'price_per_night',
        'currency',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
    ];

    /**
     * Relation avec la villa
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    /**
     * Relation avec la saison
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
