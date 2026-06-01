<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\DB;

class SetupStripeKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:setup-keys 
                            {--public-key= : Clé publique Stripe (pk_test_... ou pk_live_...)}
                            {--secret-key= : Clé secrète Stripe (sk_test_... ou sk_live_...)}
                            {--webhook-secret= : Secret webhook Stripe (whsec_...)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurer les clés Stripe dans la base de données';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Configuration des clés Stripe...');
        $this->newLine();

        // Vérifier que la table global_settings existe
        if (!DB::getSchemaBuilder()->hasTable('global_settings')) {
            $this->error('La table global_settings n\'existe pas. Veuillez exécuter les migrations.');
            return Command::FAILURE;
        }

        // Récupérer les clés depuis les options ou demander à l'utilisateur
        $publicKey = $this->option('public-key');
        $secretKey = $this->option('secret-key');
        $webhookSecret = $this->option('webhook-secret');

        if (!$publicKey) {
            $publicKey = $this->ask('Entrez la clé publique Stripe (pk_test_... ou pk_live_...)');
        }

        if (!$secretKey) {
            $secretKey = $this->ask('Entrez la clé secrète Stripe (sk_test_... ou sk_live_...)');
        }

        if (!$webhookSecret) {
            $webhookSecret = $this->ask('Entrez le secret webhook Stripe (whsec_...) - optionnel', null);
        }

        // Valider les clés
        if (!str_starts_with($publicKey, 'pk_')) {
            $this->error('La clé publique doit commencer par pk_');
            return Command::FAILURE;
        }

        if (!str_starts_with($secretKey, 'sk_')) {
            $this->error('La clé secrète doit commencer par sk_');
            return Command::FAILURE;
        }

        if ($webhookSecret && !str_starts_with($webhookSecret, 'whsec_')) {
            $this->error('Le secret webhook doit commencer par whsec_');
            return Command::FAILURE;
        }

        // Stocker les clés
        try {
            SettingsHelper::set('stripe_public_key', $publicKey, 'string');
            $this->info('✓ Clé publique Stripe stockée');

            SettingsHelper::set('stripe_secret_key', $secretKey, 'string');
            $this->info('✓ Clé secrète Stripe stockée');

            if ($webhookSecret) {
                SettingsHelper::set('stripe_webhook_secret', $webhookSecret, 'string');
                $this->info('✓ Secret webhook Stripe stocké');
            }

            // Vider le cache
            SettingsHelper::clearCache();

            $this->newLine();
            $this->info('Configuration Stripe terminée avec succès !');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Erreur lors de la configuration : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
