<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'valid_from',
        'valid_until',
        'max_uses',
        'uses_count',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * §3.2 CDC — validation d'éligibilité (saisie manuelle, un usage confirmé par client).
     */
    public function isValid(User $user): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = Carbon::today();

        if ($this->valid_from && $today->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $today->gt($this->valid_until)) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }

        $confirmedStatuses = ['pending', 'confirmed', 'deposit_paid', 'fully_paid', 'completed'];

        $alreadyUsed = PromoCodeUsage::where('promo_code_id', $this->id)
            ->where('user_id', $user->id)
            ->whereHas('reservation', function ($q) use ($confirmedStatuses) {
                $q->whereIn('status', $confirmedStatuses);
            })
            ->exists();

        return ! $alreadyUsed;
    }

    public function calculateDiscount(float $baseTotal): float
    {
        if ($baseTotal <= 0) {
            return 0.0;
        }

        if ($this->type === 'percent') {
            $discount = $baseTotal * ((float) $this->value / 100);
        } else {
            $discount = min((float) $this->value, $baseTotal);
        }

        return round(min($discount, $baseTotal), 2);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'percent' ? 'Pourcentage' : 'Montant fixe';
    }
}
