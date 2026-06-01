<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Season;

$seasonsToUpdate = [
    [
        'name' => 'Haute Saison',
        'start_month' => 12,
        'start_day' => 18,
        'end_month' => 1,
        'end_day' => 2,
    ],
    [
        'name' => 'Moyenne Saison',
        'start_month' => 1,
        'start_day' => 3,
        'end_month' => 4,
        'end_day' => 15,
    ],
    [
        'name' => 'Basse Saison',
        'start_month' => 4,
        'start_day' => 16,
        'end_month' => 12,
        'end_day' => 17,
    ],
];

foreach ($seasonsToUpdate as $data) {
    $season = Season::updateOrCreate(
        ['name' => $data['name']],
        [
            'start_month' => $data['start_month'],
            'start_day' => $data['start_day'],
            'end_month' => $data['end_month'],
            'end_day' => $data['end_day'],
            'is_active' => true
        ]
    );
    echo "Saison '{$data['name']}' mise à jour : {$season->period}\n";
}

echo "Mise à jour terminée avec succès.\n";
