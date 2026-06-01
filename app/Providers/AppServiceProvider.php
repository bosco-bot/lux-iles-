<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Island;
use App\Models\Payment;
use App\Observers\PaymentObserver;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer l'observer pour les paiements
        Payment::observe(PaymentObserver::class);

        // Partager les destinations (îles) avec le footer pour affichage dynamique
        View::composer('components.footer', function ($view) {
            $view->with('footerDestinations', Island::orderBy('name', 'asc')->get());
        });
        
        // Configurer Laravel Mail avec les paramètres SMTP depuis la base de données
        $this->configureMailFromDatabase();
    }
    
    /**
     * Configurer Laravel Mail avec les paramètres SMTP depuis global_settings
     */
    protected function configureMailFromDatabase(): void
    {
        try {
            // Vérifier que la table existe avant d'essayer de lire les paramètres
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('global_settings')) {
                return;
            }
            
            // Récupérer les paramètres SMTP depuis la base de données
            $smtpHost = SettingsHelper::get('email_smtp_host');
            $smtpPort = SettingsHelper::get('email_smtp_port');
            $smtpUsername = SettingsHelper::get('email_smtp_username');
            $smtpPassword = SettingsHelper::get('email_smtp_password');
            $smtpEncryption = SettingsHelper::get('email_smtp_encryption', 'tls');
            $fromEmail = SettingsHelper::get('email_from_address');
            $fromName = SettingsHelper::get('email_from_name');
            
            // Si les paramètres SMTP sont configurés, les utiliser pour Laravel Mail
            if ($smtpHost && $smtpUsername && $smtpPassword) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', $smtpHost);
                Config::set('mail.mailers.smtp.port', $smtpPort ?? 587);
                Config::set('mail.mailers.smtp.username', $smtpUsername);
                Config::set('mail.mailers.smtp.password', $smtpPassword);
                
                // Convertir 'tls' en 'tls' et 'ssl' en 'ssl' pour Laravel
                if ($smtpEncryption === 'ssl') {
                    Config::set('mail.mailers.smtp.encryption', 'ssl');
                } else {
                    Config::set('mail.mailers.smtp.encryption', 'tls');
                }
                
                // Configurer l'expéditeur par défaut
                if ($fromEmail) {
                    Config::set('mail.from.address', $fromEmail);
                }
                if ($fromName) {
                    Config::set('mail.from.name', $fromName);
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, ne pas bloquer le démarrage de l'application
            // Les paramètres par défaut depuis .env seront utilisés
            \Illuminate\Support\Facades\Log::warning('Impossible de charger la configuration SMTP depuis la base de données: ' . $e->getMessage());
        }
    }
}
