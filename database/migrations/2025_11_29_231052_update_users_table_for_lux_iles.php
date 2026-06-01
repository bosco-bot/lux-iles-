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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter first_name et last_name
            $table->string('first_name', 100)->after('id');
            $table->string('last_name', 100)->after('first_name');
            
            // Ajouter les autres colonnes
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('postal_code', 20)->nullable()->after('city');
            $table->string('country', 100)->default('France')->after('postal_code');
            $table->date('birth_date')->nullable()->after('country');
            $table->string('nationality', 100)->nullable()->after('birth_date');
            $table->boolean('is_admin')->default(false)->after('nationality');
            $table->boolean('is_active')->default(true)->after('is_admin');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
        
        // Migrer les données existantes de 'name' vers 'first_name'
        // On met le nom complet dans first_name et laisse last_name vide
        \DB::statement("UPDATE users SET first_name = name, last_name = '' WHERE first_name IS NULL OR first_name = ''");
        
        // Supprimer la colonne 'name' après migration
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurer la colonne name
            $table->string('name')->after('id');
        });
        
        // Migrer les données back
        \DB::statement("UPDATE users SET name = CONCAT(first_name, ' ', last_name) WHERE name IS NULL OR name = ''");
        
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'address',
                'city',
                'postal_code',
                'country',
                'birth_date',
                'nationality',
                'is_admin',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};
