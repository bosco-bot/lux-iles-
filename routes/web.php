<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VillaController as AdminVillaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CancellationPolicyController;
use App\Http\Controllers\Admin\SynchronizationController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\IslandController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VillaController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EspaceClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\TrafficStatsController;
use App\Http\Controllers\Admin\VillaReviewController as AdminVillaReviewController;
use App\Http\Controllers\VillaReviewController;
use App\Http\Controllers\PrivilegeClubController;
use App\Http\Controllers\ClientNotificationController;

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Pages Villas (Publique)
Route::prefix('villas')->name('villas.')->group(function () {
    Route::get('/', [VillaController::class, 'index'])->name('index');
    Route::get('/{id}', [VillaController::class, 'show'])->name('show');
});

// Routes pour les destinations (îles)
Route::get('/destination/martinique', function () {
    return redirect()->route('villas.index', ['island' => 1]);
})->name('destination.martinique');

Route::get('/destination/guadeloupe', function () {
    return redirect()->route('villas.index', ['island' => 2]);
})->name('destination.guadeloupe');

Route::get('/destination/saint-barthelemy', function () {
    return redirect()->route('villas.index', ['island' => 3]);
})->name('destination.saint-barthelemy');

Route::get('/destination/saint-martin', function () {
    return redirect()->route('villas.index', ['island' => 4]);
})->name('destination.saint-martin');

Route::get('/destination/les-saintes', function () {
    return redirect()->route('villas.index', ['island' => 5]);
})->name('destination.les-saintes');

// Page de Réservation (Publique)
Route::prefix('booking')->name('bookings.')->group(function () {
    Route::get('/create', [BookingController::class, 'create'])->name('create');
    Route::get('/payment', [BookingController::class, 'payment'])->name('payment');
    Route::post('/confirm', [BookingController::class, 'confirm'])->name('confirm');
    Route::get('/confirmation', [BookingController::class, 'showConfirmation'])->name('confirmation');
    Route::post('/calculate-price', [BookingController::class, 'calculatePrice'])->name('calculate-price');
    Route::post('/check-promo', [BookingController::class, 'checkPromo'])->middleware('auth')->name('check-promo');
});

// Page Espace Client (Protégée)
Route::prefix('espace-client')->name('espace-client.')->middleware('auth')->group(function () {
    Route::get('/profil', function () {
        return view('pages.profile');
    })->name('profile');
    
    Route::get('/reservations', [EspaceClientController::class, 'reservations'])->name('reservations');

    Route::get('/reservation/{reservation}/avis', [VillaReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reservation/{reservation}/avis', [VillaReviewController::class, 'store'])->name('reviews.store');

    Route::get('/privilege-club', [PrivilegeClubController::class, 'index'])->name('privilege-club');
    Route::post('/privilege-club/notifications/{notification}/read', [PrivilegeClubController::class, 'markNotificationRead'])->name('privilege-club.notifications.read');
    
    Route::get('/documents', [EspaceClientController::class, 'documents'])->name('documents');
    
    Route::get('/payments', [EspaceClientController::class, 'payments'])->name('payments');
    
    Route::get('/reservation/{reservation}/pay-deposit', [EspaceClientController::class, 'payDeposit'])->name('pay-deposit');
    Route::get('/reservation/{reservation}/pay-balance', [EspaceClientController::class, 'payBalance'])->name('pay-balance');
    Route::get('/reservation/{reservation}/pay-deposit-guarantee', [EspaceClientController::class, 'payDepositGuarantee'])->name('pay-deposit-guarantee');
    
    Route::get('/favoris', [FavoriteController::class, 'index'])->name('favoris');
    
    // Routes API pour favoris
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{villa}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/favorites/check/{villa}', [FavoriteController::class, 'check'])->name('favorites.check');
    
    Route::get('/messages', [EspaceClientController::class, 'messages'])->name('messages');
    Route::post('/messages/send', [EspaceClientController::class, 'sendMessage'])->name('messages.send');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [ClientNotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [ClientNotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{id}/mark-as-read', [ClientNotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [ClientNotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    });
    
    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/reservation/{reservation}/contract', [DocumentController::class, 'generateContract'])->name('contract');
        Route::get('/reservation/{reservation}/invoice', [DocumentController::class, 'generateInvoice'])->name('invoice');
        Route::get('/reservation/{reservation}/receipt-deposit/{payment}', [DocumentController::class, 'generateReceiptDeposit'])->name('receipt-deposit');
        Route::get('/reservation/{reservation}/receipt-balance/{payment}', [DocumentController::class, 'generateReceiptBalance'])->name('receipt-balance');
        Route::get('/dossier/{clientDocument}/download', [\App\Http\Controllers\ClientDocumentController::class, 'download'])->name('client-documents.download');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
        Route::post('/reservation/{reservation}/generate', [DocumentController::class, 'generate'])->name('generate');
    });

    Route::get('/{reservation?}', [EspaceClientController::class, 'index'])->name('index');
});

// Page Contact
Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'send'])->name('send');
});

// Gestion simple du consentement cookies
Route::post('/cookies/accept', function () {
    return back()->withCookie(cookie()->forever('cookie_consent', 'accepted'));
})->name('cookies.accept');

Route::post('/cookies/reject', function () {
    return back()->withCookie(cookie()->forever('cookie_consent', 'rejected'));
})->name('cookies.reject');

// Routes pour les services (redirection vers contact avec sujet pré-rempli)
Route::get('/services/conciergerie', function () {
    return redirect()->route('contact.index', ['subject' => 'Conciergerie 24/7']);
})->name('services.conciergerie');

Route::get('/services/chef-domicile', function () {
    return redirect()->route('contact.index', ['subject' => 'Chef à domicile']);
})->name('services.chef-domicile');

Route::get('/services/transferts-prives', function () {
    return redirect()->route('contact.index', ['subject' => 'Transferts privés']);
})->name('services.transferts-prives');

Route::get('/services/activites-exclusives', function () {
    return redirect()->route('contact.index', ['subject' => 'Activités exclusives']);
})->name('services.activites-exclusives');

// Route pour les politiques d'annulation
Route::get('/conditions-annulation', [App\Http\Controllers\Admin\CancellationPolicyController::class, 'publicIndex'])
    ->name('cancellation-policies.index');

// Pages légales
Route::get('/politique-confidentialite', function () {
    return view('pages.politique-confidentialite');
})->name('politique-confidentialite');

Route::get('/politique-cookies', function () {
    return view('pages.politique-cookies');
})->name('politique-cookies');

Route::get('/mentions-legales', function () {
    return view('pages.mentions-legales');
})->name('mentions-legales');

Route::get('/cgv', function () {
    return view('pages.cgv');
})->name('cgv');

Route::get('/mentions-legales', function () {
    return view('pages.mentions-legales');
})->name('mentions-legales');

// Authentification
Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login');

Route::get('/register', function () {
    return view('pages.auth.register');
})->name('register');

// Authentification Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', function () {
        return view('pages.auth.admin-login');
    })->name('login');
    
    // Routes protégées admin (authentification + rôle administrateur)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/traffic', [TrafficStatsController::class, 'index'])->name('traffic');
        
        // Routes Villas
        Route::get('/villas', [AdminVillaController::class, 'index'])->name('villas');
        Route::get('/villas/create', [AdminVillaController::class, 'create'])->name('villas.create');
        Route::post('/villas', [AdminVillaController::class, 'store'])->name('villas.store');
        Route::get('/villas/{id}/blocked-dates', [AdminVillaController::class, 'blockedDates'])->name('villas.blocked-dates');
        Route::get('/villas/{id}/edit', [AdminVillaController::class, 'edit'])->name('villas.edit');
        Route::put('/villas/{id}', [AdminVillaController::class, 'update'])->name('villas.update');
        Route::delete('/villas/{id}', [AdminVillaController::class, 'destroy'])->name('villas.destroy');
        Route::post('/villas/{id}/photos', [AdminVillaController::class, 'uploadPhotos'])->name('villas.photos.upload');
        Route::post('/villas/{id}/photos/{photoId}/set-primary', [AdminVillaController::class, 'setPrimaryPhoto'])->name('villas.setPrimaryPhoto');
        Route::post('/villas/import-ical', [AdminVillaController::class, 'importIcal'])->name('villas.import-ical');
        
        // Routes Saisons
        Route::get('/seasons/{id}', [App\Http\Controllers\Admin\SeasonController::class, 'show'])->name('seasons.show');
        Route::post('/seasons', [App\Http\Controllers\Admin\SeasonController::class, 'store'])->name('seasons.store');
        Route::put('/seasons/{id}', [App\Http\Controllers\Admin\SeasonController::class, 'update'])->name('seasons.update');
        Route::delete('/seasons/{id}', [App\Http\Controllers\Admin\SeasonController::class, 'destroy'])->name('seasons.destroy');

        // Routes Destinations (Îles)
        Route::get("/islands/create", [IslandController::class, "create"])->name("islands.create");
        Route::post("/islands", [IslandController::class, "store"])->name("islands.store");
        Route::get("/islands", [IslandController::class, "index"])->name("islands");
        Route::get("/islands/{id}/edit", [IslandController::class, "edit"])->name("islands.edit");
        Route::put("/islands/{id}", [IslandController::class, "update"])->name("islands.update");
        
        // Routes Réservations
        Route::get('/reservations', [AdminReservationController::class, 'index'])->name('reservations');
        Route::get('/reservations/create', [AdminReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations/calculate-price', [AdminReservationController::class, 'calculatePrice'])->name('reservations.calculate-price');
        Route::post('/reservations', [AdminReservationController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{id}', [AdminReservationController::class, 'show'])->name('reservations.show');
        Route::get('/reservations/{id}/edit', [AdminReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{id}', [AdminReservationController::class, 'update'])->name('reservations.update');
        Route::post('/reservations/{id}/cancel', [AdminReservationController::class, 'cancel'])->name('reservations.cancel');
        
        // Routes Calendrier
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
        Route::get('/calendar/global', [CalendarController::class, 'global'])->name('calendar.global');
        Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
        Route::get('/calendar/events/global', [CalendarController::class, 'getGlobalEvents'])->name('calendar.events.global');
        
        // Routes Paiements
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments');
        Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('payments.export');
        Route::get('/payments/{id}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{id}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');
        
        // Routes Documents (Admin)
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/reservation/{reservation}/contract', [DocumentController::class, 'generateContract'])->name('contract');
            Route::get('/reservation/{reservation}/invoice', [DocumentController::class, 'generateInvoice'])->name('invoice');
            Route::get('/reservation/{reservation}/receipt-deposit/{payment}', [DocumentController::class, 'generateReceiptDeposit'])->name('receipt-deposit');
            Route::get('/reservation/{reservation}/receipt-balance/{payment}', [DocumentController::class, 'generateReceiptBalance'])->name('receipt-balance');
            Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
            Route::post('/reservation/{reservation}/generate', [DocumentController::class, 'generate'])->name('generate');
            Route::post('/{document}/sign', [DocumentController::class, 'sign'])->name('sign');
        });
        
        // Routes Clients
        Route::get('/promo-codes', [PromoCodeController::class, 'index'])->name('promo-codes.index');
        Route::get('/promo-codes/create', [PromoCodeController::class, 'create'])->name('promo-codes.create');
        Route::post('/promo-codes', [PromoCodeController::class, 'store'])->name('promo-codes.store');
        Route::get('/promo-codes/{promoCode}/edit', [PromoCodeController::class, 'edit'])->name('promo-codes.edit');
        Route::put('/promo-codes/{promoCode}', [PromoCodeController::class, 'update'])->name('promo-codes.update');
        Route::delete('/promo-codes/{promoCode}', [PromoCodeController::class, 'destroy'])->name('promo-codes.destroy');
        Route::patch('/promo-codes/{promoCode}/toggle', [PromoCodeController::class, 'toggle'])->name('promo-codes.toggle');

        Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
        Route::post('/equipments', [EquipmentController::class, 'store'])->name('equipments.store');
        Route::put('/equipments/{equipment}', [EquipmentController::class, 'update'])->name('equipments.update');
        Route::patch('/equipments/{equipment}/toggle-search-filter', [EquipmentController::class, 'toggleSearchFilter'])->name('equipments.toggle-search-filter');
        Route::delete('/equipments/{equipment}', [EquipmentController::class, 'destroy'])->name('equipments.destroy');

        Route::get('/villa-reviews', [AdminVillaReviewController::class, 'index'])->name('villa-reviews.index');
        Route::get('/villa-reviews/{villaReview}', [AdminVillaReviewController::class, 'show'])->name('villa-reviews.show');
        Route::post('/villa-reviews/{villaReview}/approve', [AdminVillaReviewController::class, 'approve'])->name('villa-reviews.approve');
        Route::post('/villa-reviews/{villaReview}/reject', [AdminVillaReviewController::class, 'reject'])->name('villa-reviews.reject');
        Route::put('/villa-reviews/{villaReview}/response', [AdminVillaReviewController::class, 'updateResponse'])->name('villa-reviews.response');

        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients');
        Route::get('/clients/create', [AdminClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
        Route::post('/clients/{client}/documents', [\App\Http\Controllers\Admin\ClientDocumentController::class, 'store'])->name('clients.documents.store');
        Route::put('/clients/{client}/documents/{document}', [\App\Http\Controllers\Admin\ClientDocumentController::class, 'update'])->name('clients.documents.update');
        Route::delete('/clients/{client}/documents/{document}', [\App\Http\Controllers\Admin\ClientDocumentController::class, 'destroy'])->name('clients.documents.destroy');
        Route::get('/clients/{client}/documents/{document}/download', [\App\Http\Controllers\Admin\ClientDocumentController::class, 'download'])->name('clients.documents.download');
        Route::get('/clients/{client}', [AdminClientController::class, 'show'])->name('clients.show');
        Route::post('/clients/{client}/resend-invitation', [AdminClientController::class, 'resendInvitation'])->name('clients.resend-invitation');
        Route::post('/clients/{client}/send-promo-code', [AdminClientController::class, 'sendPromoCode'])->name('clients.send-promo-code');
        Route::post('/clients/{client}/open-promo-code-whatsapp', [AdminClientController::class, 'openPromoCodeWhatsapp'])->name('clients.open-promo-code-whatsapp');
        Route::post('/clients/{client}/toggle-status', [AdminClientController::class, 'toggleStatus'])->name('clients.toggle-status');
        Route::put('/clients/{client}/privilege-club', [AdminClientController::class, 'updatePrivilegeClub'])->name('clients.privilege-club.update');
        Route::post('/clients/{client}/privilege-club/recalculate', [AdminClientController::class, 'recalculatePrivilegeClub'])->name('clients.privilege-club.recalculate');
        Route::get('/clients/{client}/privilege-club/notifications/{notification}/whatsapp-open', [AdminClientController::class, 'openPrivilegeClubWhatsapp'])->name('clients.privilege-club.whatsapp-open');
        Route::post('/clients/{client}/privilege-club/notifications/{notification}/whatsapp-sent', [AdminClientController::class, 'markPrivilegeClubWhatsappSent'])->name('clients.privilege-club.whatsapp-sent');

        // Routes Synchronisation
        Route::get('/synchronization', [SynchronizationController::class, 'index'])->name('synchronization');
        Route::get('/synchronization/config', [SynchronizationController::class, 'config'])->name('synchronization.config');
        Route::get('/synchronization/chart-data', [SynchronizationController::class, 'getChartDataApi'])->name('synchronization.chart-data');
        Route::get('/synchronization/ical/{villaId}', [SynchronizationController::class, 'exportIcal'])->name('synchronization.ical.export');
        Route::post('/synchronization/force-sync', [SynchronizationController::class, 'forceSync'])->name('synchronization.force-sync');
        Route::post('/synchronization/configs', [SynchronizationController::class, 'storeConfig'])->name('synchronization.configs.store');
        Route::get('/synchronization/configs/{id}', [SynchronizationController::class, 'showConfig'])->name('synchronization.configs.show');
        Route::delete('/synchronization/configs/{id}', [SynchronizationController::class, 'deleteConfig'])->name('synchronization.configs.delete');
        
        // Routes Paramètres
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/history', [SettingsController::class, 'history'])->name('settings.history');
        
        // Routes Gestion des Administrateurs
        Route::post('/settings/admins', [SettingsController::class, 'storeAdmin'])->name('settings.admins.store');
        Route::put('/settings/admins/{user}', [SettingsController::class, 'updateAdmin'])->name('settings.admins.update');
        Route::delete('/settings/admins/{user}', [SettingsController::class, 'destroyAdmin'])->name('settings.admins.destroy');
        Route::get('/settings/admins/{user}', [SettingsController::class, 'showAdmin'])->name('settings.admins.show');
        
        // Routes Politiques d'Annulation
        Route::post('/cancellation-policies', [CancellationPolicyController::class, 'store'])->name('cancellation-policies.store');
        Route::put('/cancellation-policies/{cancellationPolicy}', [CancellationPolicyController::class, 'update'])->name('cancellation-policies.update');
        Route::delete('/cancellation-policies/{cancellationPolicy}', [CancellationPolicyController::class, 'destroy'])->name('cancellation-policies.destroy');
        Route::get('/cancellation-policies/{cancellationPolicy}', [CancellationPolicyController::class, 'show'])->name('cancellation-policies.show');
        
        // Routes Messagerie
        Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages');
        Route::post('/messages/send', [AdminMessageController::class, 'sendMessage'])->name('messages.send');
        
        // Routes Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
            Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        });
    });
});

// Réinitialisation de mot de passe
Route::get('/forgot-password', function () {
    return view('pages.auth.forgot-password');
})->name('password.request');

Route::get('/reset-password/{token}', function ($token) {
    return view('pages.auth.reset-password', ['token' => $token]);
})->name('password.reset');
