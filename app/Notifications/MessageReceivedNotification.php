<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class MessageReceivedNotification extends Notification
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau message reçu')
            ->line('Vous avez reçu un nouveau message.')
            ->action('Voir le message', route('admin.messages'))
            ->line('Merci d\'utiliser notre application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $sender = $this->message->sender;
        $reservation = $this->message->reservation;
        
        $messageText = 'Nouveau message de ' . ($sender->first_name . ' ' . $sender->last_name);
        if ($reservation) {
            $messageText .= ' concernant la réservation #' . $reservation->reservation_number;
        }
        
        return [
            'type' => 'message_received',
            'title' => 'Nouveau message',
            'message' => $messageText,
            'message_id' => $this->message->id,
            'sender_id' => $sender->id,
            'sender_name' => $sender->first_name . ' ' . $sender->last_name,
            'reservation_id' => $reservation->id ?? null,
            'reservation_number' => $reservation->reservation_number ?? null,
            'body_preview' => strlen($this->message->body) > 100 ? substr($this->message->body, 0, 100) . '...' : $this->message->body,
            'url' => route('admin.messages', ['conversation_id' => $reservation ? 'reservation_' . $reservation->id : 'user_' . $sender->id]),
            'icon' => 'fa-envelope',
            'color' => 'info',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}