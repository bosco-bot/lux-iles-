<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivilegeClubNotification extends Model
{
    public const TYPE_TIER_UP = 'tier_up';

    public const TYPE_TIER_DOWN = 'tier_down';

    protected $fillable = [
        'user_id',
        'type',
        'old_tier',
        'new_tier',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (! $this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}
