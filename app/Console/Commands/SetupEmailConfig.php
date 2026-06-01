<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SettingsHelper;

class SetupEmailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:setup-config 
                            {--smtp-host= : Serveur SMTP (ex: smtp.gmail.com)}
                            {--smtp-port= : Port SMTP (ex: 587)}
                            {--smtp-username= : Nom d\'utilisateur SMTP}
                            {--smtp-password= : Mot de passe SMTP}
                            {--smtp-encryption= : Chiffrement (tls ou ssl)}
                            {--from-address= : Adresse email expéditeur}
                            {--from-name= : Nom expéditeur}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurer les paramètres SMTP pour PHPMailer dans global_settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Configuration des paramètres email SMTP...');
        $this->newLine();

        // Récupérer les valeurs depuis les options ou demander interactivement
        $smtpHost = $this->option('smtp-host') ?? $this->ask('Serveur SMTP (ex: smtp.gmail.com)', 'smtp.gmail.com');
        $smtpPort = $this->option('smtp-port') ?? $this->ask('Port SMTP (ex: 587 pour TLS, 465 pour SSL)', '587');
        $smtpUsername = $this->option('smtp-username') ?? $this->ask('Nom d\'utilisateur SMTP (email)');
        $smtpPassword = $this->option('smtp-password') ?? $this->secret('Mot de passe SMTP');
        $smtpEncryption = $this->option('smtp-encryption') ?? $this->choice('Chiffrement', ['tls', 'ssl'], 'tls');
        $fromAddress = $this->option('from-address') ?? $this->ask('Adresse email expéditeur', $smtpUsername);
        $fromName = $this->option('from-name') ?? $this->ask('Nom expéditeur', 'LUXÎLES');

        // Sauvegarder les paramètres
        SettingsHelper::set('email_smtp_host', $smtpHost, 'string');
        SettingsHelper::set('email_smtp_port', $smtpPort, 'integer');
        SettingsHelper::set('email_smtp_username', $smtpUsername, 'string');
        SettingsHelper::set('email_smtp_password', $smtpPassword, 'string');
        SettingsHelper::set('email_smtp_encryption', $smtpEncryption, 'string');
        SettingsHelper::set('email_from_address', $fromAddress, 'string');
        SettingsHelper::set('email_from_name', $fromName, 'string');

        $this->newLine();
        $this->info('✅ Paramètres email configurés avec succès !');
        $this->newLine();
        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['Serveur SMTP', $smtpHost],
                ['Port', $smtpPort],
                ['Utilisateur', $smtpUsername],
                ['Chiffrement', $smtpEncryption],
                ['Expéditeur', "$fromName <$fromAddress>"],
            ]
        );

        $this->newLine();
        if ($this->confirm('Voulez-vous tester l\'envoi d\'un email de test ?', true)) {
            $testEmail = $this->ask('Adresse email pour le test', $smtpUsername);
            
            try {
                $emailService = app(\App\Services\EmailService::class);
                $emailService->testEmail($testEmail);
                $this->info("✅ Email de test envoyé avec succès à : $testEmail");
            } catch (\Exception $e) {
                $this->error("❌ Erreur lors de l'envoi de l'email de test : " . $e->getMessage());
            }
        }
    }
}
