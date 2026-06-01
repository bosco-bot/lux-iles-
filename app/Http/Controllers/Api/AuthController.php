<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'phone' => ['nullable', 'string', 'max:20'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'country' => 'France',
                'is_active' => true,
            ]);

            // Connexion automatique après inscription
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Compte créé avec succès',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ],
                'redirect' => route('espace-client.index')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du compte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
                'remember' => ['sometimes', 'boolean'],
                'is_admin_attempt' => ['sometimes', 'boolean'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            // Vérifier les identifiants sans authentifier (pour éviter la régénération de session)
            if (Auth::validate($credentials)) {
                $user = \App\Models\User::where('email', $credentials['email'])->first();

                // Vérifier si le compte est actif
                if (!$user->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Votre compte a été désactivé. Veuillez contacter le support.'
                    ], 403);
                }

                // Vérifier si l'utilisateur est administrateur (sauf si c'est une tentative admin)
                $isAdminAttempt = $request->boolean('is_admin_attempt') || $request->has('is_admin_attempt');
                if ($user->is_admin && !$isAdminAttempt) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Les administrateurs doivent utiliser la page de connexion administrateur.'
                    ], 403);
                }

                // Authentifier l'utilisateur et régénérer la session seulement après toutes les validations
                Auth::login($user, $remember);
                $request->session()->regenerate();

                // Mettre à jour la dernière connexion
                $user->last_login_at = now();
                $user->save();

                $photoUrl = null;
                if ($user->photo_url) {
                    $photoUrl = asset('storage/' . $user->photo_url);
                }

                // Déterminer la redirection selon le type d'utilisateur
                // Vérifier s'il y a une URL "intended" dans la session (redirection après connexion)
                $intendedUrl = session()->get('intended') ?: session()->get('intended_url');
                if ($intendedUrl && !$user->is_admin) {
                    // Nettoyer la session intended après utilisation
                    session()->forget('intended');
                    session()->forget('intended_url');
                    $redirect = $intendedUrl;
                } else {
                    $redirect = $user->is_admin ? route('admin.dashboard') : route('espace-client.index');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'photo_url' => $photoUrl,
                        'is_admin' => $user->is_admin ?? false,
                    ],
                    'redirect' => $redirect
                ], 200);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants incorrects'
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déconnexion d'un utilisateur
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie',
                'redirect' => route('home')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer l'utilisateur connecté
     */
    public function user(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $photoUrl = null;
            if ($user->photo_url) {
                $photoUrl = asset('storage/' . $user->photo_url);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo_url' => $photoUrl,
                    'is_admin' => $user->is_admin ?? false,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Demander la réinitialisation du mot de passe
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email', 'exists:users,email'],
            ], [
                'email.exists' => 'Aucun compte n\'est associé à cet email.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun compte n\'est associé à cet email.'
                ], 404);
            }

            // Générer un token de réinitialisation
            $token = \Str::random(64);
            
            // Stocker le token dans la base de données (table password_reset_tokens)
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => \Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Envoyer l'email de réinitialisation (en arrière-plan)
            try {
                \App\Jobs\SendPasswordResetEmailJob::dispatch($user, $token);
            } catch (\Exception $e) {
                // Log l'erreur mais ne bloque pas le processus
                \Log::error('Erreur lors de l\'envoi de l\'email de réinitialisation: ' . $e->getMessage());
            }

            // Retourner sans le token (sécurité)
            return response()->json([
                'success' => true,
                'message' => 'Un email de réinitialisation a été envoyé à votre adresse.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la demande de réinitialisation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required', 'string'],
                'email' => ['required', 'email', 'exists:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier le token
            $passwordReset = \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalide ou expiré.'
                ], 400);
            }

            // Vérifier que le token correspond (le token stocké est hashé)
            if (!\Hash::check($request->token, $passwordReset->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalide ou expiré.'
                ], 400);
            }

            // Vérifier que le token n'est pas expiré (24 heures)
            if (now()->diffInHours($passwordReset->created_at) > 24) {
                \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Le lien de réinitialisation a expiré. Veuillez faire une nouvelle demande.'
                ], 400);
            }

            // Mettre à jour le mot de passe
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->must_set_password = false;
            $user->save();

            // Supprimer le token utilisé
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Votre mot de passe a été réinitialisé avec succès.',
                'redirect' => route('login')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation du mot de passe',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

