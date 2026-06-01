<?php

namespace App\Http\Controllers;

use App\Models\PrivilegeClubNotification;
use App\Services\PrivilegeClubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivilegeClubController extends Controller
{
    public function __construct(
        protected PrivilegeClubService $clubService
    ) {}

    /**
     * Page dédiée aux membres connectés (§3.1 CDC).
     */
    public function index()
    {
        $user = Auth::user();
        $tierDefinitions = $this->clubService->tierDefinitions();
        $qualifyingStays = $this->clubService->countQualifyingStays($user);
        $earnedTier = $this->clubService->calculateTier($user);
        $currentTier = $user->privilege_tier;
        $notifications = PrivilegeClubNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('pages.privilege-club', compact(
            'user',
            'tierDefinitions',
            'qualifyingStays',
            'earnedTier',
            'currentTier',
            'notifications'
        ));
    }

    public function markNotificationRead(PrivilegeClubNotification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->markAsRead();

        return back();
    }
}
