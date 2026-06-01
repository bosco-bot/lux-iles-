<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\DatabaseNotification;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(DatabaseNotification $notification, int $userId)
    {
        $this->notification = $notification;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $data = $this->notification->data;
        
        return [
            'notification' => [
                'id' => $this->notification->id,
                'type' => $data['type'] ?? 'info',
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'url' => $data['url'] ?? '#',
                'icon' => $data['icon'] ?? 'fa-bell',
                'color' => $data['color'] ?? 'primary',
                'created_at' => $this->notification->created_at->diffForHumans(),
                'created_at_full' => $this->notification->created_at->toIso8601String(),
            ],
            'unread_count' => \App\Models\User::find($this->userId)->unreadNotifications()->count(),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
    }
}