<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Vérifie que les routes clés du CDC v4 sont enregistrées.
 */
class CdcRoutesSmokeTest extends TestCase
{
    #[Test]
    public function cdc_public_and_client_routes_exist(): void
    {
        $routes = [
            'home',
            'villas.index',
            'villas.show',
            'bookings.create',
            'espace-client.reservations',
            'espace-client.privilege-club',
            'espace-client.reviews.create',
        ];

        foreach ($routes as $name) {
            $this->assertTrue(
                \Illuminate\Support\Facades\Route::has($name),
                "La route {$name} doit être enregistrée."
            );
        }
    }

    #[Test]
    public function cdc_admin_routes_exist(): void
    {
        $routes = [
            'admin.dashboard',
            'admin.reservations',
            'admin.reservations.create',
            'admin.clients',
            'admin.promo-codes.index',
            'admin.equipments.index',
            'admin.villa-reviews.index',
            'admin.traffic',
        ];

        foreach ($routes as $name) {
            $this->assertTrue(
                \Illuminate\Support\Facades\Route::has($name),
                "La route {$name} doit être enregistrée."
            );
        }
    }
}
