<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * §3.1 CDC — LUXÎLES PRIVILEGE CLUB.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('privilege_club_tier', 20)->nullable()->after('must_set_password');
            $table->boolean('privilege_club_tier_locked')->default(false)->after('privilege_club_tier');
            $table->timestamp('privilege_club_tier_updated_at')->nullable()->after('privilege_club_tier_locked');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'privilege_club_tier',
                'privilege_club_tier_locked',
                'privilege_club_tier_updated_at',
            ]);
        });
    }
};
