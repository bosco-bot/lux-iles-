@extends('layouts.admin')

@section('title', 'Détails Paiement | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.payments') }}" class="text-white-50 text-decoration-none hover-lux-gold">Paiements</a>
    <span class="mx-2">/</span>
    <span class="text-white">Détails</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Paiement #{{ $payment->payment_number }}
            </h1>
            <p class="text-muted small mb-0">Créé le {{ $payment->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if($payment->status === 'pending')
                <button class="btn btn-outline-success d-flex align-items-center" onclick="processRefund({{ $payment->id }})">
                    <i class="fa-solid fa-undo me-2"></i>Traiter
                </button>
            @elseif($payment->status === 'completed')
                <button class="btn btn-outline-warning d-flex align-items-center" onclick="initiateRefund({{ $payment->id }})">
                    <i class="fa-solid fa-undo me-2"></i>Rembourser
                </button>
            @endif
            <a href="{{ route('admin.payments') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="fa-solid fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informations principales du paiement -->
        <div class="col-lg-8">
            <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Informations du paiement</h3>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Statut</label>
                        @php
                            $statusConfig = [
                                'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'],
                                'processing' => ['label' => 'En cours', 'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25'],
                                'completed' => ['label' => 'Complété', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                'failed' => ['label' => 'Échec', 'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'],
                                'cancelled' => ['label' => 'Annulé', 'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'],
                                'refunded' => ['label' => 'Remboursé', 'class' => 'bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25'],
                            ];
                            $status = $statusConfig[$payment->status] ?? ['label' => ucfirst($payment->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                        @endphp
                        <div>
                            <span class="badge rounded-pill px-3 py-2 fw-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Type</label>
                        @php
                            $typeConfig = [
                                'deposit' => ['label' => 'Acompte', 'class' => 'text-info'],
                                'balance' => ['label' => 'Solde', 'class' => 'text-success'],
                                'full' => ['label' => 'Paiement complet', 'class' => 'text-primary'],
                                'refund' => ['label' => 'Remboursement', 'class' => 'text-warning'],
                                'deposit_guarantee' => ['label' => 'Caution', 'class' => 'text-secondary'],
                            ];
                            $type = $typeConfig[$payment->type] ?? ['label' => ucfirst($payment->type), 'class' => 'text-dark'];
                        @endphp
                        <div class="fw-medium {{ $type['class'] }}">{{ $type['label'] }}</div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Montant</label>
                        <div class="fw-medium text-lux-dark-blue fs-5">{{ number_format($payment->amount, 2, ',', ' ') }} €</div>
                        <div class="small text-lux-greyBlue">{{ $payment->currency }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Méthode de paiement</label>
                        <div class="fw-medium text-lux-dark-blue">{{ ucfirst($payment->payment_method) }}</div>
                    </div>
                </div>

                @if($payment->paid_at)
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Payé le</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $payment->paid_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    @if($payment->due_date)
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Échéance</label>
                        <div class="fw-medium text-lux-dark-blue">{{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</div>
                    </div>
                    @endif
                </div>
                @elseif($payment->due_date)
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Échéance</label>
                        <div class="fw-medium text-lux-dark-blue">{{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</div>
                        @php
                            $isOverdue = \Carbon\Carbon::parse($payment->due_date)->isPast() && $payment->status === 'pending';
                        @endphp
                        @if($isOverdue)
                            <div class="small text-danger fw-medium">⚠️ Échéance dépassée</div>
                        @endif
                    </div>
                </div>
                @endif

                @if($payment->failure_reason)
                <div class="row g-3">
                    <div class="col-12">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Raison de l'échec</label>
                        <div class="text-danger small">{{ $payment->failure_reason }}</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Informations Stripe (si disponible) -->
            @if($payment->stripe_payment_intent_id || $payment->stripe_charge_id)
            <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Informations Stripe</h3>
                <div class="row g-3">
                    @if($payment->stripe_payment_intent_id)
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Payment Intent ID</label>
                        <div class="font-monospace small text-break">{{ $payment->stripe_payment_intent_id }}</div>
                    </div>
                    @endif
                    @if($payment->stripe_charge_id)
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Charge ID</label>
                        <div class="font-monospace small text-break">{{ $payment->stripe_charge_id }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Informations de la réservation liée -->
        <div class="col-lg-4">
            <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Réservation liée</h3>

                @if($payment->reservation)
                <div class="mb-3">
                    <label class="small text-lux-greyBlue text-uppercase fw-medium">Numéro</label>
                    <div class="fw-medium text-lux-dark-blue">
                        <a href="{{ route('admin.reservations.show', $payment->reservation->id) }}" class="text-decoration-none">
                            {{ $payment->reservation->reservation_number }}
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small text-lux-greyBlue text-uppercase fw-medium">Villa</label>
                    <div class="fw-medium text-lux-dark-blue">{{ $payment->reservation->villa->name ?? 'N/A' }}</div>
                    <div class="small text-lux-greyBlue">{{ $payment->reservation->villa->island->name ?? '' }}</div>
                </div>

                <div class="mb-3">
                    <label class="small text-lux-greyBlue text-uppercase fw-medium">Client</label>
                    <div class="fw-medium text-lux-dark-blue">{{ $payment->reservation->guest_first_name }} {{ $payment->reservation->guest_last_name }}</div>
                    <div class="small text-lux-greyBlue">{{ $payment->reservation->guest_email }}</div>
                </div>

                <div class="mb-3">
                    <label class="small text-lux-greyBlue text-uppercase fw-medium">Dates</label>
                    <div class="small text-lux-dark-blue">
                        {{ \Carbon\Carbon::parse($payment->reservation->check_in_date)->format('d/m/Y') }} -
                        {{ \Carbon\Carbon::parse($payment->reservation->check_out_date)->format('d/m/Y') }}
                    </div>
                    <div class="small text-lux-greyBlue">{{ $payment->reservation->number_of_nights }} nuits</div>
                </div>

                <div class="pt-3 border-top">
                    <a href="{{ route('admin.reservations.show', $payment->reservation->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fa-solid fa-external-link me-2"></i>Voir la réservation
                    </a>
                </div>
                @else
                <div class="text-center text-muted py-3">
                    <i class="fa-solid fa-unlink mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="small mb-0">Aucune réservation liée</p>
                </div>
                @endif
            </div>

            <!-- Actions rapides -->
            @if($payment->status === 'completed' || $payment->status === 'pending')
            <div class="bg-white rounded shadow-sm p-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Actions</h3>
                <div class="d-grid gap-2">
                    @if($payment->status === 'pending')
                        <button class="btn btn-success btn-sm" onclick="processPayment({{ $payment->id }})">
                            <i class="fa-solid fa-check me-2"></i>Marquer comme payé
                        </button>
                    @elseif($payment->status === 'completed')
                        <button class="btn btn-warning btn-sm" onclick="initiateRefund({{ $payment->id }})">
                            <i class="fa-solid fa-undo me-2"></i>Initier remboursement
                        </button>
                    @endif
                    <button class="btn btn-outline-secondary btn-sm" onclick="downloadReceipt({{ $payment->id }})">
                        <i class="fa-solid fa-download me-2"></i>Télécharger reçu
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function processPayment(paymentId) {
    if (confirm('Êtes-vous sûr de vouloir marquer ce paiement comme traité ?')) {
        // Implémenter la logique de traitement du paiement
        alert('Fonctionnalité à implémenter');
    }
}

function initiateRefund(paymentId) {
    const amount = prompt('Montant à rembourser (€):', '{{ $payment->amount }}');
    if (amount !== null && amount > 0) {
        if (confirm(`Êtes-vous sûr de vouloir rembourser ${amount} € ?`)) {
            // Implémenter la logique de remboursement
            alert('Fonctionnalité à implémenter');
        }
    }
}

function downloadReceipt(paymentId) {
    // Implémenter le téléchargement du reçu
    alert('Fonctionnalité à implémenter');
}
</script>
@endpush