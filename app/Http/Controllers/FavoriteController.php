<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Afficher la liste des favoris de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les villas favorites avec leurs relations
        $favoriteVillas = $user->favoriteVillas()
            ->with(['photos', 'island'])
            ->orderBy('favorites.created_at', 'desc')
            ->get();
        
        return view('pages.favoris', compact('favoriteVillas'));
    }

    /**
     * Ajouter une villa aux favoris
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $villaId = $request->input('villa_id');
        
        // Vérifier que la villa existe
        $villa = Villa::findOrFail($villaId);
        
        // Vérifier si déjà en favoris
        if ($user->hasFavorite($villaId)) {
            return response()->json([
                'success' => false,
                'message' => 'Cette villa est déjà dans vos favoris'
            ], 400);
        }
        
        // Ajouter aux favoris
        Favorite::create([
            'user_id' => $user->id,
            'villa_id' => $villaId,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Villa ajoutée aux favoris',
            'is_favorite' => true
        ]);
    }

    /**
     * Supprimer une villa des favoris
     */
    public function destroy($villaId)
    {
        $user = Auth::user();
        
        // Vérifier que la villa est bien en favoris
        $favorite = Favorite::where('user_id', $user->id)
            ->where('villa_id', $villaId)
            ->first();
        
        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Cette villa n\'est pas dans vos favoris'
            ], 404);
        }
        
        $favorite->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Villa retirée des favoris',
            'is_favorite' => false
        ]);
    }

    /**
     * Toggle favori (ajouter ou supprimer)
     */
    public function toggle(Request $request)
    {
        $user = Auth::user();
        $villaId = $request->input('villa_id');
        
        // Vérifier que la villa existe
        $villa = Villa::findOrFail($villaId);
        
        $favorite = Favorite::where('user_id', $user->id)
            ->where('villa_id', $villaId)
            ->first();
        
        if ($favorite) {
            // Supprimer
            $favorite->delete();
            $isFavorite = false;
            $message = 'Villa retirée des favoris';
        } else {
            // Ajouter
            Favorite::create([
                'user_id' => $user->id,
                'villa_id' => $villaId,
            ]);
            $isFavorite = true;
            $message = 'Villa ajoutée aux favoris';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Vérifier si une villa est en favoris
     */
    public function check($villaId)
    {
        $user = Auth::user();
        $isFavorite = $user->hasFavorite($villaId);
        
        return response()->json([
            'is_favorite' => $isFavorite
        ]);
    }
}
