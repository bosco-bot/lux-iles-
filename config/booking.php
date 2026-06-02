<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Délai de paiement de l'acompte (réservations en ligne)
    |--------------------------------------------------------------------------
    |
    | Durée pendant laquelle une réservation « pending » bloque le calendrier
    | avant annulation automatique si l'acompte n'est pas payé.
    |
    */
    'unpaid_deposit_grace_hours' => (int) env('BOOKING_UNPAID_DEPOSIT_GRACE_HOURS', 24),

];
