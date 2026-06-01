<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'payment_number',
        'type',
        'amount',
        'currency',
        'status',
        'payment_method',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'transaction_id',
        'due_date',
        'paid_at',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relation avec la réservation
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Obtenir le label du type de paiement
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'Arrhes',
            'balance' => 'Solde',
            'deposit_guarantee' => 'Garantie',
            'refund' => 'Remboursement',
            'adjustment' => 'Ajustement',
            default => ucfirst($this->type),
        };
    }

    /**
     * Obtenir le label du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'completed' => 'Complété',
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            'cancelled' => 'Annulé',
            default => ucfirst($this->status),
        };
    }

    /**
     * Obtenir le label de la méthode de paiement
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'stripe' => 'Stripe',
            'bank_transfer' => 'Virement bancaire',
            'check' => 'Chèque',
            'cash' => 'Espèces',
            'other' => 'Autre',
            default => ucfirst(str_replace('_', ' ', $this->payment_method ?? '')),
        };
    }
}




