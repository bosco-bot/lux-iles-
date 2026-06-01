<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nettoyer les réservations de test pour dieudonneekiki@gmail.com
     */
    public function up(): void
    {
        // Supprimer les paiements associés aux réservations de test
        DB::table('payments')
            ->whereIn('reservation_id', function ($query) {
                $query->select('id')
                      ->from('reservations')
                      ->where('guest_email', 'dieudonneekiki@gmail.com');
            })
            ->delete();

        // Supprimer les réservations de test
        DB::table('reservations')
            ->where('guest_email', 'dieudonneekiki@gmail.com')
            ->delete();
    }

    /**
     * Reverse the migrations.
     * Note: Cette migration ne peut pas être rollbackée car les données sont supprimées définitivement
     */
    public function down(): void
    {
        // Les données supprimées ne peuvent pas être restaurées
        // Cette migration est à sens unique pour des raisons de sécurité
    }
};
