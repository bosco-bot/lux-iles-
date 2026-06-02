<?php

use App\Models\Reservation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('payment_expires_at')->nullable()->after('status');
        });

        Reservation::query()
            ->where('status', 'pending')
            ->where('source', 'direct')
            ->whereNull('payment_expires_at')
            ->cursor()
            ->each(function (Reservation $reservation): void {
                $reservation->update([
                    'payment_expires_at' => $reservation->created_at->addHours(24),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('payment_expires_at');
        });
    }
};
