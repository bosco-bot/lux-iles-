<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_month',
        'start_day',
        'end_month',
        'end_day',
        'multiplier',
        'is_active',
    ];

    protected $casts = [
        'start_month' => 'integer',
        'start_day' => 'integer',
        'end_month' => 'integer',
        'end_day' => 'integer',
        'multiplier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les tarifs saisonniers des villas
     */
    public function villaSeasonalPrices()
    {
        return $this->hasMany(VillaSeasonalPrice::class);
    }

    /**
     * Obtenir la période formatée
     */
    public function getPeriodAttribute(): string
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        
        $start = $months[$this->start_month] . ' ' . $this->start_day;
        $end = $months[$this->end_month] . ' ' . $this->end_day;
        
        return $start . ' - ' . $end;
    }
}
