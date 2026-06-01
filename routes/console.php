<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tâche de synchronisation des calendriers (toutes les heures)
Schedule::command('app:sync-villas')
    ->hourly()
    ->description('Synchroniser les calendriers iCal depuis les plateformes externes');

// Planifier l'envoi des rappels automatiques (tous les jours à 9h00)
Schedule::command('email:send-reminders')
    ->dailyAt('09:00')
    ->timezone('Europe/Paris')
    ->description('Envoyer les rappels automatiques (paiements et arrivées)');

Schedule::command('privilege-club:sync-after-stays')
    ->dailyAt('06:00')
    ->timezone('Europe/Paris')
    ->description('Recalcul des paliers Privilege Club après départs');

Schedule::command('privilege:annual-downgrade')
    ->yearlyOn(1, 1, '00:15')
    ->timezone('Europe/Paris')
    ->description('Rétrogradation Privilege Club — maintenance annuelle');
