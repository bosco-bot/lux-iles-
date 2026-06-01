<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Helpers\SettingsHelper;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\User;
use App\Services\PrivilegeClubService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class EmailService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    /**
     * Configurer PHPMailer avec les paramètres depuis global_settings
     */
    protected function configureMailer()
    {
        try {
            // Configuration SMTP depuis global_settings
            $smtpHost = SettingsHelper::get('email_smtp_host', 'smtp.gmail.com');
            $smtpPort = SettingsHelper::get('email_smtp_port', 587);
            $smtpUsername = SettingsHelper::get('email_smtp_username');
            $smtpPassword = SettingsHelper::get('email_smtp_password');
            $smtpEncryption = SettingsHelper::get('email_smtp_encryption', 'tls'); // tls ou ssl
            $fromEmail = SettingsHelper::get('email_from_address', 'noreply@lux-iles.com');
            $fromName = SettingsHelper::get('email_from_name', 'LUXÎLES');

            // Configuration de base
            $this->mailer->isSMTP();
            $this->mailer->Host = $smtpHost;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $smtpUsername;
            $this->mailer->Password = $smtpPassword;
            $this->mailer->SMTPSecure = $smtpEncryption;
            $this->mailer->Port = (int) $smtpPort;
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';

            // Expéditeur par défaut
            $this->mailer->setFrom($fromEmail, $fromName);

            // Mode debug (désactivé en production)
            $this->mailer->SMTPDebug = 0; // 0 = off, 2 = client, 3 = client + server
            $this->mailer->Debugoutput = function($str, $level) {
                Log::debug("PHPMailer: $str");
            };
        } catch (Exception $e) {
            Log::error("Erreur configuration PHPMailer: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoyer un email avec un template Blade
     */
    protected function sendEmail($to, $subject, $template, $data = [])
    {
        try {
            // Réinitialiser le mailer pour chaque envoi
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->clearCustomHeaders();
            $this->mailer->clearReplyTos();

            // Reconfigurer l'expéditeur pour chaque envoi (au cas où les paramètres changent)
            $fromEmail = SettingsHelper::get('email_from_address', 'contact.luxiles@gmail.com');
            $fromName = SettingsHelper::get('email_from_name', 'LUXÎLES');
            $this->mailer->setFrom($fromEmail, $fromName);
            
            // Ajouter Reply-To avec la même adresse pour Gmail (bonne pratique)
            $this->mailer->addReplyTo($fromEmail, $fromName);

            // Destinataire
            if (is_array($to)) {
                foreach ($to as $email => $name) {
                    if (is_numeric($email)) {
                        $this->mailer->addAddress($name);
                    } else {
                        $this->mailer->addAddress($email, $name);
                    }
                }
            } else {
                $this->mailer->addAddress($to);
            }

            // Sujet
            $this->mailer->Subject = $subject;

            // Corps HTML depuis template Blade
            $htmlContent = View::make($template, $data)->render();
            $this->mailer->isHTML(true);
            $this->mailer->Body = $htmlContent;

            // Version texte alternative (optionnel)
            $textContent = strip_tags($htmlContent);
            $this->mailer->AltBody = $textContent;

            // Envoyer l'email
            $this->mailer->send();
            
            Log::info("Email envoyé avec succès à: $to | Sujet: $subject");
            return true;
        } catch (Exception $e) {
            Log::error("Erreur envoi email à $to: " . $this->mailer->ErrorInfo);
            throw new \Exception("Erreur envoi email: " . $this->mailer->ErrorInfo);
        }
    }

    /**
     * Envoyer un email de confirmation de réservation
     */
    public function sendReservationConfirmation(Reservation $reservation)
    {
        $user = $reservation->user;
        $villa = $reservation->villa;

        $data = [
            'reservation' => $reservation,
            'user' => $user,
            'villa' => $villa,
            'checkIn' => \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y'),
            'checkOut' => \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y'),
        ];

        return $this->sendEmail(
            $user->email,
            "Confirmation de réservation - {$reservation->reservation_number}",
            'emails.reservation-confirmation',
            $data
        );
    }

    /**
     * Envoyer un rappel de paiement
     */
    public function sendPaymentReminder(Payment $payment)
    {
        $reservation = $payment->reservation;
        $user = $reservation->user;
        $villa = $reservation->villa;

        $data = [
            'payment' => $payment,
            'reservation' => $reservation,
            'user' => $user,
            'villa' => $villa,
            'dueDate' => $payment->due_date ? \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') : null,
        ];

        $paymentTypeLabel = match($payment->type) {
            'deposit' => 'acompte',
            'balance' => 'solde',
            'deposit_guarantee' => 'caution',
            default => 'paiement',
        };

        return $this->sendEmail(
            $user->email,
            "Rappel de paiement - {$paymentTypeLabel} pour la réservation {$reservation->reservation_number}",
            'emails.payment-reminder',
            $data
        );
    }

    /**
     * Envoyer une confirmation de paiement
     */
    public function sendPaymentConfirmation(Payment $payment)
    {
        $reservation = $payment->reservation;
        $user = $reservation->user;
        $villa = $reservation->villa;

        $data = [
            'payment' => $payment,
            'reservation' => $reservation,
            'user' => $user,
            'villa' => $villa,
        ];

        $paymentTypeLabel = match($payment->type) {
            'deposit' => 'acompte',
            'balance' => 'solde',
            'deposit_guarantee' => 'caution',
            default => 'paiement',
        };

        return $this->sendEmail(
            $user->email,
            "Confirmation de paiement - {$paymentTypeLabel} pour la réservation {$reservation->reservation_number}",
            'emails.payment-confirmation',
            $data
        );
    }

    /**
     * Envoyer un rappel avant arrivée
     */
    public function sendArrivalReminder(Reservation $reservation, $daysBefore = 7)
    {
        $user = $reservation->user;
        $villa = $reservation->villa;

        $data = [
            'reservation' => $reservation,
            'user' => $user,
            'villa' => $villa,
            'checkIn' => \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y'),
            'checkOut' => \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y'),
            'daysBefore' => $daysBefore,
        ];

        return $this->sendEmail(
            $user->email,
            "Rappel - Votre séjour commence dans {$daysBefore} jour(s)",
            'emails.arrival-reminder',
            $data
        );
    }

    /**
     * Envoyer un email de bienvenue
     */
    public function sendWelcomeEmail(User $user)
    {
        $data = [
            'user' => $user,
        ];

        return $this->sendEmail(
            $user->email,
            "Bienvenue sur LUXÎLES",
            'emails.welcome',
            $data
        );
    }

    /**
     * Envoyer un email d'annulation
     */
    public function sendCancellationEmail(Reservation $reservation)
    {
        $user = $reservation->user;
        $villa = $reservation->villa;

        $data = [
            'reservation' => $reservation,
            'user' => $user,
            'villa' => $villa,
            'checkIn' => \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y'),
            'checkOut' => \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y'),
        ];

        return $this->sendEmail(
            $user->email,
            "Annulation de réservation - {$reservation->reservation_number}",
            'emails.cancellation',
            $data
        );
    }

    /**
     * Envoyer un email de réinitialisation de mot de passe
     */
    public function sendPasswordResetEmail(User $user, string $token)
    {
        // Construire l'URL de réinitialisation avec le token uniquement
        $resetUrl = route('password.reset', ['token' => $token]);
        
        $data = [
            'user' => $user,
            'token' => $token,
            'resetUrl' => $resetUrl,
        ];
        
        return $this->sendEmail(
            $user->email,
            "Réinitialisation de votre mot de passe - LUXÎLES",
            'emails.password-reset',
            $data
        );
    }

    /**
     * Invitation à définir le mot de passe (création de compte par l'admin — §3.9 CDC).
     */
    public function sendAccountInvitationEmail(User $user, string $token)
    {
        $setPasswordUrl = route('password.reset', ['token' => $token]);

        $data = [
            'user' => $user,
            'token' => $token,
            'setPasswordUrl' => $setPasswordUrl,
        ];

        return $this->sendEmail(
            $user->email,
            'Bienvenue sur LUXÎLES — Définissez votre mot de passe',
            'emails.account-invitation',
            $data
        );
    }

    /**
     * Changement de palier Privilege Club (§3.1 CDC).
     */
    public function sendPrivilegeClubTierChange(User $user, ?string $oldTier, ?string $newTier)
    {
        $clubService = app(PrivilegeClubService::class);

        $data = [
            'user' => $user,
            'oldTier' => $oldTier,
            'newTier' => $newTier,
            'oldTierLabel' => $clubService->tierLabel($oldTier),
            'newTierLabel' => $clubService->tierLabel($newTier),
            'clubUrl' => route('espace-client.privilege-club'),
            'tierDefinition' => $newTier ? ($clubService->tierDefinitions()[$newTier] ?? null) : null,
        ];

        $isUpgrade = $clubService->tierRank($newTier) > $clubService->tierRank($oldTier);
        $data['isUpgrade'] = $isUpgrade;

        return $this->sendEmail(
            $user->email,
            $isUpgrade
                ? 'Félicitations — Votre statut LUXÎLES PRIVILEGE CLUB évolue'
                : 'Mise à jour de votre statut LUXÎLES PRIVILEGE CLUB',
            'emails.privilege-club-tier-change',
            $data
        );
    }

    /**
     * Tester la configuration email
     */
    public function testEmail($toEmail)
    {
        $data = [
            'test' => true,
            'message' => 'Ceci est un email de test pour vérifier la configuration SMTP.',
        ];

        return $this->sendEmail(
            $toEmail,
            "Test de configuration email - LUXÎLES",
            'emails.test',
            $data
        );
    }

    /**
     * Envoyer une notification de message de contact
     */
    public function sendContactNotification($toEmail, array $data)
    {
        return $this->sendEmail(
            $toEmail,
            "Nouveau message de contact - {$data['subject']}",
            'emails.contact-notification',
            $data
        );
    }
}

