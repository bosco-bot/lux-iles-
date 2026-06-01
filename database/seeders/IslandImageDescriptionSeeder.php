<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Island;

class IslandImageDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Guadeloupe
        $guadeloupe = Island::where('name', 'Guadeloupe')->first();
        if ($guadeloupe) {
            $guadeloupe->update([
                'image_url' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?q=80&w=2070&auto=format&fit=crop',
                'description' => 'Nature luxuriante et plages de rêve. Authenticité créole et raffinement.',
            ]);
        }

        // Les Saintes
        $lesSaintes = Island::where('name', 'Les Saintes')->first();
        if ($lesSaintes) {
            $lesSaintes->update([
                'image_url' => 'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?q=80&w=2070&auto=format&fit=crop',
                'description' => 'Découvrez cette destination exceptionnelle des Caraïbes.',
            ]);
        }

        // Martinique
        $martinique = Island::where('name', 'Martinique')->first();
        if ($martinique) {
            $martinique->update([
                'image_url' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=2070&auto=format&fit=crop',
                'description' => 'L\'île aux fleurs. Volcan majestueux, art de vivre et gastronomie raffinée.',
            ]);
        }
    }
}
