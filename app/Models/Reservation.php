<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_number',
        'villa_id',
        'user_id',
        'guest_first_name',
        'guest_last_name',
        'guest_email',
        'guest_phone',
        'guest_address',
        'check_in_date',
        'check_out_date',
        'number_of_nights',
        'number_of_guests',
        'adults',
        'children',
        'infants',
        'base_price',
        'cleaning_fee',
        'service_fee',
        'vat_amount',
        'tourist_tax',
        'total_price',
        'currency',
        'deposit_percentage',
        'deposit_amount',
        'balance_amount',
        'deposit_guarantee',
        'status',
        'source',
        'promo_code_id',
        'discount_amount',
        'platform_reservation_id',
        'platform_sync_id',
        'special_requests',
        'admin_notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'created_by',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'cancelled_at' => 'datetime',
        'base_price' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'tourist_tax' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'deposit_guarantee' => 'decimal:2',
        'number_of_nights' => 'integer',
        'number_of_guests' => 'integer',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
        'deposit_percentage' => 'integer',
    ];

    /**
     * Relation avec la villa
     */
    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }

    /**
     * Code promotionnel appliqué (§3.2 CDC).
     */
    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les paiements
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relation avec les documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relation avec les invités supplémentaires
     */
    public function guests()
    {
        return $this->hasMany(ReservationGuest::class);
    }

    /**
     * Relation avec les messages
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Avis déposé pour ce séjour (§3.4 CDC).
     */
    public function review()
    {
        return $this->hasOne(VillaReview::class);
    }

    /**
     * Réservation saisie hors ligne par l'admin (§3.11 CDC) — pas de paiement Stripe côté client.
     */
    public function isManualOffline(): bool
    {
        return $this->source === 'manual';
    }

    public function allowsClientOnlinePayment(): bool
    {
        return ! $this->isManualOffline();
    }

    public function hasPendingClientPayments(): bool
    {
        return $this->payments()
            ->whereIn('status', ['pending', 'processing'])
            ->whereIn('type', ['deposit', 'balance', 'deposit_guarantee'])
            ->exists();
    }
}




