<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PromoCodeService
{
    /**
     * §3.2 CDC — valider un code saisi manuellement par le voyageur.
     */
    public function validate(string $code, User $user): array
    {
        $promo = PromoCode::whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();

        if (! $promo) {
            return [
                'valid' => false,
                'message' => 'Ce code promotionnel est invalide.',
            ];
        }

        if (! $promo->isValid($user)) {
            return [
                'valid' => false,
                'message' => 'Ce code promotionnel n\'est pas applicable à votre compte ou a expiré.',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Code promotionnel appliqué.',
            'promo_code_id' => $promo->id,
            'promo_code' => $promo->code,
            'type' => $promo->type,
            'value' => (float) $promo->value,
        ];
    }

    public function calculateDiscount(PromoCode $promo, float $baseTotal): float
    {
        return $promo->calculateDiscount($baseTotal);
    }

    /**
     * Enregistrer l'utilisation après confirmation de réservation.
     */
    public function recordUsage(PromoCode $promo, User $user, Reservation $reservation): void
    {
        DB::transaction(function () use ($promo, $user, $reservation) {
            $promo->usages()->create([
                'user_id' => $user->id,
                'reservation_id' => $reservation->id,
                'applied_at' => now(),
            ]);

            $promo->increment('uses_count');
        });
    }
}
