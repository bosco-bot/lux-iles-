<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        $adminExists = User::where('email', 'admin@luxiles.com')->exists();
        
        if (!$adminExists) {
            User::create([
                'first_name' => 'Admin',
                'last_name' => 'LUXÎLES',
                'email' => 'admin@luxiles.com',
                'password' => Hash::make('Admin123!'),
                'is_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Compte administrateur créé avec succès !');
            $this->command->info('Email: admin@luxiles.com');
            $this->command->info('Mot de passe: Admin123!');
        } else {
            $this->command->warn('Le compte administrateur existe déjà.');
        }
    }
}
