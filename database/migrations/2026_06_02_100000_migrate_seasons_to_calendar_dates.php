<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('name');
            $table->date('end_date')->nullable()->after('start_date');
        });

        $referenceYear = (int) now()->format('Y');

        foreach (DB::table('seasons')->orderBy('id')->get() as $season) {
            $start = $this->safeDate($referenceYear, (int) $season->start_month, (int) $season->start_day);
            $end = $this->safeDate($referenceYear, (int) $season->end_month, (int) $season->end_day);

            $startValue = (int) $season->start_month * 100 + (int) $season->start_day;
            $endValue = (int) $season->end_month * 100 + (int) $season->end_day;

            if ($startValue > $endValue) {
                $end = $end->copy()->addYear();
            }

            if ($end->lt($start)) {
                $end = $start->copy();
            }

            DB::table('seasons')->where('id', $season->id)->update([
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ]);
        }

        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn(['start_month', 'start_day', 'end_month', 'end_day']);
        });
    }

    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->tinyInteger('start_month')->after('name');
            $table->tinyInteger('start_day')->after('start_month');
            $table->tinyInteger('end_month')->after('start_day');
            $table->tinyInteger('end_day')->after('end_month');
        });

        foreach (DB::table('seasons')->orderBy('id')->get() as $season) {
            $start = Carbon::parse($season->start_date);
            $end = Carbon::parse($season->end_date);

            DB::table('seasons')->where('id', $season->id)->update([
                'start_month' => $start->month,
                'start_day' => $start->day,
                'end_month' => $end->month,
                'end_day' => $end->day,
            ]);
        }

        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }

    private function safeDate(int $year, int $month, int $day): Carbon
    {
        $month = max(1, min(12, $month));
        $maxDay = Carbon::create($year, $month, 1)->daysInMonth;
        $day = max(1, min($maxDay, $day));

        return Carbon::create($year, $month, $day)->startOfDay();
    }
};
