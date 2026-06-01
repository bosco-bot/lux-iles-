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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nom de la saison (Haute saison, Basse saison)');
            $table->tinyInteger('start_month')->comment('Mois de début (1-12)');
            $table->tinyInteger('start_day')->comment('Jour de début (1-31)');
            $table->tinyInteger('end_month')->comment('Mois de fin (1-12)');
            $table->tinyInteger('end_day')->comment('Jour de fin (1-31)');
            $table->decimal('multiplier', 5, 2)->default(1.00)->comment('Multiplicateur de prix (ex: 1.5 pour haute saison)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
