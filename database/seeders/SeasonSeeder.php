<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        if (Season::count() > 0) {
            $this->command->info('Les saisons existent déjà. Skipping...');

            return;
        }

        $year = (int) now()->format('Y');

        $seasons = [
            [
                'name' => 'Basse saison '.$year,
                'start_date' => "{$year}-01-01",
                'end_date' => "{$year}-04-30",
                'multiplier' => 1.00,
                'is_active' => true,
            ],
            [
                'name' => 'Haute saison '.$year,
                'start_date' => "{$year}-05-01",
                'end_date' => "{$year}-08-31",
                'multiplier' => 1.50,
                'is_active' => true,
            ],
            [
                'name' => 'Moyenne saison '.$year,
                'start_date' => "{$year}-09-01",
                'end_date' => "{$year}-12-31",
                'multiplier' => 1.25,
                'is_active' => true,
            ],
        ];

        foreach ($seasons as $season) {
            Season::create($season);
        }

        $this->command->info('Saisons '.$year.' créées avec succès (dates calendaires CDC §3.3).');
    }
}
