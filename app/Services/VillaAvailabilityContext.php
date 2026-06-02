<?php

namespace App\Services;

/**
 * Paramètres de disponibilité selon le parcours (admin vs site public).
 */
final class VillaAvailabilityContext
{
    public const PUBLIC_BLOCKING_STATUSES = [
        'pending',
        'confirmed',
        'deposit_paid',
        'fully_paid',
        'completed',
    ];

    /**
     * @param  list<string>  $blockingStatuses
     */
    public function __construct(
        public readonly array $blockingStatuses,
        public readonly bool $onlyFutureReservations,
    ) {}

    public static function admin(): self
    {
        return new self(
            blockingStatuses: VillaAvailabilityService::BLOCKING_STATUSES,
            onlyFutureReservations: false,
        );
    }

    public static function publicSite(): self
    {
        return new self(
            blockingStatuses: self::PUBLIC_BLOCKING_STATUSES,
            onlyFutureReservations: true,
        );
    }
}
