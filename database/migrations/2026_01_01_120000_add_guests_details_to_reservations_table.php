<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->tinyInteger('adults')->unsigned()->default(0)->after('number_of_guests')->comment('Nombre d\'adultes');
            $table->tinyInteger('children')->unsigned()->default(0)->after('adults')->comment('Nombre d\'enfants');
            $table->tinyInteger('infants')->unsigned()->default(0)->after('children')->comment('Nombre de bébés');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['adults', 'children', 'infants']);
        });
    }
};








