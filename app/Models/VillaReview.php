<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillaReview extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'villa_id',
        'reservation_id',
        'user_id',
        'rating',
        'comment',
        'status',
        'admin_response',
        'moderated_by',
        'moderated_at',
        'published_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'moderated_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'Publié',
            self::STATUS_REJECTED => 'Refusé',
            default => 'En attente',
        };
    }
}
