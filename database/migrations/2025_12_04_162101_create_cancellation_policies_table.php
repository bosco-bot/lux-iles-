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
        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nom de la politique (ex: Flexible, Modérée, Stricte)');
            $table->string('slug', 100)->unique()->comment('Slug unique pour la politique');
            $table->text('description')->nullable()->comment('Description de la politique');
            $table->json('refund_rules')->comment('Règles de remboursement: [{"days_before": 30, "refund_percentage": 100}, ...]');
            $table->boolean('is_default')->default(false)->comment('Politique par défaut');
            $table->boolean('is_active')->default(true)->comment('Politique active');
            $table->string('icon', 50)->nullable()->comment('Icône FontAwesome');
            $table->string('color', 20)->default('primary')->comment('Couleur du badge (primary, success, danger, etc.)');
            $table->integer('sort_order')->default(0)->comment('Ordre d\'affichage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_policies');
    }
};
