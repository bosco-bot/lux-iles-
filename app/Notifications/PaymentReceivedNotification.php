<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reservation = $this->payment->reservation;
        
        // S'assurer que la réservation est chargée
        if (!$reservation) {
            $this->payment->load('reservation');
            $reservation = $this->payment->reservation;
        }
        
        $message = (new MailMessage)
            ->subject('💰 Nouveau paiement reçu - ' . $this->payment->payment_number)
            ->greeting('Bonjour,')
            ->line('Un nouveau paiement a été reçu sur la plateforme LUXÎLES.')
            ->line('**Détails du paiement :**')
            ->line('• Numéro de paiement : ' . $this->payment->payment_number)
            ->line('• Montant : ' . number_format($this->payment->amount, 2, ',', ' ') . ' €')
            ->line('• Type : ' . $this->payment->type_label)
            ->line('• Statut : ' . $this->payment->status_label);
        
        if ($reservation) {
            $message->line('• Réservation : #' . $reservation->reservation_number)
                    ->line('• Client : ' . $reservation->guest_first_name . ' ' . $reservation->guest_last_name);
        }
        
        $message->action('Voir le paiement', route('admin.payments.show', $this->payment->id))
                ->line('Merci d\'utiliser notre application!');
        
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reservation = $this->payment->reservation;
        
        // S'assurer que la réservation est chargée
        if (!$reservation) {
            $this->payment->load('reservation');
            $reservation = $this->payment->reservation;
        }
        
        return [
            'type' => 'payment_received',
            'title' => 'Nouveau paiement reçu',
            'message' => 'Paiement #' . $this->payment->payment_number . ' de ' . number_format($this->payment->amount, 2, ',', ' ') . ' € reçu pour la réservation #' . ($reservation->reservation_number ?? 'N/A'),
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'reservation_id' => $reservation->id ?? null,
            'reservation_number' => $reservation->reservation_number ?? null,
            'amount' => $this->payment->amount,
            'type_label' => $this->payment->type_label,
            'url' => route('admin.payments.show', ['id' => $this->payment->id], false),
            'icon' => 'fa-money-bill-wave',
            'color' => 'success',
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