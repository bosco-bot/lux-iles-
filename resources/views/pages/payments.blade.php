@extends('layouts.dashboard')

@section('title', 'Mes Paiements | LUXÎLES - Dashboard')

@section('content')
    <!-- Page Header / Hero Minimal -->
    <section id="payments-hero" class="position-relative" style="height: 250px; background-color: var(--lux-dark-blue); overflow: hidden; margin-top: -1rem; margin-left: -1rem; margin-right: -1rem; margin-bottom: 2rem;">
        <style>
            @media (min-width: 768px) {
                #payments-hero {
                    margin-top: -2rem !important;
                    margin-left: -2rem !important;
                    margin-right: -2rem !important;
                }
            }
        </style>
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-40">
            <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop" class="w-100 h-100" style="object-fit: cover;" alt="Payment">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(10,26,47,0.7) 0%, rgba(10,26,47,0.4) 50%, rgba(10,26,47,0.8) 100%);"></div>
        </div>
        <div class="position-relative z-10 h-100 d-flex align-items-center justify-content-center text-center" style="padding-top: 3rem;">
            <div>
                <h1 class="h1 font-serif text-white mb-2" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">Mes Paiements</h1>
                <p class="text-lux-gold text-uppercase small fw-medium mb-0" style="letter-spacing: 0.2em; font-size: 0.875rem;">Historique & Suivi</p>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="container-fluid px-4">
        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border shadow-sm h-100" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="small text-lux-greyBlue mb-1">Total Payé</p>
                                <h3 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ number_format($totalPaid ?? 0, 0, ',', ' ') }} €</h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(10, 26, 47, 0.1);">
                                <i class="fa-solid fa-euro-sign fs-4" style="color: var(--lux-gold);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border shadow-sm h-100" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="small text-lux-greyBlue mb-1">En Attente</p>
                                <h3 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ number_format($pendingAmount ?? 0, 0, ',', ' ') }} €</h3>
                                <p class="small text-lux-greyBlue mb-0 mt-1">{{ $pendingCount ?? 0 }} paiement{{ $pendingCount > 1 ? 's' : '' }}</p>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                                <i class="fa-solid fa-clock fs-4" style="color: #ffc107;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border shadow-sm h-100" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="small text-lux-greyBlue mb-1">Total Paiements</p>
                                <h3 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ $payments->total() }}</h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(10, 26, 47, 0.1);">
                                <i class="fa-regular fa-credit-card fs-4" style="color: var(--lux-gold);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Actions Bar -->
        <div class="card border mb-4 shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('espace-client.payments') }}" id="filters-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Période</label>
                            <div class="position-relative">
                                <i class="fa-regular fa-calendar position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                                <select name="period" class="form-select form-select-sm ps-5" style="border-color: transparent; background-color: rgba(248, 248, 246, 0.5); transition: border-color 0.3s;" onchange="document.getElementById('filters-form').submit();" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.3)'" onmouseout="this.style.borderColor='transparent'" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='transparent'">
                                    <option value="">Toutes périodes</option>
                                    <option value="last_30_days" {{ request('period') == 'last_30_days' ? 'selected' : '' }}>30 derniers jours</option>
                                    <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Ce mois-ci</option>
                                    <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Cette année</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</label>
                            <select name="status" class="form-select form-select-sm" style="border-color: transparent; background-color: rgba(248, 248, 246, 0.5); transition: border-color 0.3s;" onchange="document.getElementById('filters-form').submit();" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.3)'" onmouseout="this.style.borderColor='transparent'" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='transparent'">
                                <option value="">Tous les statuts</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En traitement</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Type</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm w-100 d-flex align-items-center justify-content-between dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: rgba(138, 150, 166, 0.2);">
                                    <span>{{ request('type') ? ($availableTypes[request('type')] ?? 'Tous les types') : 'Tous les types' }}</span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="typeFilterDropdown">
                                    <li><a class="dropdown-item" href="{{ route('espace-client.payments', array_merge(request()->except('type'), ['type' => ''])) }}">Tous les types</a></li>
                                    @foreach($availableTypes as $typeKey => $typeLabel)
                                        <li><a class="dropdown-item" href="{{ route('espace-client.payments', array_merge(request()->except('type'), ['type' => $typeKey])) }}">{{ $typeLabel }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Recherche</label>
                            <div class="position-relative">
                                <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-greyBlue); z-index: 10;"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Numéro de paiement..." class="form-control form-control-sm ps-5" style="border-color: transparent; background-color: rgba(248, 248, 246, 0.5); transition: border-color 0.3s;" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.3)'" onmouseout="this.style.borderColor='transparent'" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='transparent'">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; overflow: hidden;">
            <div class="card-header bg-light border-bottom" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.2) !important;">
                <h3 class="h5 font-serif mb-0" style="color: var(--lux-dark-blue);">Historique des Paiements</h3>
            </div>
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="bg-light border-bottom" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.2) !important;">
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Numéro</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Réservation</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Type</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Montant</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Méthode</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Statut</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Date</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                @php
                                    $statusConfig = [
                                        'completed' => ['label' => 'Complété', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                        'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'],
                                        'processing' => ['label' => 'En traitement', 'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25'],
                                        'failed' => ['label' => 'Échoué', 'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'],
                                        'refunded' => ['label' => 'Remboursé', 'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'],
                                        'cancelled' => ['label' => 'Annulé', 'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'],
                                    ];
                                    $status = $statusConfig[$payment->status] ?? ['label' => ucfirst($payment->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                                @endphp
                                <tr style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                    <td class="px-4 py-3">
                                        <span class="fw-medium text-lux-dark-blue small" style="font-family: monospace;">{{ $payment->payment_number }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <div class="fw-medium text-lux-dark-blue small">{{ $payment->reservation->reservation_number ?? 'N/A' }}</div>
                                            @if($payment->reservation && $payment->reservation->villa)
                                                <div class="small text-lux-greyBlue">{{ $payment->reservation->villa->name }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="small text-lux-dark-blue">{{ $payment->type_label }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="fw-bold text-lux-dark-blue">{{ number_format($payment->amount, 2, ',', ' ') }} {{ $payment->currency }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="small text-lux-greyBlue">{{ $payment->payment_method_label }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge rounded-pill px-2 py-1 small fw-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="small text-lux-greyBlue">{{ $payment->created_at->format('d/m/Y') }}</div>
                                        @if($payment->paid_at)
                                            <div class="small text-lux-greyBlue" style="font-size: 0.7rem;">Payé le {{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            @if($payment->status === 'pending' && $payment->reservation && $payment->type === 'balance' && $payment->reservation->allowsClientOnlinePayment())
                                                <a href="{{ route('espace-client.pay-balance', $payment->reservation) }}" class="btn btn-sm btn-lux-primary" style="font-size: 0.75rem; padding: 0.375rem 0.75rem;">
                                                    <i class="fa-solid fa-credit-card me-1"></i> Payer
                                                </a>
                                            @endif
                                            @if($payment->status === 'completed' && $payment->reservation)
                                                @php
                                                    $receiptType = $payment->type === 'deposit' ? 'receipt-deposit' : ($payment->type === 'balance' ? 'receipt-balance' : null);
                                                @endphp
                                                @if($receiptType)
                                                    <a href="{{ route('espace-client.documents.' . $receiptType, ['reservation' => $payment->reservation, 'payment' => $payment]) }}" target="_blank" class="btn btn-link text-lux-gold p-2 rounded-circle border-0 text-decoration-none" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.1)'; this.style.color='var(--lux-light-gold)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-gold)'" title="Télécharger le reçu">
                                                        <i class="fa-regular fa-file-lines"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($payments->hasPages())
                    <div class="card-footer bg-white border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-3">
                        <div class="small text-lux-gray">
                            Affichage de <strong>{{ $payments->firstItem() }}</strong> à <strong>{{ $payments->lastItem() }}</strong> sur <strong>{{ $payments->total() }}</strong> paiement{{ $payments->total() > 1 ? 's' : '' }}
                        </div>
                        <div>
                            {{ $payments->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="card-body text-center py-5">
                    <i class="fa-regular fa-credit-card fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                    <p class="text-lux-gray mb-0">Aucun paiement trouvé</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Pagination LUX ÎLES - Respect de la charte graphique */
.lux-pagination .page-link {
    color: var(--lux-dark-blue) !important;
    background-color: transparent !important;
    border-color: rgba(203, 174, 130, 0.3) !important;
    border-radius: 0.375rem !important;
    padding: 0.5rem 0.75rem !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
}

.lux-pagination .page-link:hover {
    color: var(--lux-gold) !important;
    background-color: rgba(203, 174, 130, 0.1) !important;
    border-color: var(--lux-gold) !important;
    transform: translateY(-1px);
}

.lux-pagination .page-item.active .page-link {
    background-color: var(--lux-gold) !important;
    border-color: var(--lux-gold) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(203, 174, 130, 0.3) !important;
}

.lux-pagination .page-item.disabled .page-link {
    color: var(--lux-greyBlue) !important;
    background-color: transparent !important;
    border-color: rgba(138, 150, 166, 0.2) !important;
    opacity: 0.6 !important;
}

.lux-pagination .page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(203, 174, 130, 0.25) !important;
    border-color: var(--lux-gold) !important;
}

/* Responsive pour mobile */
@media (max-width: 575.98px) {
    .lux-pagination .page-link {
        padding: 0.375rem 0.5rem !important;
        font-size: 0.875rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // Debounce pour la recherche
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filters-form').submit();
            }, 500);
        });
    }
</script>
@endpush

