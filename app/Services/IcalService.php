<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Villa;
use App\Models\VillaIcalConfig;
use App\Models\PlatformSync;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IcalService
{
    /**
     * Génère un fichier iCal pour une villa à partir de ses réservations
     */
    public function generateIcalForVilla(Villa $villa): string
    {
        $reservations = Reservation::where('villa_id', $villa->id)
            ->whereIn('status', ['confirmed', 'deposit_paid', 'fully_paid', 'pending'])
            ->where('check_out_date', '>=', now())
            ->orderBy('check_in_date')
            ->get();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//LUXÎLES//Reservations//FR\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";

        foreach ($reservations as $reservation) {
            $ical .= $this->generateEvent($reservation);
        }

        $ical .= "END:VCALENDAR\r\n";

        return $ical;
    }

    /**
     * Génère un événement iCal pour une réservation
     */
    private function generateEvent(Reservation $reservation): string
    {
        $checkIn = Carbon::parse($reservation->check_in_date)->setTime(16, 0, 0);
        $checkOut = Carbon::parse($reservation->check_out_date)->setTime(10, 0, 0);

        // Utiliser le domaine depuis APP_URL pour rendre portable
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'luxiles.com';
        $uid = 'reservation-' . $reservation->id . '@' . $domain;
        $dtstamp = now()->format('Ymd\THis\Z');
        $dtstart = $checkIn->format('Ymd\THis\Z');
        $dtend = $checkOut->format('Ymd\THis\Z');

        $summary = "Réservé - " . $reservation->guest_first_name . " " . $reservation->guest_last_name;
        $description = "Réservation #" . $reservation->reservation_number;

        $event = "BEGIN:VEVENT\r\n";
        $event .= "UID:" . $uid . "\r\n";
        $event .= "DTSTAMP:" . $dtstamp . "\r\n";
        $event .= "DTSTART:" . $dtstart . "\r\n";
        $event .= "DTEND:" . $dtend . "\r\n";
        $event .= "SUMMARY:" . $this->escapeIcalText($summary) . "\r\n";
        $event .= "DESCRIPTION:" . $this->escapeIcalText($description) . "\r\n";
        $event .= "STATUS:CONFIRMED\r\n";
        $event .= "END:VEVENT\r\n";

        return $event;
    }

    /**
     * Échappe le texte pour iCal
     */
    private function escapeIcalText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(',', '\\,', $text);
        $text = str_replace(';', '\\;', $text);
        $text = str_replace("\n", '\\n', $text);
        return $text;
    }

    /**
     * Parse un fichier iCal depuis une URL et retourne les événements
     */
    public function parseIcalFromUrl(string $url): array
    {
        try {
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                throw new \Exception("Impossible de récupérer le fichier iCal depuis l'URL: " . $url);
            }

            return $this->parseIcalContent($response->body());
        } catch (\Exception $e) {
            Log::error("Erreur lors du parsing iCal depuis l'URL: " . $url, [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Parse le contenu d'un fichier iCal
     */
    public function parseIcalContent(string $icalContent): array
    {
        $events = [];
        $lines = explode("\n", $icalContent);
        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strpos($line, 'BEGIN:VEVENT') !== false) {
                $currentEvent = [];
            } elseif (strpos($line, 'END:VEVENT') !== false && $currentEvent !== null) {
                if (isset($currentEvent['DTSTART']) && isset($currentEvent['DTEND'])) {
                    $events[] = $currentEvent;
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null) {
                if (strpos($line, 'DTSTART') === 0) {
                    $currentEvent['DTSTART'] = $this->parseIcalDate($line);
                } elseif (strpos($line, 'DTEND') === 0) {
                    $currentEvent['DTEND'] = $this->parseIcalDate($line);
                } elseif (strpos($line, 'SUMMARY') === 0) {
                    $currentEvent['SUMMARY'] = $this->parseIcalValue($line);
                } elseif (strpos($line, 'UID') === 0) {
                    $currentEvent['UID'] = $this->parseIcalValue($line);
                }
            }
        }

        return $events;
    }

    /**
     * Parse une date iCal
     */
    private function parseIcalDate(string $line): ?Carbon
    {
        if (preg_match('/DTSTART[^:]*:(.+)/', $line, $matches)) {
            $dateStr = trim($matches[1]);
            
            // Format iCal standard: YYYYMMDDTHHMMSSZ ou YYYYMMDD
            if (strlen($dateStr) >= 8) {
                $year = substr($dateStr, 0, 4);
                $month = substr($dateStr, 4, 2);
                $day = substr($dateStr, 6, 2);
                
                $hour = 0;
                $minute = 0;
                $second = 0;
                
                if (strlen($dateStr) > 8 && $dateStr[8] === 'T') {
                    $hour = substr($dateStr, 9, 2) ?? 0;
                    $minute = substr($dateStr, 11, 2) ?? 0;
                    $second = substr($dateStr, 13, 2) ?? 0;
                }
                
                try {
                    return Carbon::create($year, $month, $day, $hour, $minute, $second);
                } catch (\Exception $e) {
                    Log::warning("Erreur de parsing de date iCal: " . $dateStr);
                }
            }
        }
        
        return null;
    }

    /**
     * Parse une valeur iCal
     */
    private function parseIcalValue(string $line): string
    {
        if (preg_match('/[^:]+:(.+)/', $line, $matches)) {
            $value = trim($matches[1]);
            // Décoder les caractères échappés
            $value = str_replace('\\n', "\n", $value);
            $value = str_replace('\\,', ',', $value);
            $value = str_replace('\\;', ';', $value);
            $value = str_replace('\\\\', '\\', $value);
            return $value;
        }
        return '';
    }

    /**
     * Synchronise les réservations d'une villa depuis sa configuration iCal
     */
    public function syncVillaFromPlatform(VillaIcalConfig $config): array
    {
        if (!$config->ical_import_url) {
            return ['success' => false, 'message' => 'Pas d\'URL d\'import configurée'];
        }

        try {
            $events = $this->parseIcalFromUrl($config->ical_import_url);
            $newReservationsCount = 0;

            DB::beginTransaction();

            foreach ($events as $event) {
                $checkIn = $event['DTSTART'] ?? null;
                $checkOut = $event['DTEND'] ?? null;

                if (!$checkIn || !$checkOut) {
                    continue;
                }

                // Vérifier si la réservation existe déjà (par UID ou par dates/source)
                $existingReservation = Reservation::where('villa_id', $config->villa_id)
                    ->where(function($query) use ($event, $checkIn, $checkOut, $config) {
                        if (isset($event['UID']) && !empty($event['UID'])) {
                            $query->where('platform_reservation_id', $event['UID']);
                        } else {
                            $query->where('check_in_date', $checkIn->format('Y-m-d'))
                                  ->where('check_out_date', $checkOut->format('Y-m-d'))
                                  ->where('source', $config->platform);
                        }
                    })
                    ->first();

                if (!$existingReservation) {
                    // Créer une nouvelle réservation
                    $reservationNumber = 'SYNC-' . strtoupper($config->platform) . '-' . time() . '-' . rand(1000, 9999);
                    $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'luxiles.com';

                    Reservation::create([
                        'reservation_number' => $reservationNumber,
                        'villa_id' => $config->villa_id,
                        'guest_first_name' => 'Client',
                        'guest_last_name' => $config->platform_name ?? ucfirst($config->platform),
                        'guest_email' => 'sync@' . $domain,
                        'check_in_date' => $checkIn->format('Y-m-d'),
                        'check_out_date' => $checkOut->format('Y-m-d'),
                        'number_of_nights' => $checkIn->diffInDays($checkOut),
                        'number_of_guests' => 2,
                        'base_price' => 0,
                        'total_price' => 0,
                        'deposit_amount' => 0,
                        'balance_amount' => 0,
                        'status' => 'confirmed',
                        'source' => $config->platform,
                        'platform_reservation_id' => $event['UID'] ?? null,
                    ]);
                    $newReservationsCount++;
                }
            }

            $config->update([
                'last_sync_status' => 'success',
                'last_sync_error' => null,
                'last_sync_at' => now()
            ]);

            // Logger dans platform_syncs pour l'historique et le dashboard
            PlatformSync::create([
                'villa_id' => $config->villa_id,
                'platform' => in_array($config->platform, ['airbnb', 'booking', 'abritel']) ? $config->platform : 'airbnb',
                'platform_listing_id' => $config->villa_id,
                'sync_type' => 'availability',
                'status' => 'synced',
                'last_sync_at' => now(),
                'sync_data' => ['new_reservations' => $newReservationsCount],
            ]);

            DB::commit();

            return [
                'success' => true, 
                'new_count' => $newReservationsCount,
                'message' => "Synchronisation réussie : {$newReservationsCount} nouvelles réservations."
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur sync iCal pour Villa {$config->villa_id} ({$config->platform}): " . $e->getMessage());

            $config->update([
                'last_sync_status' => 'error',
                'last_sync_error' => $e->getMessage(),
                'last_sync_at' => now()
            ]);

            // Logger l'erreur
            PlatformSync::create([
                'villa_id' => $config->villa_id,
                'platform' => in_array($config->platform, ['airbnb', 'booking', 'abritel']) ? $config->platform : 'airbnb',
                'platform_listing_id' => $config->villa_id,
                'sync_type' => 'availability',
                'status' => 'error',
                'last_sync_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }
}



