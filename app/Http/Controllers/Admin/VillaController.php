<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use App\Models\VillaPhoto;
use App\Models\VillaAvailabilityBlock;
use App\Models\VillaSeasonalPrice;
use App\Services\VillaAvailabilityContext;
use App\Services\VillaAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VillaController extends Controller
{
    /**
     * Afficher la liste des villas
     */
    public function index()
    {
        $villas = Villa::with(['island', 'photos', 'equipments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => Villa::count(),
            'active' => Villa::where('is_active', true)->count(),
            'inactive' => Villa::where('is_active', false)->count(),
            'featured' => Villa::where('is_featured', true)->count(),
        ];
        
        return view('pages.admin.villas', compact('villas', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $islands = \App\Models\Island::all();
        $equipments = \App\Models\Equipment::all();
        $seasons = \App\Models\Season::where('is_active', true)->orderBy('start_date')->get();
        
        return view('pages.admin.villas.create', compact('islands', 'equipments', 'seasons'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $villa = Villa::with(['island', 'photos', 'equipments', 'availabilityBlocks', 'seasonalPrices.season'])->findOrFail($id);
        $islands = \App\Models\Island::all();
        $equipments = \App\Models\Equipment::all();
        $seasons = \App\Models\Season::where('is_active', true)->orderBy('start_date')->get();
        
        return view('pages.admin.villas.create', compact('villa', 'islands', 'equipments', 'seasons'));
    }

    /**
     * Enregistrer une nouvelle villa
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'island_id' => 'required|exists:islands,id',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1',
            'surface_area' => 'nullable|integer|min:0',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'base_price_per_night' => 'required|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'service_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'deposit_amount' => 'nullable|numeric|min:0',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'minimum_stay_nights' => 'nullable|integer|min:1',
            'status' => 'required|in:active,maintenance',
            'is_featured' => 'boolean',
            'equipments' => 'nullable|array',
            'equipments.*' => 'string',
            'blocked_periods' => 'nullable|string', // JSON string
            'seasonal_prices' => 'nullable|array',
            'seasonal_prices.*' => 'array',
            'seasonal_prices.*.season_id' => 'required_with:seasonal_prices.*|exists:seasons,id',
            'seasonal_prices.*.price_per_night' => 'required_with:seasonal_prices.*|numeric|min:0',
            'seasonal_prices.*.currency' => 'nullable|string|max:3',
            'seasonal_prices.*.id' => 'nullable|exists:villa_seasonal_prices,id',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:10240', // 10MB max par photo
        ]);

        DB::beginTransaction();
        try {
            // Créer la villa
            $villa = Villa::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'island_id' => $validated['island_id'],
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'max_capacity' => $validated['max_capacity'],
                'surface_area' => $validated['surface_area'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'base_price_per_night' => $validated['base_price_per_night'],
                'cleaning_fee' => $validated['cleaning_fee'] ?? 0.00,
                'service_fee_percentage' => $validated['service_fee_percentage'] ?? 0.00,
                'deposit_amount' => $validated['deposit_amount'] ?? 0.00,
                'check_in_time' => $validated['check_in_time'] ?? '16:00:00',
                'check_out_time' => $validated['check_out_time'] ?? '10:00:00',
                'minimum_stay_nights' => $validated['minimum_stay_nights'] ?? 3,
                'is_active' => $validated['status'] === 'active',
                'is_featured' => $request->has('is_featured'),
                'currency' => 'EUR',
            ]);

            // Attacher les équipements (mapper les noms aux IDs)
            if (!empty($request->input('equipments'))) {
                $equipmentNames = $request->input('equipments');
                $equipmentIds = [];
                
                // Mapping des noms aux équipements
                $equipmentMap = [
                    'piscine-debordement' => 'Piscine',
                    'jacuzzi-prive' => 'Jacuzzi',
                    'acces-plage' => 'Plage privée',
                    'climatisation' => 'Climatisation',
                    'wifi' => 'WiFi',
                    'salle-sport' => 'Salle de sport',
                ];
                
                foreach ($equipmentNames as $name) {
                    $searchName = $equipmentMap[$name] ?? $name;
                    $equipment = \App\Models\Equipment::where('name', 'LIKE', '%' . $searchName . '%')->first();
                    if ($equipment) {
                        $equipmentIds[] = $equipment->id;
                    }
                }
                
                if (!empty($equipmentIds)) {
                    $villa->equipments()->attach($equipmentIds);
                }
            }

            // Traiter les périodes bloquées
            if (!empty($request->input('blocked_periods'))) {
                $blockedPeriods = json_decode($request->input('blocked_periods'), true);
                if (is_array($blockedPeriods)) {
                    foreach ($blockedPeriods as $period) {
                        VillaAvailabilityBlock::create([
                            'villa_id' => $villa->id,
                            'start_date' => $period['start'],
                            'end_date' => $period['end'],
                            'reason' => $period['reason'] ?? 'Bloqué manuellement',
                        ]);
                    }
                }
            }

            // Traiter les tarifs saisonniers
            if ($request->has('seasonal_prices')) {
                $seasonalPrices = $request->input('seasonal_prices');
                foreach ($seasonalPrices as $key => $seasonalPrice) {
                    if (!empty($seasonalPrice['season_id']) && !empty($seasonalPrice['price_per_night'])) {
                        VillaSeasonalPrice::create([
                            'villa_id' => $villa->id,
                            'season_id' => $seasonalPrice['season_id'],
                            'price_per_night' => $seasonalPrice['price_per_night'],
                            'currency' => $seasonalPrice['currency'] ?? 'EUR',
                        ]);
                    }
                }
            }

            // Traiter les photos uploadées
            if ($request->hasFile('photos')) {
                $validPhotos = array_filter($request->file('photos'), function($photo) {
                    return $photo && $photo->isValid();
                });
                
                $primaryPhotoId = $request->input('primary_photo_id');
                $hasExistingPrimary = false; // Pour la création, pas de photos existantes
                
                foreach ($validPhotos as $index => $photo) {
                    if ($photo && $photo->isValid()) {
                        $path = $photo->store('villas/' . $villa->id, 'public');
                        
                        // La première photo est principale par défaut lors de la création
                        $isPrimary = ($index === 0);
                        
                        VillaPhoto::create([
                            'villa_id' => $villa->id,
                            'file_path' => $path,
                            'file_name' => $photo->getClientOriginalName(),
                            'alt_text' => $villa->name,
                            'is_primary' => $isPrimary,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.villas')
                ->with('success', 'Villa créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la villa : ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une villa existante
     */
    public function update(Request $request, $id)
    {
        $villa = Villa::findOrFail($id);
        
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'island_id' => 'required|exists:islands,id',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1',
            'surface_area' => 'nullable|integer|min:0',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'base_price_per_night' => 'required|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'service_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'deposit_amount' => 'nullable|numeric|min:0',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'minimum_stay_nights' => 'nullable|integer|min:1',
            'status' => 'required|in:active,maintenance',
            'is_featured' => 'boolean',
            'equipments' => 'nullable|array',
            'equipments.*' => 'string',
            'blocked_periods' => 'nullable|string',
            'seasonal_prices' => 'nullable|array',
            'seasonal_prices.*' => 'array',
            'seasonal_prices.*.season_id' => 'required_with:seasonal_prices.*|exists:seasons,id',
            'seasonal_prices.*.price_per_night' => 'required_with:seasonal_prices.*|numeric|min:0',
            'seasonal_prices.*.currency' => 'nullable|string|max:3',
            'seasonal_prices.*.id' => 'nullable|exists:villa_seasonal_prices,id',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:10240', // 10MB max par photo
            'deleted_photos' => 'nullable|array',
            'deleted_photos.*' => 'integer',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour la villa
            $villa->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'island_id' => $validated['island_id'],
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'max_capacity' => $validated['max_capacity'],
                'surface_area' => $validated['surface_area'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'base_price_per_night' => $validated['base_price_per_night'],
                'cleaning_fee' => $validated['cleaning_fee'] ?? 0.00,
                'service_fee_percentage' => $validated['service_fee_percentage'] ?? 0.00,
                'deposit_amount' => $validated['deposit_amount'] ?? 0.00,
                'check_in_time' => $validated['check_in_time'] ?? '16:00:00',
                'check_out_time' => $validated['check_out_time'] ?? '10:00:00',
                'minimum_stay_nights' => $validated['minimum_stay_nights'] ?? 3,
                'is_active' => $validated['status'] === 'active',
                'is_featured' => $request->has('is_featured'),
            ]);

            // Synchroniser les équipements
            if (!empty($request->input('equipments'))) {
                $equipmentNames = $request->input('equipments');
                $equipmentIds = [];
                
                $equipmentMap = [
                    'piscine-debordement' => 'Piscine',
                    'jacuzzi-prive' => 'Jacuzzi',
                    'acces-plage' => 'Plage privée',
                    'climatisation' => 'Climatisation',
                    'wifi' => 'WiFi',
                    'salle-sport' => 'Salle de sport',
                ];
                
                foreach ($equipmentNames as $name) {
                    $searchName = $equipmentMap[$name] ?? $name;
                    $equipment = \App\Models\Equipment::where('name', 'LIKE', '%' . $searchName . '%')->first();
                    if ($equipment) {
                        $equipmentIds[] = $equipment->id;
                    }
                }
                
                $villa->equipments()->sync($equipmentIds);
            } else {
                $villa->equipments()->detach();
            }

            // Supprimer les anciennes périodes bloquées et en créer de nouvelles
            $villa->availabilityBlocks()->delete();
            if (!empty($request->input('blocked_periods'))) {
                $blockedPeriods = json_decode($request->input('blocked_periods'), true);
                if (is_array($blockedPeriods)) {
                    foreach ($blockedPeriods as $period) {
                        VillaAvailabilityBlock::create([
                            'villa_id' => $villa->id,
                            'start_date' => $period['start'],
                            'end_date' => $period['end'],
                            'reason' => $period['reason'] ?? 'Bloqué manuellement',
                        ]);
                    }
                }
            }

            // Traiter les tarifs saisonniers
            if ($request->has('seasonal_prices')) {
                $seasonalPrices = $request->input('seasonal_prices');
                $existingIds = [];
                
                foreach ($seasonalPrices as $key => $seasonalPrice) {
                    if (!empty($seasonalPrice['season_id']) && !empty($seasonalPrice['price_per_night'])) {
                        // Si c'est une mise à jour (id existe dans les données et la clé n'est pas "new-")
                        if (!empty($seasonalPrice['id']) && strpos($key, 'new-') === false) {
                            VillaSeasonalPrice::where('id', $seasonalPrice['id'])
                                ->where('villa_id', $villa->id)
                                ->update([
                                    'season_id' => $seasonalPrice['season_id'],
                                    'price_per_night' => $seasonalPrice['price_per_night'],
                                    'currency' => $seasonalPrice['currency'] ?? 'EUR',
                                ]);
                            $existingIds[] = $seasonalPrice['id'];
                        } else {
                            // Nouveau tarif (clé commence par "new-" ou pas d'id)
                            $newSeasonalPrice = VillaSeasonalPrice::create([
                                'villa_id' => $villa->id,
                                'season_id' => $seasonalPrice['season_id'],
                                'price_per_night' => $seasonalPrice['price_per_night'],
                                'currency' => $seasonalPrice['currency'] ?? 'EUR',
                            ]);
                            $existingIds[] = $newSeasonalPrice->id;
                        }
                    }
                }
                
                // Supprimer les tarifs qui ne sont plus dans la liste (ceux qui existaient mais ne sont plus présents)
                $allExistingIds = $villa->seasonalPrices()->pluck('id')->toArray();
                $idsToDelete = array_diff($allExistingIds, $existingIds);
                if (!empty($idsToDelete)) {
                    VillaSeasonalPrice::where('villa_id', $villa->id)
                        ->whereIn('id', $idsToDelete)
                        ->delete();
                }
            } else {
                // Si aucun tarif saisonnier n'est envoyé, supprimer tous les tarifs existants
                $villa->seasonalPrices()->delete();
            }

            // Supprimer les photos marquées pour suppression
            if ($request->has('deleted_photos')) {
                $deletedPhotoIds = $request->input('deleted_photos');
                foreach ($deletedPhotoIds as $photoId) {
                    $photo = VillaPhoto::find($photoId);
                    if ($photo && $photo->villa_id == $villa->id) {
                        // Supprimer le fichier du stockage
                        if (\Storage::disk('public')->exists($photo->file_path)) {
                            \Storage::disk('public')->delete($photo->file_path);
                        }
                        $photo->delete();
                    }
                }
            }

            // Traiter les nouvelles photos uploadées
            if ($request->hasFile('photos')) {
                $validPhotos = array_filter($request->file('photos'), function($photo) {
                    return $photo && $photo->isValid();
                });
                
                $existingPhotosCount = $villa->photos()->count();
                $hasExistingPrimary = $villa->photos()->where('is_primary', true)->exists();
                
                foreach ($validPhotos as $index => $photo) {
                    if ($photo && $photo->isValid()) {
                        $path = $photo->store('villas/' . $villa->id, 'public');
                        
                        // Déterminer si cette photo est principale
                        $isPrimary = false;
                        if (!$hasExistingPrimary) {
                            // Si aucune photo principale existante, la première nouvelle photo est principale
                            $isPrimary = ($index === 0);
                        }
                        
                        VillaPhoto::create([
                            'villa_id' => $villa->id,
                            'file_path' => $path,
                            'file_name' => $photo->getClientOriginalName(),
                            'alt_text' => $villa->name,
                            'is_primary' => $isPrimary,
                            'sort_order' => $existingPhotosCount + $index,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.villas')
                ->with('success', 'Villa mise à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la villa : ' . $e->getMessage());
        }
    }

    /**
     * Upload de photos pour une villa
     */
    public function uploadPhotos(Request $request, $villaId)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:10240', // 10MB max
        ]);

        $villa = Villa::findOrFail($villaId);
        $uploadedPhotos = [];

        foreach ($request->file('photos') as $photo) {
            if (!$photo || !$photo->isValid()) {
                continue;
            }
            
            $path = $photo->store('villas/' . $villa->id, 'public');
            
            $villaPhoto = VillaPhoto::create([
                'villa_id' => $villa->id,
                'file_path' => $path,
                'file_name' => $photo->getClientOriginalName(),
                'alt_text' => $villa->name,
                'is_primary' => $villa->photos()->count() === 0, // Première photo = principale
                'sort_order' => $villa->photos()->count(),
            ]);

            $uploadedPhotos[] = [
                'id' => $villaPhoto->id,
                'url' => Storage::url($path),
                'name' => $villaPhoto->file_name,
            ];
        }

        return response()->json([
            'success' => true,
            'photos' => $uploadedPhotos,
        ]);
    }

    /**
     * Supprimer une villa
     */
    public function destroy($id)
    {
        try {
            $villa = Villa::findOrFail($id);
            
            DB::beginTransaction();
            
            // Supprimer les photos du stockage
            foreach ($villa->photos as $photo) {
                if (\Storage::disk('public')->exists($photo->file_path)) {
                    \Storage::disk('public')->delete($photo->file_path);
                }
            }
            
            // Supprimer la villa (les relations seront supprimées en cascade grâce aux foreign keys)
            $villa->delete();
            
            DB::commit();
            
            return redirect()->route('admin.villas')
                ->with('success', 'Villa supprimée avec succès');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.villas')
                ->with('error', 'Erreur lors de la suppression de la villa : ' . $e->getMessage());
        }
    }

    /**
     * Importer des dates depuis un fichier iCal
     */
    public function importIcal(Request $request)
    {
        $request->validate([
            'villa_id' => 'nullable|exists:villas,id',
            'import_type' => 'required|in:url,file',
            'ical_url' => 'required_if:import_type,url|url',
            'ical_file' => 'required_if:import_type,file|file|mimes:ics,txt|max:10240',
        ]);
        
        // Si pas de villa_id, on peut quand même parser le fichier pour prévisualisation
        if (!$request->villa_id) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez d\'abord enregistrer la villa avant d\'importer des dates',
            ], 400);
        }

        try {
            $villa = Villa::findOrFail($request->villa_id);
            $icalService = app(\App\Services\IcalService::class);
            $events = [];

            if ($request->import_type === 'url') {
                // Importer depuis une URL
                $events = $icalService->parseIcalFromUrl($request->ical_url);
            } else {
                // Importer depuis un fichier
                $fileContent = file_get_contents($request->file('ical_file')->getRealPath());
                $events = $icalService->parseIcalContent($fileContent);
            }

            // Formater les événements pour la réponse
            $formattedEvents = [];
            foreach ($events as $event) {
                if (isset($event['DTSTART']) && isset($event['DTEND'])) {
                    $formattedEvents[] = [
                        'start' => $event['DTSTART']->format('Y-m-d'),
                        'end' => $event['DTEND']->format('Y-m-d'),
                        'summary' => $event['SUMMARY'] ?? 'Importé depuis iCal',
                        'uid' => $event['UID'] ?? null,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'events' => $formattedEvents,
                'message' => count($formattedEvents) . ' événement(s) trouvé(s)',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Définir une photo comme principale
     */
    public function setPrimaryPhoto($villaId, $photoId)
    {
        try {
            $villa = Villa::findOrFail($villaId);
            $photo = VillaPhoto::where('villa_id', $villaId)->findOrFail($photoId);
            
            DB::beginTransaction();
            
            // Retirer le statut "principale" de toutes les photos de cette villa
            VillaPhoto::where('villa_id', $villaId)->update(['is_primary' => false]);
            
            // Définir cette photo comme principale
            $photo->update(['is_primary' => true]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Photo principale mise à jour avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dates indisponibles pour une villa (formulaire réservation manuelle, calendrier admin).
     */
    public function blockedDates(Request $request, int $id, VillaAvailabilityService $availability)
    {
        $villa = Villa::where('is_active', true)->findOrFail($id);

        $excludeReservationId = $request->filled('exclude_reservation_id')
            ? (int) $request->query('exclude_reservation_id')
            : null;

        return response()->json([
            'blocked_dates' => $availability->getBlockedDates(
                $villa->id,
                $excludeReservationId,
                VillaAvailabilityContext::admin()
            ),
            'min_stay' => (int) ($villa->minimum_stay_nights ?? 3),
            'max_capacity' => (int) $villa->max_capacity,
        ]);
    }
}

