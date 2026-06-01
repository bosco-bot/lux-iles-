<?php

namespace App\Console\Commands;

use App\Services\PrivilegeClubService;
use Illuminate\Console\Command;

class PrivilegeAnnualDowngrade extends Command
{
    protected $signature = 'privilege:annual-downgrade {--year=}';

    protected $description = 'Rétrograde les membres sans réservation l\'année précédente (1er janvier — §3.1 CDC)';

    public function handle(PrivilegeClubService $clubService): int
    {
        $year = $this->option('year') ? (int) $this->option('year') : null;
        $count = $clubService->runAnnualMaintenance($year);
        $this->info("{$count} membre(s) rétrogradé(s).");

        return self::SUCCESS;
    }
}
