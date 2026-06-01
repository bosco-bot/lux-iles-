<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Afficher la page de contact
     */
    public function index()
    {
        return view('pages.contact');
    }

    /**
     * Traiter l'envoi du formulaire de contact
     */
    public function send(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
            ]);
        } catch (ValidationException $e) {
            // Gérer les erreurs de validation pour les requêtes AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veuillez corriger les erreurs dans le formulaire.',
                    'errors' => $e->errors()
                ], 422);
            }
            // Pour les requêtes normales, Laravel redirige automatiquement avec les erreurs
            throw $e;
        }

        try {
            // Récupérer l'email de contact depuis les paramètres globaux
            $contactEmail = SettingsHelper::get('company_email', 'contact.luxiles@gmail.com');
            
            // Préparer les données pour l'email
            $emailData = [
                'firstName' => $validated['first_name'],
                'lastName' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'submittedAt' => now()->format('d/m/Y à H:i'),
            ];

            // Envoyer l'email de notification à l'admin
            $this->emailService->sendContactNotification($contactEmail, $emailData);

            Log::info('Message de contact envoyé', [
                'from' => $validated['email'],
                'subject' => $validated['subject'],
            ]);

            // Retourner une réponse JSON pour AJAX ou rediriger avec message de succès
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
                ]);
            }

            return redirect()->route('contact.index')
                ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du message de contact', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? 'N/A',
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.',
                ], 500);
            }

            return redirect()->route('contact.index')
                ->with('error', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.');
        }
    }
}

