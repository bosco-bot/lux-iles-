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
        'whatsapp_sent_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
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

    /**
     * Changements de palier nécessitant un suivi WhatsApp manuel (CDC §3.1).
     */
    public function requiresWhatsappFollowUp(): bool
    {
        return in_array($this->type, [self::TYPE_TIER_UP, self::TYPE_TIER_DOWN], true)
            && $this->new_tier !== null;
    }

    public function isWhatsappSent(): bool
    {
        return $this->whatsapp_sent_at !== null;
    }

    public function markWhatsappSent(): void
    {
        if (! $this->whatsapp_sent_at) {
            $this->update(['whatsapp_sent_at' => now()]);
        }
    }

    public function scopePendingWhatsapp($query)
    {
        return $query
            ->whereIn('type', [self::TYPE_TIER_UP, self::TYPE_TIER_DOWN])
            ->whereNotNull('new_tier')
            ->whereNull('whatsapp_sent_at');
    }
}
