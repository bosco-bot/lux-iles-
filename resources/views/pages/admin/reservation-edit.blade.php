@extends('layouts.admin')

@section('title', 'Modifier Réservation | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.reservations') }}" class="text-white-50 text-decoration-none hover-lux-gold">Réservations</a>
    <span class="mx-2">/</span>
    <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="text-white-50 text-decoration-none hover-lux-gold">Détails</a>
    <span class="mx-2">/</span>
    <span class="text-white">Modifier</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Modifier la réservation #{{ $reservation->reservation_number }}
            </h1>
        </div>
        <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Informations de la réservation</h3>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                                <option value="deposit_paid" {{ $reservation->status == 'deposit_paid' ? 'selected' : '' }}>Acompte payé</option>
                                <option value="fully_paid" {{ $reservation->status == 'fully_paid' ? 'selected' : '' }}>Payée</option>
                                <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>Terminée</option>
                                <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="check_in_date" class="form-label">Date d'arrivée <span class="text-danger">*</span></label>
                            <input type="date" name="check_in_date" id="check_in_date" class="form-control" value="{{ $reservation->check_in_date->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="check_out_date" class="form-label">Date de départ <span class="text-danger">*</span></label>
                            <input type="date" name="check_out_date" id="check_out_date" class="form-control" value="{{ $reservation->check_out_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="number_of_guests" class="form-label">Nombre de personnes <span class="text-danger">*</span></label>
                            <input type="number" name="number_of_guests" id="number_of_guests" class="form-control" value="{{ $reservation->number_of_guests }}" min="1" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="total_price" class="form-label">Montant total (€) <span class="text-danger">*</span></label>
                            <input type="number" name="total_price" id="total_price" class="form-control" value="{{ $reservation->total_price }}" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Informations client</h3>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="guest_first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="guest_first_name" id="guest_first_name" class="form-control" value="{{ $reservation->guest_first_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guest_last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="guest_last_name" id="guest_last_name" class="form-control" value="{{ $reservation->guest_last_name }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="guest_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="guest_email" id="guest_email" class="form-control" value="{{ $reservation->guest_email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guest_phone" class="form-label">Téléphone</label>
                            <input type="text" name="guest_phone" id="guest_phone" class="form-control" value="{{ $reservation->guest_phone }}">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded shadow-sm p-4 border">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Notes</h3>
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Notes administrateur</label>
                        <textarea name="admin_notes" id="admin_notes" class="form-control" rows="4">{{ $reservation->admin_notes }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white rounded shadow-sm p-4 border sticky-top" style="top: 20px;">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Informations</h3>
                    <div class="mb-3">
                        <label class="small text-lux-greyBlue">Villa</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->villa->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-lux-greyBlue">Numéro de réservation</label>
                        <div class="fw-medium text-lux-dark-blue">#{{ $reservation->reservation_number }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="small text-lux-greyBlue">Créée le</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lux-primary">
                            <i class="fa-solid fa-save me-2"></i>Enregistrer les modifications
                        </button>
                        <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection




