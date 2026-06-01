<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Models\Island;
use Illuminate\Http\Request;

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
        
        return view('pages.home', compact('featuredVillas', 'destinations', 'islands'));
    }
}

