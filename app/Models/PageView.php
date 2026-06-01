<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    public const SOURCE_DIRECT = 'direct';

    public const SOURCE_SEARCH = 'search';

    public const SOURCE_SOCIAL = 'social';

    public const SOURCE_REFERRAL = 'referral';

    protected $fillable = [
        'session_id',
        'visitor_hash',
        'user_id',
        'path',
        'route_name',
        'page_type',
        'villa_id',
        'island_id',
        'referrer',
        'referrer_source',
        'country_code',
        'user_agent',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }
}
