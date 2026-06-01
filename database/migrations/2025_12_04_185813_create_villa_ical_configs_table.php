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
        Schema::create('villa_ical_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->onDelete('cascade');
            $table->enum('platform', ['airbnb', 'booking', 'vrbo', 'abritel'])->notNull();
            $table->string('ical_export_url')->nullable()->comment('URL pour exporter notre calendrier vers la plateforme');
            $table->string('ical_import_url')->nullable()->comment('URL pour importer le calendrier de la plateforme');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->enum('last_sync_status', ['success', 'error', 'pending'])->nullable();
            $table->text('last_sync_error')->nullable();
            $table->timestamps();
            
            $table->unique(['villa_id', 'platform']);
            $table->index(['platform', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_ical_configs');
    }
};
