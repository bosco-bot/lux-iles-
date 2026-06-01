<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CancellationPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CancellationPolicyController extends Controller
{
    /**
     * Afficher les politiques d'annulation publiques
     */
    public function publicIndex()
    {
        $policies = CancellationPolicy::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('sort_order')
            ->get();
        
        return view('pages.cancellation-policies', compact('policies'));
    }

    /**
     * Afficher la liste des politiques (pour API/AJAX admin)
     */
    public function index()
    {
        $policies = CancellationPolicy::orderBy('sort_order')->orderBy('name')->get();
        return response()->json($policies);
    }

    /**
     * Créer une nouvelle politique
     */
    public function store(Request $request)
    {
        // Convertir refund_rules depuis le format du formulaire
        $refundRules = [];
        if ($request->has('refund_rules')) {
            foreach ($request->refund_rules as $rule) {
                if (isset($rule['days_before']) && isset($rule['refund_percentage'])) {
                    $refundRules[] = [
                        'days_before' => (int) $rule['days_before'],
                        'refund_percentage' => (float) $rule['refund_percentage'],
                    ];
                }
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        if (empty($refundRules)) {
            return redirect()->back()->withErrors(['refund_rules' => 'Au moins une règle de remboursement est requise.'])->withInput();
        }

        // Si c'est la politique par défaut, désactiver les autres
        if ($validated['is_default'] ?? false) {
            CancellationPolicy::where('is_default', true)->update(['is_default' => false]);
        }

        // Générer le slug
        $slug = Str::slug($validated['name']);
        $baseSlug = $slug;
        $counter = 1;
        while (CancellationPolicy::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $policy = CancellationPolicy::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'refund_rules' => $refundRules,
            'is_default' => $validated['is_default'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'icon' => $validated['icon'] ?? 'fa-handshake',
            'color' => $validated['color'] ?? 'primary',
            'sort_order' => CancellationPolicy::max('sort_order') + 1,
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Politique d\'annulation créée avec succès.');
    }

    /**
     * Mettre à jour une politique
     */
    public function update(Request $request, CancellationPolicy $cancellationPolicy)
    {
        // Convertir refund_rules depuis le format du formulaire
        $refundRules = [];
        if ($request->has('refund_rules')) {
            foreach ($request->refund_rules as $rule) {
                if (isset($rule['days_before']) && isset($rule['refund_percentage'])) {
                    $refundRules[] = [
                        'days_before' => (int) $rule['days_before'],
                        'refund_percentage' => (float) $rule['refund_percentage'],
                    ];
                }
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        if (empty($refundRules)) {
            return redirect()->back()->withErrors(['refund_rules' => 'Au moins une règle de remboursement est requise.'])->withInput();
        }

        // Si c'est la politique par défaut, désactiver les autres
        if ($validated['is_default'] ?? false) {
            CancellationPolicy::where('id', '!=', $cancellationPolicy->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $cancellationPolicy->update(array_merge($validated, ['refund_rules' => $refundRules]));

        return redirect()->route('admin.settings')
            ->with('success', 'Politique d\'annulation mise à jour avec succès.');
    }

    /**
     * Supprimer une politique
     */
    public function destroy(CancellationPolicy $cancellationPolicy)
    {
        // Ne pas supprimer si c'est la politique par défaut
        if ($cancellationPolicy->is_default) {
            return redirect()->route('admin.settings')
                ->with('error', 'Impossible de supprimer la politique par défaut.');
        }

        $cancellationPolicy->delete();

        return redirect()->route('admin.settings')
            ->with('success', 'Politique d\'annulation supprimée avec succès.');
    }

    /**
     * Récupérer une politique pour édition (AJAX)
     */
    public function show(CancellationPolicy $cancellationPolicy)
    {
        return response()->json($cancellationPolicy);
    }
}
