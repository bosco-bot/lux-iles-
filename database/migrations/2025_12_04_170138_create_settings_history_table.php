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
        Schema::create('settings_history', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->comment('Clé du paramètre modifié');
            $table->text('old_value')->nullable()->comment('Ancienne valeur');
            $table->text('new_value')->nullable()->comment('Nouvelle valeur');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Utilisateur qui a effectué la modification');
            $table->string('change_type', 50)->default('update')->comment('Type de modification: create, update, delete');
            $table->text('notes')->nullable()->comment('Notes additionnelles');
            $table->timestamps();

            $table->index('setting_key');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_history');
    }
};
