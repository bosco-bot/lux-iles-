<?php

namespace App\Services;

use App\Jobs\SendPrivilegeClubTierChangeJob;
use App\Models\PrivilegeClubNotification;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrivilegeClubService
{
    public const TIER_INSIDER = 'insider';

    public const TIER_SIGNATURE = 'signature';

    public const TIER_LEGEND = 'legend';

    public const ROLLING_YEARS = 3;

    /** @var list<string> */
    public const ELIGIBLE_RESERVATION_STATUSES = [
        'confirmed',
        'deposit_paid',
        'fully_paid',
        'completed',
    ];

    /**
     * Définition des paliers pour la page club (§3.1 CDC).
     *
     * @return array<string, array<string, mixed>>
     */
    public function tierDefinitions(): array
    {
        return [
            self::TIER_INSIDER => [
                'label' => 'INSIDER',
                'emoji' => '🌴',
                'min_stays' => 1,
                'condition' => 'Dès le 1er séjour confirmé et réalisé',
                'benefits' => [
                    'Accès à la collection privilège',
                    'Conciergerie personnalisée',
                    'Offres exclusives réservées aux membres',
                ],
                'maintenance' => 'Réserver au moins 1 fois par an',
            ],
            self::TIER_SIGNATURE => [
                'label' => 'SIGNATURE',
                'emoji' => '⭐',
                'min_stays' => 3,
                'condition' => '3 séjours confirmés sur 3 ans glissants',
                'benefits' => [
                    'Early check-in / late check-out selon disponibilité',
                    'Cadeau de bienvenue premium',
                    'Priorité sur les demandes de réservation',
                ],
                'maintenance' => 'Réserver au moins 1 fois par an',
            ],
            self::TIER_LEGEND => [
                'label' => 'LEGEND',
                'emoji' => '💎',
                'min_stays' => 7,
                'condition' => '7 séjours confirmés sur 3 ans glissants',
                'benefits' => [
                    'Surclassement villa selon disponibilité',
                    'Transfert aéroport offert',
                    'Concierge dédié',
                    'Accès aux villas exclusives du catalogue',
                ],
                'maintenance' => 'Réserver au moins 1 fois par an',
            ],
        ];
    }

    public function tierLabel(?string $tier): string
    {
        if (! $tier) {
            return 'Non membre';
        }

        return $this->tierDefinitions()[$tier]['label'] ?? strtoupper($tier);
    }

    /**
     * Séjours confirmés et réalisés sur 3 ans glissants (§3.1).
     */
    public function countQualifyingStays(User $user): int
    {
        $since = Carbon::now()->subYears(self::ROLLING_YEARS)->startOfDay();

        return $user->reservations()
            ->whereIn('status', self::ELIGIBLE_RESERVATION_STATUSES)
            ->where('check_out_date', '<', Carbon::today())
            ->where('check_out_date', '>=', $since)
            ->count();
    }

    public function tierFromStayCount(int $stayCount): ?string
    {
        if ($stayCount >= 7) {
            return self::TIER_LEGEND;
        }
        if ($stayCount >= 3) {
            return self::TIER_SIGNATURE;
        }
        if ($stayCount >= 1) {
            return self::TIER_INSIDER;
        }

        return null;
    }

    public function calculateTier(User $user): ?string
    {
        return $this->tierFromStayCount($this->countQualifyingStays($user));
    }

    /**
     * Au moins une réservation confirmée sur l'année civile (hors annulations).
     */
    public function hadBookingInYear(User $user, int $year): bool
    {
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end = Carbon::create($year, 12, 31)->endOfDay();

        return $user->reservations()
            ->whereIn('status', self::ELIGIBLE_RESERVATION_STATUSES)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('check_in_date', [$start, $end])
                    ->orWhereBetween('check_out_date', [$start, $end]);
            })
            ->exists();
    }

    public function previousTier(?string $tier): ?string
    {
        return match ($tier) {
            self::TIER_LEGEND => self::TIER_SIGNATURE,
            self::TIER_SIGNATURE => self::TIER_INSIDER,
            self::TIER_INSIDER => null,
            default => null,
        };
    }

    /**
     * Recalcule et applique le palier automatique (sauf verrou admin).
     */
    public function updateTierIfChanged(User $user, bool $notify = true): bool
    {
        if ($user->is_admin) {
            return false;
        }

        if ($user->privilege_tier_manual_override) {
            return false;
        }

        $earnedTier = $this->calculateTier($user);
        $currentTier = $user->privilege_tier;

        if ($earnedTier === $currentTier) {
            return false;
        }

        return $this->applyTierChange($user, $earnedTier, $currentTier, $notify);
    }

    /**
     * Rétrogradation d'un cran (maintenance annuelle §3.1).
     */
    public function downgradeOneLevel(User $user, bool $notify = true): bool
    {
        if ($user->is_admin || $user->privilege_tier_manual_override || ! $user->privilege_tier) {
            return false;
        }

        $newTier = $this->previousTier($user->privilege_tier);

        return $this->applyTierChange($user, $newTier, $user->privilege_tier, $notify);
    }

    /**
     * Ajustement manuel admin (§3.1).
     */
    public function setManualTier(User $user, ?string $tier, bool $lock = true): void
    {
        $oldTier = $user->privilege_tier;

        $user->update([
            'privilege_tier' => $tier,
            'privilege_tier_manual_override' => $lock,
            'privilege_tier_updated_at' => now(),
        ]);

        if ($oldTier !== $tier) {
            $this->createInAppNotification($user, $oldTier, $tier, true);
            SendPrivilegeClubTierChangeJob::dispatch($user->fresh(), $oldTier, $tier);
        }
    }

    protected function applyTierChange(User $user, ?string $newTier, ?string $oldTier, bool $notify): bool
    {
        DB::transaction(function () use ($user, $newTier, $oldTier, $notify) {
            $user->update([
                'privilege_tier' => $newTier,
                'privilege_tier_updated_at' => now(),
            ]);

            if ($notify) {
                $this->createInAppNotification($user, $oldTier, $newTier, false);
                SendPrivilegeClubTierChangeJob::dispatch($user->fresh(), $oldTier, $newTier);
            }
        });

        return true;
    }

    protected function createInAppNotification(User $user, ?string $oldTier, ?string $newTier, bool $manual): void
    {
        $isUpgrade = $this->tierRank($newTier) > $this->tierRank($oldTier);

        $message = $manual
            ? sprintf(
                'Votre statut Privilege Club a été mis à jour : %s.',
                $this->tierLabel($newTier)
            )
            : ($isUpgrade
                ? sprintf('Félicitations ! Vous accédez au niveau %s du LUXÎLES PRIVILEGE CLUB.', $this->tierLabel($newTier))
                : sprintf('Votre statut Privilege Club est désormais %s.', $this->tierLabel($newTier)));

        PrivilegeClubNotification::create([
            'user_id' => $user->id,
            'type' => $isUpgrade ? PrivilegeClubNotification::TYPE_TIER_UP : PrivilegeClubNotification::TYPE_TIER_DOWN,
            'old_tier' => $oldTier,
            'new_tier' => $newTier,
            'message' => $message,
        ]);
    }

    public function tierRank(?string $tier): int
    {
        return match ($tier) {
            self::TIER_LEGEND => 3,
            self::TIER_SIGNATURE => 2,
            self::TIER_INSIDER => 1,
            default => 0,
        };
    }

    /**
     * Utilisateurs ayant terminé un séjour la veille — recalcul des paliers.
     */
    public function syncUsersAfterRecentCheckouts(): int
    {
        $userIds = Reservation::query()
            ->whereIn('status', self::ELIGIBLE_RESERVATION_STATUSES)
            ->whereDate('check_out_date', Carbon::yesterday())
            ->pluck('user_id')
            ->unique();

        $synced = 0;

        foreach (User::whereIn('id', $userIds)->where('is_admin', false)->get() as $user) {
            if ($this->updateTierIfChanged($user)) {
                $synced++;
            }
        }

        return $synced;
    }

    /**
     * Rétrogradations du 1er janvier si aucune réservation l'année précédente.
     */
    public function runAnnualMaintenance(?int $year = null): int
    {
        $checkYear = $year ?? (Carbon::now()->year - 1);
        $downgraded = 0;

        User::where('is_admin', false)
            ->whereNotNull('privilege_tier')
            ->where('privilege_tier_manual_override', false)
            ->chunkById(100, function ($users) use ($checkYear, &$downgraded) {
                foreach ($users as $user) {
                    if (! $this->hadBookingInYear($user, $checkYear) && $this->downgradeOneLevel($user)) {
                        $downgraded++;
                    }
                }
            });

        return $downgraded;
    }
}
