<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Récupérer les notifications non lues de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 20);
        
        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
        
        // Formater les notifications
        $formatted = $notifications->map(function ($notification) {
            $data = $notification->data;
            return [
                'id' => $notification->id,
                'type' => $data['type'] ?? 'info',
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'url' => $data['url'] ?? '#',
                'icon' => $data['icon'] ?? 'fa-bell',
                'color' => $data['color'] ?? 'primary',
                'created_at' => $notification->created_at->diffForHumans(),
                'created_at_full' => $notification->created_at->toIso8601String(),
            ];
        });
        
        return response()->json([
            'success' => true,
            'notifications' => $formatted,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        
        // Laravel utilise un UUID pour les notifications, mais la route peut recevoir un ID
        $notification = $user->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue.',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notification non trouvée.',
        ], 404);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues.',
            'unread_count' => 0,
        ]);
    }

    /**
     * Récupérer le nombre de notifications non lues
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'count' => $user->unreadNotifications()->count(),
        ]);
    }
}
