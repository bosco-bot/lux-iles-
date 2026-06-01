<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;
class SeasonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validateSeason($request);

        Season::create($validated);

        return redirect()->route('admin.settings', ['tab' => 'seasons'])
            ->with('success', 'Saison créée avec succès.');
    }

    public function show($id)
    {
        $season = Season::findOrFail($id);

        return response()->json([
            ...$season->toArray(),
            'start_date' => $season->start_date?->format('Y-m-d'),
            'end_date' => $season->end_date?->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $season = Season::findOrFail($id);
        $season->update($this->validateSeason($request));

        return redirect()->route('admin.settings', ['tab' => 'seasons'])
            ->with('success', 'Saison mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $season = Season::findOrFail($id);

        if ($season->villaSeasonalPrices()->count() > 0) {
            return redirect()->route('admin.settings', ['tab' => 'seasons'])
                ->with('error', 'Cette saison ne peut pas être supprimée car elle est utilisée par des tarifs de villas.');
        }

        $season->delete();

        return redirect()->route('admin.settings', ['tab' => 'seasons'])
            ->with('success', 'Saison supprimée avec succès.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateSeason(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ], [
            'end_date.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
