<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'multiplier',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'multiplier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function villaSeasonalPrices()
    {
        return $this->hasMany(VillaSeasonalPrice::class);
    }

    /**
     * CDC §3.3 : une date appartient à la saison si elle est dans [start_date, end_date]
     * (période calendaire précise, sans reconduction automatique d'une année à l'autre).
     */
    public function containsDate(\DateTimeInterface|string $date): bool
    {
        if (! $this->start_date || ! $this->end_date) {
            return false;
        }

        $date = Carbon::parse($date)->startOfDay();

        return $date->betweenIncluded(
            $this->start_date->copy()->startOfDay(),
            $this->end_date->copy()->startOfDay()
        );
    }

    /**
     * Période affichée (admin / back-office).
     */
    public function getPeriodAttribute(): string
    {
        if (! $this->start_date || ! $this->end_date) {
            return '';
        }

        return $this->start_date->format('d/m/Y').' — '.$this->end_date->format('d/m/Y');
    }

    /**
     * Libellé période côté voyageur (sans nom de saison — CDC §3.3).
     */
    public function getPeriodLabelForGuestAttribute(): string
    {
        return $this->period;
    }
}
