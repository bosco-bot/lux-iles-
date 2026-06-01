<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * §3.1 CDC — alignement nomenclature colonnes users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('privilege_club_tier', 'privilege_tier');
            $table->renameColumn('privilege_club_tier_locked', 'privilege_tier_manual_override');
            $table->renameColumn('privilege_club_tier_updated_at', 'privilege_tier_updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('privilege_tier', 'privilege_club_tier');
            $table->renameColumn('privilege_tier_manual_override', 'privilege_club_tier_locked');
            $table->renameColumn('privilege_tier_updated_at', 'privilege_club_tier_updated_at');
        });
    }
};
