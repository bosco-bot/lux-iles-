<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    /**
     * Référentiel des équipements (§3.5 CDC).
     */
    public function index()
    {
        $equipments = Equipment::withCount('villas')
            ->orderBy('name')
            ->get();

        return view('pages.admin.equipments.index', compact('equipments'));
    }

    /**
     * Création libre d'un équipement (sans validation préalable — §3.5 CDC).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('equipments', 'name')],
            'icon' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_search_filter' => ['nullable', 'boolean'],
        ]);

        Equipment::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'category' => $validated['category'] ?? null,
            'is_search_filter' => $request->boolean('is_search_filter'),
        ]);

        return redirect()
            ->route('admin.equipments.index')
            ->with('success', 'Équipement ajouté au référentiel.');
    }

    /**
     * Mise à jour d'un équipement.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('equipments', 'name')->ignore($equipment->id)],
            'icon' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_search_filter' => ['nullable', 'boolean'],
        ]);

        $equipment->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'category' => $validated['category'] ?? null,
            'is_search_filter' => $request->boolean('is_search_filter'),
        ]);

        return redirect()
            ->route('admin.equipments.index')
            ->with('success', 'Équipement mis à jour.');
    }

    /**
     * Activer / désactiver le filtre de recherche (action rapide).
     */
    public function toggleSearchFilter(Equipment $equipment)
    {
        $equipment->update([
            'is_search_filter' => ! $equipment->is_search_filter,
        ]);

        return redirect()
            ->route('admin.equipments.index')
            ->with('success', $equipment->is_search_filter
                ? "« {$equipment->name} » est proposé dans les filtres de recherche."
                : "« {$equipment->name} » n'apparaît plus dans les filtres de recherche.");
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->villas()->exists()) {
            return redirect()
                ->route('admin.equipments.index')
                ->with('error', 'Cet équipement est lié à des villas et ne peut pas être supprimé.');
        }

        $equipment->delete();

        return redirect()
            ->route('admin.equipments.index')
            ->with('success', 'Équipement supprimé.');
    }
}
