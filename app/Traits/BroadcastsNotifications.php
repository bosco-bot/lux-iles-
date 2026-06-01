<?php

namespace App\Traits;

use Illuminate\Notifications\DatabaseNotification;

trait BroadcastsNotifications
{
    /**
     * Broadcast a notification after it's created
     */
    protected function broadcastNotification(DatabaseNotification $notification, $userId)
    {
        try {
            event(new \App\Events\NotificationCreated($notification, $userId));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la diffusion de la notification: ' . $e->getMessage());
        }
    }
}









