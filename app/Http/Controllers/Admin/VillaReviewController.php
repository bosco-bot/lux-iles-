<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VillaReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VillaReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = VillaReview::with(['villa', 'user', 'reservation'])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reviews = $query->paginate(20)->withQueryString();

        $counts = [
            'pending' => VillaReview::pending()->count(),
            'approved' => VillaReview::approved()->count(),
            'rejected' => VillaReview::where('status', VillaReview::STATUS_REJECTED)->count(),
        ];

        return view('pages.admin.villa-reviews.index', compact('reviews', 'status', 'counts'));
    }

    public function show(VillaReview $villaReview)
    {
        $villaReview->load(['villa', 'user', 'reservation', 'moderator']);

        return view('pages.admin.villa-reviews.show', compact('villaReview'));
    }

    public function approve(VillaReview $villaReview)
    {
        if ($villaReview->status !== VillaReview::STATUS_PENDING) {
            return back()->with('error', 'Seuls les avis en attente peuvent être validés.');
        }

        $villaReview->update([
            'status' => VillaReview::STATUS_APPROVED,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.villa-reviews.show', $villaReview)
            ->with('success', 'Avis publié sur la fiche villa.');
    }

    public function reject(Request $request, VillaReview $villaReview)
    {
        if ($villaReview->status !== VillaReview::STATUS_PENDING) {
            return back()->with('error', 'Seuls les avis en attente peuvent être refusés.');
        }

        $villaReview->update([
            'status' => VillaReview::STATUS_REJECTED,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
            'published_at' => null,
        ]);

        return redirect()
            ->route('admin.villa-reviews.index', ['status' => 'rejected'])
            ->with('success', 'Avis refusé — il ne sera pas visible sur le site.');
    }

    public function updateResponse(Request $request, VillaReview $villaReview)
    {
        if ($villaReview->status !== VillaReview::STATUS_APPROVED) {
            return back()->with('error', 'La réponse publique n\'est possible que pour un avis publié.');
        }

        $validated = $request->validate([
            'admin_response' => ['nullable', 'string', 'max:5000'],
        ]);

        $villaReview->update([
            'admin_response' => $validated['admin_response'] ?: null,
        ]);

        return back()->with('success', 'Réponse de l\'équipe enregistrée.');
    }
}
