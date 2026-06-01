<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'name',
        'icon',
        'category',
        'is_search_filter',
    ];

    protected $casts = [
        'is_search_filter' => 'boolean',
    ];

    /**
     * §3.5 CDC — équipements affichés dans les filtres de recherche villas.
     */
    public function scopeSearchFilters($query)
    {
        return $query->where('is_search_filter', true);
    }

    public function villas()
    {
        return $this->belongsToMany(Villa::class, 'villa_equipments');
    }
}

