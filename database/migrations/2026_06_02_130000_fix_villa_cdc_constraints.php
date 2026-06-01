<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * §3.6 & §3.7 CDC — valeurs contractuelles Aurélio, Soléro, Élios.
     */
    public function up(): void
    {
        $rules = [
            ['pattern' => '%urélio%', 'minimum_stay_nights' => 4, 'max_capacity' => 8],
            ['pattern' => '%oléro%', 'minimum_stay_nights' => 4, 'max_capacity' => 8],
            ['pattern' => '%lios%', 'minimum_stay_nights' => 2, 'max_capacity' => 4],
        ];

        foreach ($rules as $rule) {
            DB::table('villas')
                ->where('name', 'like', $rule['pattern'])
                ->update([
                    'minimum_stay_nights' => $rule['minimum_stay_nights'],
                    'max_capacity' => $rule['max_capacity'],
                ]);
        }
    }

    public function down(): void
    {
        // Pas de rollback métier — valeurs CDC non réversibles de façon fiable.
    }
};
