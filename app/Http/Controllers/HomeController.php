<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\Villa;
use App\Models\VillaReview;

class HomeController extends Controller
{
    /**
     * Afficher la page d'accueil
     */
    public function index()
    {
        // Récupérer les villas mises en avant et actives
        $featuredVillas = Villa::with(['island', 'photos'])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        // Récupérer les 3 premières destinations créées avec une image
        $destinations = Island::where(function($query) {
                $query->whereNotNull('image_path')
                      ->orWhereNotNull('image_url');
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();
        
        // Récupérer toutes les îles pour le formulaire de recherche
        $islands = Island::orderBy('name', 'asc')->get();

        // §3.4 CDC — témoignages accueil : 3 derniers avis publiés (modérés)
        $featuredReviews = VillaReview::query()
            ->approved()
            ->with([
                'user:id,first_name,last_name,city,country,photo_url',
                'villa:id,name,island_id',
                'villa.island:id,name',
            ])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('pages.home', compact('featuredVillas', 'destinations', 'islands', 'featuredReviews'));
    }
}

