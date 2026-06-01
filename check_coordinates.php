<?php

use App\Models\Villa;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$villas = Villa::all();
$total = $villas->count();
$withCoords = $villas->whereNotNull('latitude')->whereNotNull('longitude')->count();
$withoutCoords = $total - $withCoords;

echo "Total Villas: $total\n";
echo "With Coordinates: $withCoords\n";
echo "Without Coordinates: $withoutCoords\n";

if ($withoutCoords > 0) {
    echo "\nVillas without coordinates (will use address fallback):\n";
    foreach ($villas->whereNull('latitude') as $villa) {
        echo "- {$villa->name}\n";
    }
}
