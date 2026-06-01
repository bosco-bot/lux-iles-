<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\CancellationPolicy;
use App\Models\SettingsHistory;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        // Récupérer les paramètres globaux depuis la base de données
        $settings = [];
        if (DB::getSchemaBuilder()->hasTable('global_settings')) {
            $settings = DB::table('global_settings')
                ->pluck('value', 'key')
                ->toArray();
        }

        // Récupérer les administrateurs avec leurs rôles
        $admins = User::where('is_admin', true)
            ->with('roles')
            ->get();

        // Récupérer les rôles disponibles
        $roles = Role::orderBy('name')->get();

        // Récupérer les politiques d'annulation
        $policies = CancellationPolicy::orderBy('sort_order')->orderBy('name')->get();

        // Récupérer les saisons
        $seasons = \App\Models\Season::orderBy('start_date')->get();

        return view('pages.admin.settings', compact('settings', 'admins', 'roles', 'policies', 'seasons'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'global_tax_rate' => 'nullable|numeric|min:0|max:100',
            'tourist_tax_per_night' => 'nullable|numeric|min:0',
            'tourist_tax_enabled' => 'nullable',
            'service_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'deposit_percentage_min' => 'nullable|integer|min:0|max:100',
            'deposit_percentage_max' => 'nullable|integer|min:0|max:100',
            'balance_due_days_before_checkin' => 'nullable|integer|min:0',
            'deposit_guarantee_days_before_checkin' => 'nullable|integer|min:0',
            'cancellation_policy_days' => 'nullable|integer|min:0',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'company_siret' => 'nullable|string|max:20',
            'company_vat' => 'nullable|string|max:20',
        ]);

        // Convertir la checkbox en boolean
        $validated['tourist_tax_enabled'] = $request->has('tourist_tax_enabled') ? true : false;

        // Sauvegarder les paramètres dans la table global_settings
        if (DB::getSchemaBuilder()->hasTable('global_settings')) {
            foreach ($validated as $key => $newValue) {
                // Récupérer l'ancienne valeur
                $oldSetting = DB::table('global_settings')->where('key', $key)->first();
                $oldValue = $oldSetting ? $oldSetting->value : null;

                // Mettre à jour ou créer le paramètre
                DB::table('global_settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $newValue, 'updated_at' => now()]
                );

                // Enregistrer dans l'historique si la valeur a changé
                if ($oldValue != $newValue) {
                    SettingsHistory::create([
                        'setting_key' => $key,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                        'changed_by' => auth()->id(),
                        'change_type' => $oldValue === null ? 'create' : 'update',
                    ]);
                }

                // Vider le cache pour ce paramètre
                SettingsHelper::clearCache();
            }
        }

        // Clear application cache after settings update
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        return redirect()->route('admin.settings')
            ->with('success', 'Paramètres mis à jour avec succès');
    }

    /**
     * Afficher l'historique des modifications des paramètres
     */
    public function history()
    {
        $history = SettingsHistory::with('changedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($history);
    }

    /**
     * Créer un nouvel administrateur
     */
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $userData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'is_admin' => true,
            'is_active' => true,
        ];

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
            $userData['photo_url'] = $photoPath;
        }

        $user = User::create($userData);

        // Assigner le rôle
        $user->roles()->attach($validated['role_id']);

        return redirect()->route('admin.settings')
            ->with('success', 'Administrateur créé avec succès.');
    }

    /**
     * Mettre à jour un administrateur
     */
    public function updateAdmin(Request $request, User $user)
    {
        // Vérifier que c'est un admin
        if (!$user->is_admin) {
            return redirect()->route('admin.settings')
                ->with('error', 'Cet utilisateur n\'est pas un administrateur.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];

        // Mettre à jour le mot de passe si fourni
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }

            // Stocker la nouvelle photo
            $photoPath = $request->file('photo')->store('profiles', 'public');
            $updateData['photo_url'] = $photoPath;
        }

        $user->update($updateData);

        // Mettre à jour le rôle
        $user->roles()->sync([$validated['role_id']]);

        return redirect()->route('admin.settings')
            ->with('success', 'Administrateur mis à jour avec succès.');
    }

    /**
     * Supprimer un administrateur
     */
    public function destroyAdmin(User $user)
    {
        // Vérifier que c'est un admin
        if (!$user->is_admin) {
            return redirect()->route('admin.settings')
                ->with('error', 'Cet utilisateur n\'est pas un administrateur.');
        }

        // Ne pas supprimer l'utilisateur actuel
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.settings')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Retirer les rôles
        $user->roles()->detach();

        // Désactiver au lieu de supprimer
        $user->update(['is_admin' => false, 'is_active' => false]);

        return redirect()->route('admin.settings')
            ->with('success', 'Administrateur retiré avec succès.');
    }

    /**
     * Récupérer un administrateur pour édition (AJAX)
     */
    public function showAdmin(User $user)
    {
        if (!$user->is_admin) {
            return response()->json(['error' => 'Cet utilisateur n\'est pas un administrateur.'], 404);
        }

        $user->load('roles');
        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'role_id' => $user->roles->first()?->id,
            'photo_url' => $user->photo_url ? asset('storage/' . $user->photo_url) : null,
        ]);
    }
}

