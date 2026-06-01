<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaAvailabilityBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'start_date',
        'end_date',
        'reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relation avec la villa
     */
    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}




