<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSync extends Model
{
    protected $table = 'platform_syncs';

    protected $fillable = [
        'villa_id',
        'platform',
        'platform_listing_id',
        'platform_reservation_id',
        'sync_type',
        'status',
        'last_sync_at',
        'sync_data',
        'error_message',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
        'sync_data' => 'array',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
