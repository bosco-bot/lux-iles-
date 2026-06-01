<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'file_path',
        'file_name',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relation avec la villa
     */
    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}




