<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes d'authentification publiques (avec middleware web pour CSRF)
Route::middleware('web')->prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
});

// Routes d'authentification protégées (avec middleware web et auth)
Route::middleware(['web', 'auth'])->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.auth.user');
});

// Routes de profil protégées
Route::middleware(['web', 'auth'])->prefix('profile')->group(function () {
    Route::post('/update', [\App\Http\Controllers\Api\ProfileController::class, 'update'])->name('api.profile.update');
});

// Routes de paiement protégées
Route::middleware(['web', 'auth'])->prefix('payments')->group(function () {
    Route::post('/create-intent', [\App\Http\Controllers\Api\PaymentController::class, 'createIntent'])->name('api.payments.create-intent');
    Route::post('/confirm', [\App\Http\Controllers\Api\PaymentController::class, 'confirm'])->name('api.payments.confirm');
    Route::get('/status', [\App\Http\Controllers\Api\PaymentController::class, 'status'])->name('api.payments.status');
});// Route webhook Stripe (publique, sans CSRF)
Route::post('/payments/webhook/stripe', [\App\Http\Controllers\Api\PaymentController::class, 'webhook'])->name('api.payments.webhook.stripe');