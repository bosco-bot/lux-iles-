<?php

namespace App\Notifications;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ReservationCreatedNotification extends Notification
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
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
        $villaName = 'N/A';
        if ($this->reservation->relationLoaded('villa') && $this->reservation->villa) {
            $villaName = $this->reservation->villa->name;
        } else {
            // Charger la relation si elle n'est pas chargée
            $this->reservation->load('villa');
            $villaName = $this->reservation->villa->name ?? 'N/A';
        }
        
        $message = (new MailMessage)
            ->subject('📅 Nouvelle réservation - ' . $this->reservation->reservation_number)
            ->greeting('Bonjour,')
            ->line('Une nouvelle réservation a été créée sur la plateforme LUXÎLES.')
            ->line('**Détails de la réservation :**')
            ->line('• Numéro de réservation : ' . $this->reservation->reservation_number)
            ->line('• Villa : ' . $villaName)
            ->line('• Client : ' . $this->reservation->guest_first_name . ' ' . $this->reservation->guest_last_name)
            ->line('• Email : ' . $this->reservation->guest_email)
            ->line('• Dates : Du ' . Carbon::parse($this->reservation->check_in_date)->format('d/m/Y') . ' au ' . Carbon::parse($this->reservation->check_out_date)->format('d/m/Y'))
            ->line('• Nombre de nuits : ' . $this->reservation->number_of_nights)
            ->line('• Nombre de voyageurs : ' . $this->reservation->number_of_guests)
            ->line('• Montant total : ' . number_format($this->reservation->total_price, 2, ',', ' ') . ' €')
            ->line('• Statut : ' . ucfirst($this->reservation->status));
        
        $message->action('Voir la réservation', route('admin.reservations.show', $this->reservation->id))
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
        $villaName = 'N/A';
        if ($this->reservation->relationLoaded('villa') && $this->reservation->villa) {
            $villaName = $this->reservation->villa->name;
        } else {
            // Charger la relation si elle n'est pas chargée
            $this->reservation->load('villa');
            $villaName = $this->reservation->villa->name ?? 'N/A';
        }
        
        return [
            'type' => 'reservation_created',
            'title' => 'Nouvelle réservation',
            'message' => 'Réservation #' . $this->reservation->reservation_number . ' créée pour la villa ' . $villaName,
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'villa_name' => $villaName,
            'guest_name' => $this->reservation->guest_first_name . ' ' . $this->reservation->guest_last_name,
            'amount' => $this->reservation->total_price,
            'url' => route('admin.reservations.show', $this->reservation->id),
            'icon' => 'fa-calendar-check',
            'color' => 'primary',
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