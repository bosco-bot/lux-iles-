<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::orderByDesc('created_at')->paginate(20);

        return view('pages.admin.promo-codes.index', compact('promoCodes'));
    }

    public function create()
    {
        return view('pages.admin.promo-codes.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePromoCode($request);

        PromoCode::create([
            ...$validated,
            'code' => strtoupper($validated['code']),
            'uses_count' => 0,
        ]);

        return redirect()
            ->route('admin.promo-codes.index')
            ->with('success', 'Code promotionnel créé avec succès.');
    }

    public function edit(PromoCode $promoCode)
    {
        return view('pages.admin.promo-codes.edit', compact('promoCode'));
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $this->validatePromoCode($request, $promoCode->id);

        $promoCode->update([
            ...$validated,
            'code' => strtoupper($validated['code']),
        ]);

        return redirect()
            ->route('admin.promo-codes.index')
            ->with('success', 'Code promotionnel mis à jour.');
    }

    public function destroy(PromoCode $promoCode)
    {
        if ($promoCode->uses_count > 0) {
            return redirect()
                ->route('admin.promo-codes.index')
                ->with('error', 'Ce code a déjà été utilisé. Désactivez-le plutôt que de le supprimer.');
        }

        $promoCode->delete();

        return redirect()
            ->route('admin.promo-codes.index')
            ->with('success', 'Code promotionnel supprimé.');
    }

    public function toggle(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => ! $promoCode->is_active]);

        $status = $promoCode->is_active ? 'activé' : 'désactivé';

        return redirect()
            ->route('admin.promo-codes.index')
            ->with('success', "Code {$status} avec succès.");
    }

    private function validatePromoCode(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('promo_codes', 'code')->ignore($ignoreId),
            ],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
