<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VillaIcalConfig;
use App\Services\IcalService;
use Illuminate\Support\Facades\Log;

class SyncVillas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-villas {--villa= : ID d\'une villa spécifique}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise les calendriers des villas depuis les plateformes externes (iCal)';

    protected $icalService;

    public function __construct(IcalService $icalService)
    {
        parent::__construct();
        $this->icalService = $icalService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de la synchronisation des villas...');

        $query = VillaIcalConfig::where('is_active', true)
            ->whereNotNull('ical_import_url');

        if ($this->option('villa')) {
            $query->where('villa_id', $this->option('villa'));
        }

        $configs = $query->with('villa')->get();

        if ($configs->isEmpty()) {
            $this->warn('Aucune configuration active trouvée.');
            return 0;
        }

        $this->info("Trouvé {$configs->count()} configurations à synchroniser.");

        $bar = $this->output->createProgressBar($configs->count());
        $bar->start();

        foreach ($configs as $config) {
            try {
                $result = $this->icalService->syncVillaFromPlatform($config);
                
                if ($result['success']) {
                    Log::info("Sync réussie pour {$config->villa->name} ({$config->platform}): " . $result['message']);
                } else {
                    $this->error("\nErreur pour {$config->villa->name} ({$config->platform}): " . $result['message']);
                }
            } catch (\Exception $e) {
                $this->error("\nException lors de la sync pour {$config->villa->name} ({$config->platform}): " . $e->getMessage());
                Log::error("Command SyncVillas error: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Synchronisation terminée.');

        return 0;
    }
}
