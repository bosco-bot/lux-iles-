<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Mettre à jour le profil de l'utilisateur
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            

            $validator = Validator::make($request->all(), [
                'first_name' => ['sometimes', 'string', 'max:100'],
                'last_name' => ['sometimes', 'string', 'max:100'],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string'],
                'city' => ['nullable', 'string', 'max:100'],
                'postal_code' => ['nullable', 'string', 'max:20'],
                'country' => ['nullable', 'string', 'max:100'],
                'birth_date' => ['nullable', 'date'],
                'nationality' => ['nullable', 'string', 'max:100'],
                'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mettre à jour les informations du profil
            $updateData = [];
            
            if ($request->has('first_name')) {
                $updateData['first_name'] = $request->first_name;
            }
            if ($request->has('last_name')) {
                $updateData['last_name'] = $request->last_name;
            }
            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }
            if ($request->has('phone')) {
                $updateData['phone'] = $request->phone;
            }
            if ($request->has('address')) {
                $updateData['address'] = $request->address;
            }
            if ($request->has('city')) {
                $updateData['city'] = $request->city;
            }
            if ($request->has('postal_code')) {
                $updateData['postal_code'] = $request->postal_code;
            }
            if ($request->has('country')) {
                $updateData['country'] = $request->country;
            }
            if ($request->has('birth_date')) {
                $updateData['birth_date'] = $request->birth_date;
            }
            if ($request->has('nationality')) {
                $updateData['nationality'] = $request->nationality;
            }

            // Gérer l'upload de la photo
            if ($request->hasFile('photo')) {
                try {
                    $file = $request->file('photo');
                    
                    // Supprimer l'ancienne photo si elle existe
                    if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                        Storage::disk('public')->delete($user->photo_url);
                    }

                    // Stocker la nouvelle photo
                    $photoPath = $file->store('profiles', 'public');
                    $updateData['photo_url'] = $photoPath;
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de l\'upload de la photo', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => $user->id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur lors de l\'upload de la photo: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Mettre à jour l'utilisateur
            $user->update($updateData);

            // Recharger l'utilisateur pour avoir les dernières données
            $user->refresh();

            $photoUrl = null;
            if ($user->photo_url) {
                $photoUrl = asset('storage/' . $user->photo_url);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo_url' => $photoUrl,
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProfileController::update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['password', 'photo'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

