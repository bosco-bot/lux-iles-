<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * §3.8 CDC — suivi de fréquentation luxiles.fr.
     */
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->string('visitor_hash', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('path', 500);
            $table->string('route_name', 100)->nullable()->index();
            $table->string('page_type', 50)->nullable()->index();
            $table->foreignId('villa_id')->nullable()->constrained('villas')->nullOnDelete();
            $table->foreignId('island_id')->nullable()->constrained('islands')->nullOnDelete();
            $table->string('referrer', 500)->nullable();
            $table->string('referrer_source', 30)->default('direct')->index();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['viewed_at', 'route_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
