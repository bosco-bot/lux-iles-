<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Services\VillaAvailabilityContext;
use App\Services\VillaAvailabilityService;
use App\Models\Island;
use App\Models\Equipment;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;

class VillaController extends Controller
{
    /**
     * Afficher la liste des villas
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $islandId = $request->query('island');
        $priceMax = $request->query('price_max');
        $bedrooms = $request->query('bedrooms');
        $bathrooms = $request->query('bathrooms');
        $capacity = $request->query('capacity');
        $surface = $request->query('surface');
        $equipments = $request->query('equipments', []);
        $featuredOnly = $request->query('featured_only');
        $sortBy = $request->query('sort', 'recommended');
        
        // Construire la requête
        $query = Villa::with(['island', 'photos', 'equipments'])
            ->where('is_active', true);
        
        // Filtrer par île si spécifié
        if ($islandId) {
            $query->where('island_id', $islandId);
        }
        
        // Filtrer par prix maximum si spécifié
        if ($priceMax) {
            $query->where('base_price_per_night', '<=', $priceMax);
        }
        
        // Filtrer par nombre de chambres si spécifié
        if ($bedrooms) {
            $query->where('bedrooms', '>=', $bedrooms);
        }
        
        // Filtrer par nombre de salles de bain si spécifié
        if ($bathrooms) {
            $query->where('bathrooms', '>=', $bathrooms);
        }
        
        // Filtrer par capacité minimale si spécifié
        if ($capacity) {
            $query->where('max_capacity', '>=', $capacity);
        }
        
        // Filtrer par surface minimale si spécifié
        if ($surface) {
            $query->where('surface_area', '>=', $surface);
        }
        
        // Filtrer par équipements si spécifié (§3.5 — uniquement les IDs autorisés comme filtres)
        if (!empty($equipments)) {
            $equipmentIds = is_array($equipments) ? $equipments : explode(',', $equipments);
            $equipmentIds = array_filter($equipmentIds);

            $allowedFilterIds = Equipment::searchFilters()->pluck('id')->map(fn ($id) => (string) $id)->all();
            $equipmentIds = array_values(array_intersect(
                array_map('strval', $equipmentIds),
                $allowedFilterIds
            ));

            if (! empty($equipmentIds)) {
                $query->whereHas('equipments', function ($q) use ($equipmentIds) {
                    $q->whereIn('equipments.id', $equipmentIds);
                });
            }
        }
        
        // Filtrer uniquement les villas mises en avant si spécifié
        if ($featuredOnly) {
            $query->where('is_featured', true);
        }
        
        // Trier selon le paramètre
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('base_price_per_night', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price_per_night', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'capacity_asc':
                $query->orderBy('max_capacity', 'asc');
                break;
            case 'capacity_desc':
                $query->orderBy('max_capacity', 'desc');
                break;
            case 'bedrooms_asc':
                $query->orderBy('bedrooms', 'asc');
                break;
            case 'bedrooms_desc':
                $query->orderBy('bedrooms', 'desc');
                break;
            default: // 'recommended'
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('sort_order', 'asc')
                      ->orderBy('created_at', 'desc');
                break;
        }
        
        // Pagination
        $villas = $query->paginate(12);
        
        // Récupérer toutes les îles pour les filtres
        $islands = Island::orderBy('name', 'asc')->get();
        
        // Équipements proposables en filtres de recherche uniquement (§3.5 CDC)
        $allEquipments = Equipment::searchFilters()->orderBy('name', 'asc')->get();
        
        // Récupérer le prix maximum pour les filtres
        $maxPrice = Villa::where('is_active', true)->max('base_price_per_night') ?? 10000;
        
        // Convertir equipments en tableau si c'est une chaîne
        $selectedEquipments = [];
        if (!empty($equipments)) {
            if (is_string($equipments)) {
                $selectedEquipments = explode(',', $equipments);
            } elseif (is_array($equipments)) {
                $selectedEquipments = $equipments;
            }
        }
        
        return view('pages.villas', compact('villas', 'islands', 'allEquipments', 'islandId', 'sortBy', 'priceMax', 'maxPrice', 'bedrooms', 'bathrooms', 'capacity', 'surface', 'selectedEquipments', 'featuredOnly'));
    }

    /**
     * Afficher les détails d'une villa
     */
    public function show($id, VillaAvailabilityService $availability)
    {
        $villa = Villa::with(['island', 'photos', 'equipments', 'availabilityBlocks'])
            ->where('is_active', true)
            ->findOrFail($id);

        $publishedReviews = $villa->publishedReviews()
            ->with('user:id,first_name,last_name,photo_url')
            ->get();

        $averageRating = $villa->averageRating();
        $reviewsCount = $villa->publishedReviewsCount();

        $publicAvailability = VillaAvailabilityContext::publicSite();
        $blockedDates = $availability->getBlockedDates($villa->id, null, $publicAvailability);
        $reservations = $availability->getReservationsForCalendar($villa->id, $publicAvailability);

        // Récupérer la photo principale
        $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
            ?? $villa->photos->first();

        // Récupérer les autres photos
        $otherPhotos = $villa->photos->where('id', '!=', $primaryPhoto->id ?? null)->take(2);

        // Récupérer les paramètres globaux pour le JavaScript
        $globalTaxRate = SettingsHelper::get('global_tax_rate', 8.5);
        $touristTaxPerNight = SettingsHelper::get('tourist_tax_per_night', 2.50);
        $touristTaxEnabled = SettingsHelper::get('tourist_tax_enabled', true);
        $depositPercentage = SettingsHelper::get('deposit_percentage_min', 30);
        $depositPercentageMax = SettingsHelper::get('deposit_percentage_max', 50);

        return view('pages.villa-detail', compact(
            'villa',
            'primaryPhoto',
            'otherPhotos',
            'reservations',
            'blockedDates',
            'globalTaxRate',
            'touristTaxPerNight',
            'touristTaxEnabled',
            'depositPercentage',
            'depositPercentageMax',
            'publishedReviews',
            'averageRating',
            'reviewsCount'
        ));
    }
}


