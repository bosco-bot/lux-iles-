@extends('layouts.admin')

@section('title', 'Détails Réservation | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.reservations') }}" class="text-white-50 text-decoration-none hover-lux-gold">Réservations</a>
    <span class="mx-2">/</span>
    <span class="text-white">Détails</span>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Réservation #{{ $reservation->reservation_number }}
            </h1>
            <p class="text-muted small mb-0">Créée le {{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-lux-primary d-flex align-items-center">
                <i class="fa-solid fa-pen me-2"></i>Modifier
            </a>
            @if($reservation->status != 'cancelled')
                <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger d-flex align-items-center" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                        <i class="fa-solid fa-ban me-2"></i>Annuler
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.reservations') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="fa-solid fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Informations de la réservation</h3>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Statut</label>
                        @php
                            $statusConfig = [
                                'confirmed' => ['label' => 'Confirmée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'],
                                'cancelled' => ['label' => 'Annulée', 'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'],
                                'fully_paid' => ['label' => 'Payée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                'deposit_paid' => ['label' => 'Acompte payé', 'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25'],
                            ];
                            $status = $statusConfig[$reservation->status] ?? ['label' => ucfirst($reservation->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                        @endphp
                        <div>
                            <span class="badge rounded-pill px-3 py-2 fw-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Source</label>
                        <div class="fw-medium text-lux-dark-blue">
                            @if($reservation->source === 'manual')
                                Manuelle (hors ligne)
                            @else
                                {{ ucfirst($reservation->source) }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Villa</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->villa->name ?? 'N/A' }}</div>
                        <div class="small text-lux-greyBlue">{{ $reservation->villa->island->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Dates</label>
                        <div class="fw-medium text-lux-dark-blue">
                            {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}
                        </div>
                        <div class="small text-lux-greyBlue">{{ $reservation->number_of_nights }} nuits</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Nombre de personnes</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->number_of_guests }} personnes</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Montant total</label>
                        @if($reservation->discount_amount > 0 && $reservation->promoCode)
                            <div class="small text-success mb-1">
                                Réduction ({{ $reservation->promoCode->code }}) : -{{ number_format($reservation->discount_amount, 2, ',', ' ') }} €
                            </div>
                        @endif
                        <div class="fw-medium text-lux-dark-blue fs-5">{{ number_format($reservation->total_price, 2, ',', ' ') }} €</div>
                    </div>
                </div>
            </div>

            <!-- Informations client -->
            <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Informations client</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Nom complet</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Email</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->guest_email }}</div>
                    </div>
                    @if($reservation->guest_phone)
                    <div class="col-md-6">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Téléphone</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->guest_phone }}</div>
                    </div>
                    @endif
                    @if($reservation->guest_address)
                    <div class="col-md-12">
                        <label class="small text-lux-greyBlue text-uppercase fw-medium">Adresse</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->guest_address }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($reservation->special_requests || $reservation->admin_notes)
            <div class="bg-white rounded shadow-sm p-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Notes</h3>
                @if($reservation->special_requests)
                <div class="mb-3">
                    <label class="small text-lux-greyBlue text-uppercase fw-medium">Demandes spéciales</label>
                    <div class="text-lux-dark-blue">{{ $reservation->special_requests }}</div>
                </div>
                @endif
                @if($reservation->admin_notes)
                <div>
                    <label class="small text-lux-greyBlue text-uppercase fw-medium">Notes administrateur</label>
                    <div class="text-lux-dark-blue">{{ $reservation->admin_notes }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Détails financiers -->
            <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Détails financiers</h3>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-lux-greyBlue">Prix de base</span>
                        <span class="fw-medium">{{ number_format($reservation->base_price, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-lux-greyBlue">Frais de ménage</span>
                        <span class="fw-medium">{{ number_format($reservation->cleaning_fee, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-lux-greyBlue">Frais de service</span>
                        <span class="fw-medium">{{ number_format($reservation->service_fee, 2, ',', ' ') }} €</span>
                    </div>
                    @if($reservation->vat_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-lux-greyBlue">TVA</span>
                        <span class="fw-medium">{{ number_format($reservation->vat_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-lux-greyBlue">Taxe de séjour</span>
                        <span class="fw-medium">{{ number_format($reservation->tourist_tax, 2, ',', ' ') }} €</span>
                    </div>
                    @if($reservation->discount_amount > 0 && $reservation->promoCode)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success">Réduction ({{ $reservation->promoCode->code }})</span>
                        <span class="fw-medium text-success">-{{ number_format($reservation->discount_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-medium text-lux-dark-blue">Total</span>
                        <span class="fw-bold text-lux-gold fs-5">{{ number_format($reservation->total_price, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-lux-greyBlue">Acompte</span>
                        <span class="fw-medium">{{ number_format($reservation->deposit_amount, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-lux-greyBlue">Solde</span>
                        <span class="fw-medium">{{ number_format($reservation->balance_amount, 2, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>

            <!-- Paiements -->
            @if($reservation->payments->count() > 0)
            <div class="bg-white rounded shadow-sm p-4 border">
                <h3 class="h5 mb-4 text-lux-dark-blue">Paiements</h3>
                @foreach($reservation->payments as $payment)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between mb-1">
                        <div class="d-flex flex-column">
                            <span class="fw-medium text-lux-dark-blue">{{ number_format($payment->amount, 2, ',', ' ') }} €</span>
                            <span class="small text-lux-greyBlue">{{ $payment->type === 'deposit' ? 'Acompte' : ($payment->type === 'balance' ? 'Solde' : ($payment->type === 'deposit_guarantee' ? 'Caution' : 'Paiement')) }}</span>
                        </div>
                        <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">{{ $payment->status == 'completed' ? 'Payé' : 'En attente' }}</span>
                    </div>
                    <div class="small text-lux-greyBlue">
                        Mode : {{ $payment->payment_method_label }}
                        @if($payment->paid_at)
                            · Payé le {{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') }}
                        @elseif($payment->due_date)
                            · Échéance : {{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}
                        @else
                            · Non payé
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
@endsection


