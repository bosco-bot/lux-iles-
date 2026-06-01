<?php

/**
 * Script CLI — met à jour les saisons avec des dates calendaires (CDC §3.3).
 * Usage : php update_seasons.php [année]
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Season;

$year = (int) ($argv[1] ?? now()->format('Y'));

$definitions = [
    [
        'name' => 'Fêtes de fin d\'année '.$year,
        'start_date' => "{$year}-12-18",
        'end_date' => ($year + 1).'-01-02',
    ],
    [
        'name' => 'Haute saison hiver '.($year + 1),
        'start_date' => ($year + 1).'-01-03',
        'end_date' => ($year + 1).'-04-15',
    ],
    [
        'name' => 'Saison inter-saison '.$year,
        'start_date' => "{$year}-04-16",
        'end_date' => "{$year}-12-17",
    ],
];

foreach ($definitions as $data) {
    $season = Season::updateOrCreate(
        ['name' => $data['name']],
        [
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => true,
        ]
    );

    echo "Saison « {$season->name} » : {$season->period}\n";
}

echo "Terminé pour l'année de référence {$year}.\n";
