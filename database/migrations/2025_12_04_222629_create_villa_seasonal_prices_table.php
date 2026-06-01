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
        Schema::create('villa_seasonal_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->onDelete('cascade');
            $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
            $table->decimal('price_per_night', 10, 2)->comment('Prix par nuit pour cette saison');
            $table->string('currency', 3)->default('EUR');
            $table->timestamps();
            
            $table->unique(['villa_id', 'season_id'], 'unique_villa_season');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_seasonal_prices');
    }
};
