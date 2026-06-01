<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Season;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si les saisons existent déjà
        if (Season::count() > 0) {
            $this->command->info('Les saisons existent déjà. Skipping...');
            return;
        }

        $seasons = [
            [
                'name' => 'Basse saison',
                'start_month' => 1,
                'start_day' => 1,
                'end_month' => 4,
                'end_day' => 30,
                'multiplier' => 1.00,
                'is_active' => true,
            ],
            [
                'name' => 'Haute saison',
                'start_month' => 5,
                'start_day' => 1,
                'end_month' => 8,
                'end_day' => 31,
                'multiplier' => 1.50,
                'is_active' => true,
            ],
            [
                'name' => 'Moyenne saison',
                'start_month' => 9,
                'start_day' => 1,
                'end_month' => 12,
                'end_day' => 31,
                'multiplier' => 1.25,
                'is_active' => true,
            ],
        ];

        foreach ($seasons as $season) {
            Season::create($season);
        }

        $this->command->info('Saisons créées avec succès !');
    }
}
