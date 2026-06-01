<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * §3.4 CDC — statut modération « approved » (ex. published).
     */
    public function up(): void
    {
        DB::table('villa_reviews')
            ->where('status', 'published')
            ->update(['status' => 'approved']);
    }

    public function down(): void
    {
        DB::table('villa_reviews')
            ->where('status', 'approved')
            ->update(['status' => 'published']);
    }
};
