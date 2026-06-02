<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientNotificationController extends Controller
{
    /**
     * Récupérer les notifications non lues du client connecté.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $limit = $request->integer('limit', 20);

        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        $formatted = $notifications->map(function ($notification) {
            $data = $notification->data;

            return [
                'id' => $notification->id,
                'type' => $data['type'] ?? 'info',
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'url' => $this->resolveNotificationUrl($data),
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
     * Marquer une notification comme lue.
     */
    public function markAsRead(string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée.',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue.',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues.
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
     * Récupérer le nombre de notifications non lues.
     */
    public function unreadCount()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'count' => $user->unreadNotifications()->count(),
        ]);
    }

    private function resolveNotificationUrl(array $data): string
    {
        $type = $data['type'] ?? null;
        $rawUrl = $data['url'] ?? '#';

        if ($type === 'message_received') {
            $conversationId = null;
            if (! empty($data['reservation_id'])) {
                $conversationId = 'reservation_' . $data['reservation_id'];
            } elseif (! empty($data['sender_id'])) {
                $conversationId = 'user_' . $data['sender_id'];
            }

            if ($conversationId) {
                return route('espace-client.messages', ['conversation_id' => $conversationId], false);
            }

            return route('espace-client.messages', [], false);
        }

        return $this->normalizeUrl($rawUrl);
    }

    private function normalizeUrl(?string $url): string
    {
        if (! $url || $url === '#') {
            return '#';
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! $path) {
            return '#';
        }

        $query = parse_url($url, PHP_URL_QUERY);

        return $query ? "{$path}?{$query}" : $path;
    }
}

