<?php

namespace App\Services;

class WhatsAppClickToChatService
{
    /**
     * Génère un lien Click-to-Chat WhatsApp avec message prérempli.
     */
    public function buildLink(string $phone, string $message): ?string
    {
        $formattedPhone = $this->formatPhoneNumber($phone);
        if (! $formattedPhone) {
            return null;
        }

        return 'https://wa.me/'.$formattedPhone.'?text='.rawurlencode($message);
    }

    /**
     * Formate un numéro vers le format attendu par wa.me
     * (ex: +33 6 12 34 56 78 => 33612345678).
     */
    public function formatPhoneNumber(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (! $digits) {
            return null;
        }

        // Cas fréquent FR local 0X... => 33X...
        if (str_starts_with($digits, '0')) {
            $digits = '33'.substr($digits, 1);
        }

        // Sanity check minimal
        if (strlen($digits) < 8) {
            return null;
        }

        return $digits;
    }

    /**
     * Construit le message WhatsApp de notification Privilege Club.
     */
    public function buildPrivilegeClubMessage(string $firstName, string $newTierLabel): string
    {
        return "Bonjour {$firstName},\n\n"
            ."Félicitations ! Votre statut LUXÎLES PRIVILEGE CLUB est désormais : {$newTierLabel}.\n\n"
            ."Retrouvez vos avantages dans votre espace client.\n\n"
            ."L'équipe LUXÎLES";
    }

    /**
     * Construit le message WhatsApp d'envoi d'un code promo (§3.2 CDC).
     */
    public function buildPromoCodeMessage(string $firstName, string $code, string $valueLabel, ?string $validUntil = null): string
    {
        $validityText = $validUntil ? "\nValidité : jusqu'au {$validUntil}." : '';

        return "Bonjour {$firstName},\n\n"
            ."Voici votre code promo LUXÎLES : {$code}\n"
            ."Avantage : {$valueLabel}{$validityText}\n\n"
            ."Saisissez ce code lors de votre réservation sur luxiles.fr.\n\n"
            ."L'équipe LUXÎLES";
    }
}

