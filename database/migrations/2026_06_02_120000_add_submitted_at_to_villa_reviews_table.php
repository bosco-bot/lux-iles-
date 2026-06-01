<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * §3.4 CDC — date de dépôt distincte de la date de publication.
     */
    public function up(): void
    {
        Schema::table('villa_reviews', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('status');
        });

        DB::table('villa_reviews')
            ->whereNull('submitted_at')
            ->update(['submitted_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('villa_reviews', function (Blueprint $table) {
            $table->dropColumn('submitted_at');
        });
    }
};
