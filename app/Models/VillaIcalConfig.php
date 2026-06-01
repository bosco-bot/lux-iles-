<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillaIcalConfig extends Model
{
    protected $fillable = [
        'villa_id',
        'platform',
        'ical_export_url',
        'ical_import_url',
        'is_active',
        'last_sync_at',
        'last_sync_status',
        'last_sync_error',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    /**
     * Relation avec la villa
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    /**
     * Obtenir le nom de la plateforme formaté
     */
    public function getPlatformNameAttribute(): string
    {
        return match($this->platform) {
            'airbnb' => 'Airbnb',
            'booking' => 'Booking.com',
            'vrbo' => 'VRBO',
            'abritel' => 'Abritel',
            default => ucfirst($this->platform),
        };
    }
}
