<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * §3.5 CDC — équipements proposables comme filtres de recherche.
     */
    public function up(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->boolean('is_search_filter')->default(false)->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn('is_search_filter');
        });
    }
};
