@extends('layouts.admin')

@section('title', 'Gestion des Paiements | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Paiements</span>
@endsection

@section('content')
    <!-- Top Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Gestion des Paiements
            </h1>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            <div class="position-relative d-none d-md-block">
                <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-greyBlue);"></i>
                <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Rechercher un paiement..." class="form-control ps-5" style="width: 300px; border-color: rgba(0,0,0,0.1);">
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm dropdown-toggle" style="background-color: var(--lux-blue); color: white; border: none;" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-download me-2"></i> Exporter
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.payments.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                            <i class="fa-solid fa-file-csv me-2"></i> Exporter en CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.payments.export', array_merge(request()->all(), ['format' => 'excel'])) }}">
                            <i class="fa-solid fa-file-excel me-2"></i> Exporter en Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="small text-lux-greyBlue mb-1">Total Collecté</p>
                        <h3 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ number_format($totalAmount ?? 0, 0, ',', ' ') }} €</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(10, 26, 47, 0.1);">
                        <i class="fa-solid fa-euro-sign fs-4" style="color: var(--lux-gold);"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="small text-lux-greyBlue mb-1">En Attente</p>
                        <h3 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ $pendingCount ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="fa-solid fa-clock fs-4" style="color: #ffc107;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="small text-lux-greyBlue mb-1">Échecs</p>
                        <h3 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ $failedCount ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="fa-solid fa-xmark-circle fs-4" style="color: #dc3545;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <section class="bg-white rounded shadow-sm p-4 mb-4 border" style="border-color: rgba(0,0,0,0.05) !important;">
        <form method="GET" action="{{ route('admin.payments') }}" id="filters-form">
            <div class="d-flex flex-column flex-md-row align-items-end gap-3">
                <div class="row g-3 flex-fill">
                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Période</label>
                        <div class="position-relative">
                            <i class="fa-regular fa-calendar position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="period" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Toutes périodes</option>
                                <option value="last_30_days" {{ request('period') == 'last_30_days' ? 'selected' : '' }}>30 derniers jours</option>
                                <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Ce mois-ci</option>
                                <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                                <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Cette année</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</label>
                        <div class="position-relative">
                            <i class="fa-solid fa-filter position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="status" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Tous les statuts</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En traitement</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Type</label>
                        <div class="position-relative">
                            <i class="fa-solid fa-tag position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="type" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Tous les types</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Arrhes</option>
                                <option value="balance" {{ request('type') == 'balance' ? 'selected' : '' }}>Solde</option>
                                <option value="deposit_guarantee" {{ request('type') == 'deposit_guarantee' ? 'selected' : '' }}>Garantie</option>
                                <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Remboursement</option>
                                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajustement</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Méthode</label>
                        <div class="position-relative">
                            <i class="fa-solid fa-credit-card position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="payment_method" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Toutes méthodes</option>
                                <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement</option>
                                <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-lux-primary d-flex align-items-center gap-2">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filtrer</span>
                    </button>
                    <a href="{{ route('admin.payments') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-rotate"></i>
                    </a>
                </div>
            </div>
        </form>
    </section>

    <!-- Payments Table -->
    <section class="bg-white rounded shadow-sm border overflow-hidden mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light border-bottom" style="background-color: #f8f9fa !important;">
                    <tr>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">N° Paiement</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Réservation</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Type</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Montant</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Méthode</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Date</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue text-end" style="font-size: 0.7rem; letter-spacing: 0.05em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="payment-row" style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.03)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3">
                                <div class="fw-medium text-lux-dark-blue">#{{ $payment->payment_number }}</div>
                                <div class="small text-lux-greyBlue">{{ $payment->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-medium text-lux-dark-blue">#{{ $payment->reservation->reservation_number ?? 'N/A' }}</div>
                                <div class="small text-lux-greyBlue">{{ $payment->reservation->guest_first_name ?? '' }} {{ $payment->reservation->guest_last_name ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $typeLabels = [
                                        'deposit' => 'Arrhes',
                                        'balance' => 'Solde',
                                        'deposit_guarantee' => 'Garantie',
                                        'refund' => 'Remboursement',
                                        'adjustment' => 'Ajustement',
                                    ];
                                    $typeColors = [
                                        'deposit' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                        'balance' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                        'deposit_guarantee' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                        'refund' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                        'adjustment' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                                    ];
                                @endphp
                                <span class="badge rounded-pill px-3 py-1 small fw-medium {{ $typeColors[$payment->type] ?? 'bg-secondary' }}">
                                    {{ $typeLabels[$payment->type] ?? ucfirst($payment->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-lux-dark-blue">{{ number_format($payment->amount, 2, ',', ' ') }} {{ $payment->currency }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $methodLabels = [
                                        'stripe' => 'Stripe',
                                        'bank_transfer' => 'Virement',
                                        'other' => 'Autre',
                                    ];
                                @endphp
                                <span class="small text-lux-greyBlue">{{ $methodLabels[$payment->payment_method] ?? ucfirst($payment->payment_method) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusConfig = [
                                        'completed' => ['label' => 'Complété', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                        'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'],
                                        'processing' => ['label' => 'En traitement', 'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25'],
                                        'failed' => ['label' => 'Échoué', 'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'],
                                        'refunded' => ['label' => 'Remboursé', 'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'],
                                        'cancelled' => ['label' => 'Annulé', 'class' => 'bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25'],
                                    ];
                                    $status = $statusConfig[$payment->status] ?? ['label' => ucfirst($payment->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                                @endphp
                                <span class="badge rounded-pill px-3 py-1 small fw-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="small text-lux-dark-blue">
                                    @if($payment->paid_at)
                                        {{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') }}
                                    @elseif($payment->due_date)
                                        Échéance: {{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}
                                    @else
                                        {{ $payment->created_at->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2 payment-actions" style="opacity: 0; transition: opacity 0.2s;">
                                    <button class="btn btn-sm btn-link text-lux-gold p-2 border-0 view-payment-btn" style="text-decoration: none;" title="Voir détails" data-payment-id="{{ $payment->id }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    @if($payment->status == 'completed' && $payment->type != 'refund')
                                        <button class="btn btn-sm btn-link text-danger p-2 border-0 refund-payment-btn" style="text-decoration: none;" title="Rembourser" data-payment-id="{{ $payment->id }}" data-payment-amount="{{ $payment->amount }}">
                                            <i class="fa-solid fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-lux-greyBlue">
                                <i class="fa-regular fa-credit-card fs-1 mb-3 d-block"></i>
                                <p class="mb-0">Aucun paiement trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="px-4 py-3 border-top bg-light d-flex align-items-center justify-content-between">
                <span class="small text-lux-greyBlue">
                    Affichage de {{ $payments->firstItem() }} à {{ $payments->lastItem() }} sur {{ $payments->total() }} paiements
                </span>
                <div class="d-flex align-items-center gap-2">
                    {{ $payments->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </section>

    <!-- Modal Détails Paiement -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom" style="border-color: rgba(0,0,0,0.1) !important;">
                    <h5 class="modal-title font-serif" id="paymentDetailsModalLabel" style="color: var(--lux-dark-blue);">Détails du Paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="paymentDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-lux-gold" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Remboursement -->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-bottom" style="border-color: rgba(0,0,0,0.1) !important;">
                    <h5 class="modal-title font-serif" id="refundModalLabel" style="color: var(--lux-dark-blue);">Initier un Remboursement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="refundForm">
                    <div class="modal-body">
                        <input type="hidden" id="refund_payment_id" name="payment_id">
                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: var(--lux-dark-blue);">Montant du remboursement</label>
                            <div class="position-relative">
                                <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="color: var(--lux-dark-blue); font-weight: 500;">€</span>
                                <input type="number" id="refund_amount" name="amount" step="0.01" min="0.01" class="form-control ps-5" required>
                            </div>
                            <small class="text-lux-greyBlue">Montant maximum: <span id="max_refund_amount">0.00</span> €</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: var(--lux-dark-blue);">Raison du remboursement</label>
                            <textarea name="reason" id="refund_reason" class="form-control" rows="3" placeholder="Optionnel"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="border-color: rgba(0,0,0,0.1) !important;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-lux-primary">Confirmer le remboursement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Recherche avec debounce
        const searchInput = document.getElementById('search-input');
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filters-form').submit();
                }, 500);
            });
        }

        // Afficher les actions au survol
        document.querySelectorAll('.payment-row').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.querySelector('.payment-actions').style.opacity = '1';
            });
            row.addEventListener('mouseleave', function() {
                this.querySelector('.payment-actions').style.opacity = '0';
            });
        });

        // Modal détails paiement
        document.querySelectorAll('.view-payment-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
                const content = document.getElementById('paymentDetailsContent');

                // Vérifier si l'utilisateur semble connecté
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken || !csrfToken.getAttribute('content')) {
                    content.innerHTML = '<div class="alert alert-warning">Session invalide. Veuillez rafraîchir la page.</div>';
                    modal.show();
                    return;
                }

                content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-lux-gold" role="status"><span class="visually-hidden">Chargement...</span></div></div>';
                modal.show();

                fetch(`/admin/payments/${paymentId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        // Vérifier le type de contenu de la réponse
                        const contentType = response.headers.get('content-type');

                        if (!response.ok) {
                            if (response.status === 401 || response.status === 403) {
                                throw new Error('AUTH_REQUIRED');
                            } else if (response.status === 404) {
                                throw new Error('NOT_FOUND');
                            } else {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                        }

                        // Vérifier que c'est bien du JSON
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('INVALID_RESPONSE_TYPE');
                        }

                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const payment = data.payment;
                            const reservation = data.reservation;
                            
                            content.innerHTML = `
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold mb-3" style="color: var(--lux-dark-blue);">Informations Paiement</h6>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Numéro de paiement</label>
                                            <div class="fw-medium">#${payment.payment_number}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Type</label>
                                            <div class="fw-medium">${getTypeLabel(payment.type)}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Montant</label>
                                            <div class="fw-bold fs-5" style="color: var(--lux-dark-blue);">${parseFloat(payment.amount).toFixed(2)} ${payment.currency}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Méthode de paiement</label>
                                            <div class="fw-medium">${getMethodLabel(payment.payment_method)}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Statut</label>
                                            <div><span class="badge ${getStatusClass(payment.status)}">${getStatusLabel(payment.status)}</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold mb-3" style="color: var(--lux-dark-blue);">Informations Réservation</h6>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Numéro de réservation</label>
                                            <div class="fw-medium">#${reservation.reservation_number}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Client</label>
                                            <div class="fw-medium">${reservation.guest_first_name} ${reservation.guest_last_name}</div>
                                            <div class="small text-lux-greyBlue">${reservation.guest_email}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Villa</label>
                                            <div class="fw-medium">${reservation.villa?.name || 'N/A'}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-lux-greyBlue">Dates</label>
                                            <div class="fw-medium">${formatDate(reservation.check_in_date)} - ${formatDate(reservation.check_out_date)}</div>
                                        </div>
                                    </div>
                                </div>
                                ${payment.paid_at ? `<div class="mt-3 pt-3 border-top"><small class="text-lux-greyBlue">Payé le: ${formatDateTime(payment.paid_at)}</small></div>` : ''}
                                ${payment.failure_reason ? `<div class="mt-3 pt-3 border-top"><small class="text-danger">Raison d'échec: ${payment.failure_reason}</small></div>` : ''}
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur chargement détails paiement:', error);
                        let errorMessage = 'Erreur lors du chargement des détails.';

                        if (error.message === 'AUTH_REQUIRED') {
                            errorMessage = 'Session expirée. Veuillez vous reconnecter en tant qu\'administrateur.';
                        } else if (error.message === 'NOT_FOUND') {
                            errorMessage = 'Paiement non trouvé.';
                        } else if (error.message === 'INVALID_RESPONSE_TYPE') {
                            errorMessage = 'Réponse invalide du serveur. Vous n\'êtes peut-être pas connecté.';
                        } else if (error.message.includes('401')) {
                            errorMessage = 'Non autorisé. Veuillez vous reconnecter.';
                        } else if (error.message.includes('403')) {
                            errorMessage = 'Accès refusé. Vérifiez vos permissions administrateur.';
                        } else if (error.message.includes('404')) {
                            errorMessage = 'Paiement non trouvé.';
                        } else if (error.message.includes('500')) {
                            errorMessage = 'Erreur serveur. Consultez les logs pour plus de détails.';
                        }

                        content.innerHTML = `<div class="alert alert-danger">
                            <strong>Erreur :</strong> ${errorMessage}
                            <br><small class="text-muted">Code: ${error.message}</small>
                        </div>`;
                    });
            });
        });

        // Modal remboursement
        document.querySelectorAll('.refund-payment-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const maxAmount = parseFloat(this.getAttribute('data-payment-amount'));
                
                document.getElementById('refund_payment_id').value = paymentId;
                document.getElementById('refund_amount').value = maxAmount.toFixed(2);
                document.getElementById('refund_amount').max = maxAmount;
                document.getElementById('max_refund_amount').textContent = maxAmount.toFixed(2);
                
                const modal = new bootstrap.Modal(document.getElementById('refundModal'));
                modal.show();
            });
        });

        // Soumission formulaire remboursement
        document.getElementById('refundForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const paymentId = document.getElementById('refund_payment_id').value;
            const formData = {
                amount: parseFloat(document.getElementById('refund_amount').value),
                reason: document.getElementById('refund_reason').value,
            };

            fetch(`/admin/payments/${paymentId}/refund`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('refundModal')).hide();
                    alert('Remboursement initié avec succès.');
                    location.reload();
                } else {
                    alert(data.message || 'Erreur lors du remboursement.');
                }
            })
            .catch(error => {
                alert('Erreur lors du remboursement.');
            });
        });

        // Fonctions utilitaires
        function getTypeLabel(type) {
            const labels = {
                'deposit': 'Arrhes',
                'balance': 'Solde',
                'deposit_guarantee': 'Garantie',
                'refund': 'Remboursement',
                'adjustment': 'Ajustement',
            };
            return labels[type] || type;
        }

        function getMethodLabel(method) {
            const labels = {
                'stripe': 'Stripe',
                'bank_transfer': 'Virement bancaire',
                'other': 'Autre',
            };
            return labels[method] || method;
        }

        function getStatusLabel(status) {
            const labels = {
                'completed': 'Complété',
                'pending': 'En attente',
                'processing': 'En traitement',
                'failed': 'Échoué',
                'refunded': 'Remboursé',
                'cancelled': 'Annulé',
            };
            return labels[status] || status;
        }

        function getStatusClass(status) {
            const classes = {
                'completed': 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                'pending': 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                'processing': 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                'failed': 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                'refunded': 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                'cancelled': 'bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25',
            };
            return classes[status] || 'bg-secondary';
        }

        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function formatDateTime(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    });
</script>
@endpush

