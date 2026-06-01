<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender', 'recipient', 'reservation', 'attachments']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Diffuser sur le canal privé du destinataire et de l'expéditeur
        $channels = [];
        
        if ($this->message->recipient_id) {
            $channels[] = new PrivateChannel('user.' . $this->message->recipient_id);
        }
        
        if ($this->message->sender_id) {
            $channels[] = new PrivateChannel('user.' . $this->message->sender_id);
        }
        
        return $channels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'sender_name' => $this->message->sender->first_name . ' ' . $this->message->sender->last_name,
                'recipient_id' => $this->message->recipient_id,
                'recipient_name' => $this->message->recipient ? ($this->message->recipient->first_name . ' ' . $this->message->recipient->last_name) : null,
                'reservation_id' => $this->message->reservation_id,
                'reservation_number' => $this->message->reservation->reservation_number ?? null,
                'body' => $this->message->body,
                'is_read' => $this->message->is_read,
                'is_admin_message' => $this->message->is_admin_message,
                'created_at' => $this->message->created_at->format('H:i'),
                'created_at_full' => $this->message->created_at->toIso8601String(),
                'attachments' => $this->message->attachments->map(function($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_path' => $attachment->file_path,
                        'file_name' => $attachment->file_name,
                        'file_type' => $attachment->file_type,
                        'mime_type' => $attachment->mime_type,
                    ];
                }),
            ],
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}