<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    /**
     * Store a newly created season.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_month' => 'required|integer|min:1|max:12',
            'start_day' => 'required|integer|min:1|max:31',
            'end_month' => 'required|integer|min:1|max:12',
            'end_day' => 'required|integer|min:1|max:31',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ?: true;

        Season::create($validated);

        return redirect()->route('admin.settings', ['tab' => 'seasons'])
            ->with('success', 'Saison créée avec succès.');
    }

    /**
     * Display the specified season (AJAX).
     */
    public function show($id)
    {
        $season = Season::findOrFail($id);
        return response()->json($season);
    }

    /**
     * Update the specified season.
     */
    public function update(Request $request, $id)
    {
        $season = Season::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_month' => 'required|integer|min:1|max:12',
            'start_day' => 'required|integer|min:1|max:31',
            'end_month' => 'required|integer|min:1|max:12',
            'end_day' => 'required|integer|min:1|max:31',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ?: true;

        $season->update($validated);

        return redirect()->route('admin.settings', ['tab' => 'seasons'])
            ->with('success', 'Saison mise à jour avec succès.');
    }

    /**
     * Remove the specified season.
     */
    public function destroy($id)
    {
        $season = Season::findOrFail($id);
        
        // Vérifier si la saison est utilisée par des tarifs
        if ($season->villaSeasonalPrices()->count() > 0) {
            return redirect()->route('admin.settings', ['tab' => 'seasons'])
                ->with('error', 'Cette saison ne peut pas être supprimée car elle est utilisée par des tarifs de villas.');
        }

        $season->delete();

        return redirect()->route('admin.settings', ['tab' => 'seasons'])
            ->with('success', 'Saison supprimée avec succès.');
    }
}
