<?php

namespace App\Console\Commands;

use App\Services\PrivilegeClubService;
use Illuminate\Console\Command;

class SyncPrivilegeClubAfterStays extends Command
{
    protected $signature = 'privilege-club:sync-after-stays';

    protected $description = 'Recalcule les paliers Privilege Club après les départs de la veille (§3.1 CDC)';

    public function handle(PrivilegeClubService $clubService): int
    {
        $count = $clubService->syncUsersAfterRecentCheckouts();
        $this->info("{$count} palier(s) mis à jour.");

        return self::SUCCESS;
    }
}
