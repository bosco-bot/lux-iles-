@extends('layouts.admin')

@section('title', 'Paramètres | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Paramètres</span>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-serif mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
            Paramètres de la Plateforme
        </h1>
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="admin-view-site-btn" class="btn bg-lux-blue text-white px-5 py-2 rounded small" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">
                <i class="fa-solid fa-eye me-2"></i>Voir le site
            </button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <!-- Section Configuration des Taxes -->
        <section class="bg-white rounded shadow-sm border mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="background-color: rgba(248, 248, 246, 0.5); border-color: rgba(138, 150, 166, 0.1) !important;">
                <div>
                    <h2 class="h5 font-serif text-lux-dark-blue fw-semibold mb-1" style="font-family: 'Playfair Display', serif;">Configuration des Taxes</h2>
                    <p class="small text-lux-greyBlue mb-0">Gérez les taxes de séjour et frais de service par défaut.</p>
                </div>
                <button type="button" class="btn btn-link text-lux-gold p-0 border-0 small fw-medium" data-bs-toggle="modal" data-bs-target="#settingsHistoryModal" onclick="loadSettingsHistory()" style="text-decoration: none;">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Historique
                </button>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">TVA Globale (%)</label>
                                <div class="position-relative">
                                    <input type="number" name="global_tax_rate" value="{{ $settings['global_tax_rate'] ?? '8.5' }}" step="0.1" class="form-control pe-5" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-lux-greyBlue">%</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Appliquée sur tous les services additionnels.</p>
                            </div>

                            <div>
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Taxe de Séjour (Par nuit/pers)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative flex-fill">
                                        <span class="position-absolute start-0 top-50 translate-middle-y ps-3 text-lux-greyBlue">€</span>
                                        <input type="number" name="tourist_tax_per_night" value="{{ $settings['tourist_tax_per_night'] ?? '2.50' }}" step="0.01" class="form-control" style="border-color: rgba(138, 150, 166, 0.3); padding-left: 2.5rem;" placeholder="2.50">
                                    </div>
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <input class="form-check-input tourist-tax-switch" type="checkbox" name="tourist_tax_enabled" id="tourist_tax_enabled" value="1" {{ ($settings['tourist_tax_enabled'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-3 small text-lux-dark-blue fw-medium" for="tourist_tax_enabled">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light rounded p-4 border h-100 d-flex flex-column justify-content-center" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.1) !important;">
                                <h3 class="small fw-medium text-lux-dark-blue mb-3">Aperçu Facture Client (Simulation)</h3>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2 text-lux-greyBlue">
                                        <span>Sous-total Villa (5 nuits)</span>
                                        <span>5 000,00 €</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 text-lux-dark-blue">
                                        <span>TVA (8.5%)</span>
                                        <span>425,00 €</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 text-lux-dark-blue">
                                        <span>Taxe de séjour (2.50€ x 2p x 5n)</span>
                                        <span>25,00 €</span>
                                    </div>
                                    <hr class="my-2" style="border-color: rgba(138, 150, 166, 0.2);">
                                    <div class="d-flex justify-content-between fw-semibold text-lux-gold">
                                        <span>Total Estimé</span>
                                        <span class="fs-5">5 450,00 €</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-light border-top d-flex justify-content-end" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.1) !important;">
                    <button type="submit" class="btn bg-lux-blue text-white px-6 py-2 rounded small fw-medium" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">Enregistrer les modifications</button>
                </div>
            </form>
        </section>

        <!-- Section Informations Légales de l'Entreprise -->
        <section class="bg-white rounded shadow-sm border mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
            <div class="p-4 border-bottom" style="background-color: rgba(248, 248, 246, 0.5); border-color: rgba(138, 150, 166, 0.1) !important;">
                <h2 class="h5 font-serif text-lux-dark-blue fw-semibold mb-1" style="font-family: 'Playfair Display', serif;">Informations Légales de l'Entreprise</h2>
                <p class="small text-lux-greyBlue mb-0">Informations affichées sur les documents officiels (factures, contrats, reçus) et le site web.</p>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Nom de l'entreprise</label>
                                <input type="text" name="company_name" value="{{ $settings['company_name'] ?? 'BLUE SECRET' }}" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);" placeholder="BLUE SECRET">
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Nom officiel de l'entreprise (utilisé sur les documents légaux).</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Adresse complète du siège social</label>
                                <textarea name="company_address" rows="3" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);" placeholder="4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE">{{ $settings['company_address'] ?? '4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE' }}</textarea>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Adresse complète utilisée sur les documents légaux.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Téléphone professionnel</label>
                                <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '+33 7 66 33 41 98' }}" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);" placeholder="+33 7 66 33 41 98">
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Numéro de téléphone professionnel affiché sur le site et les documents.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Email de contact officiel</label>
                                <input type="email" name="company_email" value="{{ $settings['company_email'] ?? 'contact.luxiles@gmail.com' }}" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);" placeholder="contact.luxiles@gmail.com">
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Email officiel utilisé pour les communications et les documents.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Numéro SIRET</label>
                                <input type="text" name="company_siret" value="{{ $settings['company_siret'] ?? '85262415400013' }}" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);" placeholder="85262415400013" maxlength="14">
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Numéro SIRET à 14 chiffres (obligatoire sur les factures).</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Numéro de TVA intracommunautaire</label>
                                <input type="text" name="company_vat" value="{{ $settings['company_vat'] ?? 'FR31852624154' }}" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);" placeholder="FR31852624154">
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Numéro de TVA intracommunautaire (format FR + numéro).</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-light border-top d-flex justify-content-end" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.1) !important;">
                    <button type="submit" class="btn bg-lux-blue text-white px-6 py-2 rounded small fw-medium" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">Enregistrer les modifications</button>
                </div>
            </form>
        </section>

        <!-- Section Paramètres de Réservation -->
        <section class="bg-white rounded shadow-sm border mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
            <div class="p-4 border-bottom" style="background-color: rgba(248, 248, 246, 0.5); border-color: rgba(138, 150, 166, 0.1) !important;">
                <h2 class="h5 font-serif text-lux-dark-blue fw-semibold mb-1" style="font-family: 'Playfair Display', serif;">Paramètres de Réservation</h2>
                <p class="small text-lux-greyBlue mb-0">Configurez les paramètres par défaut pour les réservations (arrhes, solde, délais).</p>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Frais de Service (%)</label>
                                <div class="position-relative">
                                    <input type="number" name="service_fee_percentage" value="{{ $settings['service_fee_percentage'] ?? '5.00' }}" step="0.01" min="0" max="100" class="form-control pe-5" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-lux-greyBlue">%</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Pourcentage appliqué sur le prix de base de la villa.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Acompte Minimum (%)</label>
                                <div class="position-relative">
                                    <input type="number" name="deposit_percentage_min" value="{{ $settings['deposit_percentage_min'] ?? '30' }}" step="1" min="0" max="100" class="form-control pe-5" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-lux-greyBlue">%</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Pourcentage minimum d'arrhes requis lors de la réservation.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Acompte Maximum (%)</label>
                                <div class="position-relative">
                                    <input type="number" name="deposit_percentage_max" value="{{ $settings['deposit_percentage_max'] ?? '50' }}" step="1" min="0" max="100" class="form-control pe-5" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-lux-greyBlue">%</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Pourcentage maximum d'arrhes autorisé lors de la réservation.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Jours avant arrivée - Paiement du solde</label>
                                <div class="position-relative">
                                    <input type="number" name="balance_due_days_before_checkin" value="{{ $settings['balance_due_days_before_checkin'] ?? '30' }}" step="1" min="0" class="form-control pe-5" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-lux-greyBlue">jours</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Nombre de jours avant l'arrivée où le solde doit être payé.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Jours avant arrivée - Dépôt de garantie</label>
                                <div class="position-relative">
                                    <input type="number" name="deposit_guarantee_days_before_checkin" value="{{ $settings['deposit_guarantee_days_before_checkin'] ?? '7' }}" step="1" min="0" class="form-control pe-5" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-lux-greyBlue">jours</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Nombre de jours avant l'arrivée où le dépôt de garantie doit être effectué.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Délai d'annulation (jours)</label>
                                <div class="position-relative">
                                    <input type="number" name="cancellation_policy_days" value="{{ $settings['cancellation_policy_days'] ?? '30' }}" step="1" min="0" class="form-control pe-5" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-lux-greyBlue">jours</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 fst-italic mb-0">Délai minimum avant l'arrivée pour pouvoir annuler sans pénalité.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-light border-top d-flex justify-content-end" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.1) !important;">
                    <button type="submit" class="btn bg-lux-blue text-white px-6 py-2 rounded small fw-medium" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">Enregistrer les modifications</button>
                </div>
            </form>
        </section>
        {{-- 
        <!-- Section Gestion des Saisons -->
        <section class="mb-4">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="h4 font-serif text-lux-dark-blue fw-semibold mb-1" style="font-family: 'Playfair Display', serif;">Gestion des Saisons</h2>
                    <p class="small text-lux-greyBlue mb-0">Définissez les périodes creuses et pleines pour vos tarifs.</p>
                </div>
                <button type="button" class="btn text-lux-gold border border-lux-gold rounded px-4 py-2 small fw-medium bg-transparent btn-new-season" data-bs-toggle="modal" data-bs-target="#seasonModal" onclick="openSeasonModal()">
                    <i class="fa-solid fa-plus me-2"></i>Nouvelle Saison
                </button>
            </div>

            <div class="bg-white rounded shadow-sm border overflow-hidden" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: rgba(248, 248, 246, 0.5);">
                            <tr>
                                <th class="px-4 py-3 small text-uppercase fw-semibold text-lux-greyBlue border-0" style="font-size: 0.7rem;">Nom</th>
                                <th class="px-4 py-3 small text-uppercase fw-semibold text-lux-greyBlue border-0" style="font-size: 0.7rem;">Période</th>
                                <th class="px-4 py-3 small text-uppercase fw-semibold text-lux-greyBlue border-0" style="font-size: 0.7rem;">Statut</th>
                                <th class="px-4 py-3 small text-uppercase fw-semibold text-lux-greyBlue border-0 text-end" style="font-size: 0.7rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($seasons as $season)
                            <tr class="align-middle">
                                <td class="px-4 py-3">
                                    <span class="fw-medium text-lux-dark-blue">{{ $season->name }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-lux-greyBlue">{{ $season->period }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($season->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 small fw-bold text-uppercase" style="font-size: 0.65rem;">Actif</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 small fw-bold text-uppercase" style="font-size: 0.65rem;">Inactif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-link text-lux-dark-blue p-0" onclick="editSeason({{ $season->id }})" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.seasons.destroy', $season->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette saison ?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-lux-greyBlue">
                                    Aucune saison définie. Cliquez sur "Nouvelle Saison" pour commencer.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        --}}


        <!-- Section Politiques d'Annulation -->
        <section class="mb-4">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="h4 font-serif text-lux-dark-blue fw-semibold mb-1" style="font-family: 'Playfair Display', serif;">Politiques d'Annulation</h2>
                    <p class="small text-lux-greyBlue mb-0">Définissez les règles applicables aux remboursements.</p>
                </div>
                <button type="button" class="btn text-lux-gold border border-lux-gold rounded px-4 py-2 small fw-medium bg-transparent btn-new-policy" data-bs-toggle="modal" data-bs-target="#policyModal" onclick="openPolicyModal()">
                    <i class="fa-solid fa-plus me-2"></i>Nouvelle Politique
                </button>
            </div>

            <div class="row g-4">
                @forelse($policies as $policy)
                <div class="col-md-4">
                    <div class="bg-white rounded shadow-sm border p-4 h-100 position-relative {{ $policy->is_default ? 'border-lux-gold' : '' }}" style="border-color: {{ $policy->is_default ? 'rgba(203, 174, 130, 0.2)' : 'rgba(138, 150, 166, 0.1)' }} !important; {{ $policy->is_default ? 'border-width: 2px;' : '' }}">
                        @if($policy->is_default)
                        <div class="position-absolute top-0 start-50 translate-middle" style="margin-top: -12px;">
                            <span class="badge bg-lux-gold text-white px-3 py-1 small fw-medium text-uppercase">Par défaut</span>
                        </div>
                        @endif
                        @if($policy->is_active && !$policy->is_default)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 small fw-bold text-uppercase">Active</span>
                        </div>
                        @endif
                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background-color: rgba(203, 174, 130, 0.1);">
                            <i class="{{ $policy->icon ?? 'fa-regular fa-handshake' }} fs-5 {{ $policy->is_default ? 'text-lux-gold' : 'text-lux-dark-blue' }}"></i>
                        </div>
                        <h3 class="h6 fw-medium text-lux-dark-blue mb-2">{{ $policy->name }}</h3>
                        <p class="small text-lux-greyBlue mb-3" style="line-height: 1.6;">{{ $policy->description ?? 'Aucune description' }}</p>
                        <ul class="small text-lux-dark-blue mb-4 ps-0" style="list-style: none;">
                            @foreach($policy->formatted_rules as $rule)
                            <li class="d-flex align-items-center mb-2"><i class="fa-solid fa-check text-lux-gold me-2"></i> {{ $rule['label'] }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn w-100 small py-2 rounded {{ $policy->is_default ? 'btn-lux-primary' : '' }}" onclick="editPolicy({{ $policy->id }})" style="{{ !$policy->is_default ? 'background-color: var(--lux-white); color: var(--lux-dark-blue); border: 1px solid rgba(138, 150, 166, 0.2); transition: all 0.3s;' : '' }}" onmouseover="{{ !$policy->is_default ? "this.style.backgroundColor='var(--lux-dark-blue)'; this.style.color='white'" : '' }}" onmouseout="{{ !$policy->is_default ? "this.style.backgroundColor='var(--lux-white)'; this.style.color='var(--lux-dark-blue)'" : '' }}">{{ $policy->is_default ? 'Configurer' : 'Modifier' }}</button>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fa-regular fa-handshake fs-1 text-lux-greyBlue mb-3"></i>
                        <p class="text-lux-greyBlue mb-0">Aucune politique d'annulation définie. Cliquez sur "Nouvelle Politique" pour en créer une.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </section>

        <!-- Section Emails et Rôles -->
        <div class="row g-4">
            <!-- Emails Automatiques -->
            <div class="col-lg-6">
                <section class="bg-white rounded shadow-sm border p-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <h2 class="h5 font-serif text-lux-dark-blue fw-semibold mb-4 d-flex align-items-center" style="font-family: 'Playfair Display', serif;">
                        <i class="fa-regular fa-envelope text-lux-gold me-3"></i>Emails Automatiques
                    </h2>

                    <div class="d-flex flex-column gap-3">
                        <!-- Email Item 1 -->
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded" style="border-color: rgba(138, 150, 166, 0.1) !important; background-color: rgba(248, 248, 246, 0.3);">
                            <div>
                                <h4 class="small fw-medium text-lux-dark-blue mb-1">Confirmation de réservation</h4>
                                <p class="small text-lux-greyBlue mb-0">Envoyé immédiatement après paiement.</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <button class="btn btn-link text-lux-greyBlue p-0 border-0 small" style="text-decoration: underline;">Éditer Template</button>
                                <div class="form-check form-switch">
                                    <input class="form-check-input email-switch" type="checkbox" checked>
                                </div>
                            </div>
                        </div>

                        <!-- Email Item 2 -->
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded" style="border-color: rgba(138, 150, 166, 0.1) !important; background-color: rgba(248, 248, 246, 0.3);">
                            <div>
                                <h4 class="small fw-medium text-lux-dark-blue mb-1">Rappel Check-in (J-3)</h4>
                                <p class="small text-lux-greyBlue mb-0">Instructions d'arrivée et code villa.</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <button class="btn btn-link text-lux-greyBlue p-0 border-0 small" style="text-decoration: underline;">Éditer Template</button>
                                <div class="form-check form-switch">
                                    <input class="form-check-input email-switch" type="checkbox" checked>
                                </div>
                            </div>
                        </div>

                        <!-- Email Item 3 -->
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded opacity-75" style="border-color: rgba(138, 150, 166, 0.1) !important; background-color: rgba(248, 248, 246, 0.3);">
                            <div>
                                <h4 class="small fw-medium text-lux-dark-blue mb-1">Demande d'avis (J+2 après départ)</h4>
                                <p class="small text-lux-greyBlue mb-0">Lien vers le formulaire de satisfaction.</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <button class="btn btn-link text-lux-greyBlue p-0 border-0 small" style="text-decoration: underline;">Éditer Template</button>
                                <div class="form-check form-switch">
                                    <input class="form-check-input email-switch" type="checkbox">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Rôles & Permissions -->
            <div class="col-lg-6">
                <section class="bg-white rounded shadow-sm border p-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h5 font-serif text-lux-dark-blue fw-semibold d-flex align-items-center mb-0" style="font-family: 'Playfair Display', serif;">
                            <i class="fa-solid fa-user-shield text-lux-gold me-3"></i>Rôles & Permissions
                        </h2>
                        <button type="button" class="btn bg-lux-blue text-white px-3 py-1 rounded small" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="openAdminModal()" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">Ajouter un admin</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr class="border-bottom" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                                    <th class="pb-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Utilisateur</th>
                                    <th class="pb-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Rôle</th>
                                    <th class="pb-3 small text-uppercase fw-medium text-lux-greyBlue text-end" style="font-size: 0.7rem; letter-spacing: 0.05em;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($admins as $admin)
                                    <tr class="border-bottom" style="border-color: rgba(138, 150, 166, 0.05) !important;">
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-circle overflow-hidden" style="width: 32px; height: 32px; background-color: var(--lux-gold); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                    {{ strtoupper(substr($admin->first_name ?? '', 0, 1) . substr($admin->last_name ?? '', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="text-lux-dark-blue fw-medium mb-0">{{ $admin->first_name }} {{ $admin->last_name }}</p>
                                                    <p class="small text-lux-greyBlue mb-0">{{ $admin->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $primaryRole = $admin->primary_role;
                                                $roleName = $primaryRole ? $primaryRole->formatted_name : 'Super Admin';
                                                $badgeColor = $primaryRole ? $primaryRole->badge_color : 'danger';
                                                $badgeClass = 'badge bg-' . $badgeColor . ' bg-opacity-10 text-' . $badgeColor;
                                            @endphp
                                            <span class="{{ $badgeClass }} px-2 py-1 small fw-medium">{{ $roleName }}</span>
                                        </td>
                                        <td class="py-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link text-lux-greyBlue p-0 border-0 dropdown-toggle" type="button" id="adminActions{{ $admin->id }}" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminActions{{ $admin->id }}">
                                                    <li><a class="dropdown-item" href="#" onclick="editAdmin({{ $admin->id }}); return false;"><i class="fa-solid fa-pen me-2"></i>Modifier</a></li>
                                                    @if($admin->id !== auth()->id())
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteAdmin({{ $admin->id }}); return false;"><i class="fa-solid fa-trash me-2"></i>Retirer</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-lux-greyBlue">Aucun administrateur trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded border" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.1) !important;">
                        <h4 class="small text-uppercase fw-medium text-lux-dark-blue mb-3" style="font-size: 0.7rem; letter-spacing: 0.05em;">Permissions par rôle</h4>
                        <div class="small text-lux-greyBlue">
                            @forelse($roles as $role)
                            <p class="mb-2">
                                @php
                                    $roleColorClass = 'fw-medium text-' . $role->badge_color;
                                @endphp
                                <span class="{{ $roleColorClass }}">{{ $role->formatted_name }}:</span> 
                                @if($role->permissions && isset($role->permissions['all']) && $role->permissions['all'])
                                    Accès complet, gestion utilisateurs
                                @elseif($role->permissions)
                                    {{ implode(', ', array_keys($role->permissions)) }}
                                @else
                                    Permissions à définir
                                @endif
                            </p>
                            @empty
                            <p class="mb-0 text-lux-greyBlue">Aucun rôle défini</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <style>
        .email-switch {
            width: 2.25rem !important;
            height: 1.25rem !important;
            background-color: #e5e7eb !important;
            border-color: #e5e7eb !important;
            cursor: pointer;
        }
        .email-switch:checked {
            background-color: var(--lux-gold) !important;
            border-color: var(--lux-gold) !important;
        }
        .email-switch:focus {
            box-shadow: 0 0 0 0.25rem rgba(203, 174, 130, 0.25) !important;
        }
        .tourist-tax-switch {
            width: 2.5rem !important;
            height: 1.5rem !important;
            background-color: #e5e7eb !important;
            border-color: #e5e7eb !important;
            cursor: pointer;
        }
        .tourist-tax-switch:checked {
            background-color: var(--lux-gold) !important;
            border-color: var(--lux-gold) !important;
        }
        .tourist-tax-switch:focus {
            box-shadow: 0 0 0 0.25rem rgba(203, 174, 130, 0.25) !important;
        }
        .btn-new-policy {
            transition: all 0.3s ease;
            color: var(--lux-gold) !important;
            border-color: var(--lux-gold) !important;
        }
        .btn-new-policy:hover {
            color: var(--lux-goldHover) !important;
            border-color: var(--lux-goldHover) !important;
            background-color: rgba(203, 174, 130, 0.05) !important;
        }
    </style>

    {{-- 
    <!-- Modal Saison -->
    <div class="modal fade" id="seasonModal" tabindex="-1" aria-labelledby="seasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-serif text-lux-dark-blue" id="seasonModalLabel" style="font-family: 'Playfair Display', serif;">Nouvelle Saison</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="seasonForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="seasonMethod" value="POST">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Nom de la saison</label>
                            <input type="text" name="name" id="seasonName" class="form-control" required style="border-color: rgba(138, 150, 166, 0.3);" placeholder="ex: Haute Saison">
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Début (Mois)</label>
                                <select name="start_month" id="seasonStartMonth" class="form-select" required style="border-color: rgba(138, 150, 166, 0.3);">
                                    @foreach([1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre'] as $num => $month)
                                        <option value="{{ $num }}">{{ $num }} - {{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Début (Jour)</label>
                                <input type="number" name="start_day" id="seasonStartDay" class="form-control" min="1" max="31" value="1" required style="border-color: rgba(138, 150, 166, 0.3);">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Fin (Mois)</label>
                                <select name="end_month" id="seasonEndMonth" class="form-select" required style="border-color: rgba(138, 150, 166, 0.3);">
                                    @foreach([1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre'] as $num => $month)
                                        <option value="{{ $num }}">{{ $num }} - {{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Fin (Jour)</label>
                                <input type="number" name="end_day" id="seasonEndDay" class="form-control" min="1" max="31" value="1" required style="border-color: rgba(138, 150, 166, 0.3);">
                            </div>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="seasonIsActive" value="1" checked>
                            <label class="form-check-label small text-lux-dark-blue" for="seasonIsActive">Saison active</label>
                        </div>
                        <p class="small text-lux-greyBlue fst-italic">Note : Les saisons peuvent chevaucher deux années (ex: de Décembre à Janvier).</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn bg-lux-blue text-white">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    --}}

    <!-- Modal Administrateur -->
    <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-serif text-lux-dark-blue" id="adminModalLabel" style="font-family: 'Playfair Display', serif;">Ajouter un Administrateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="adminForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="adminMethod" value="POST">
                    <input type="hidden" name="admin_id" id="adminId">
                    <div class="modal-body">
                        <!-- Avatar Preview -->
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <div class="rounded-circle overflow-hidden position-relative" id="adminAvatarPreview" style="width: 80px; height: 80px; background-color: var(--lux-gold); display: flex; align-items-center; justify-content: center; color: white; font-weight: 600; font-size: 2rem; margin: 0 auto;">
                                    <span id="adminAvatarInitials">A</span>
                                    <img id="adminAvatarImage" src="" alt="Avatar" class="w-100 h-100 d-none" style="object-fit: cover;">
                                </div>
                                <label for="adminPhoto" class="position-absolute bottom-0 end-0 rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; cursor: pointer; border: 2px solid white;" title="Changer la photo">
                                    <i class="fa-solid fa-camera" style="font-size: 0.75rem;"></i>
                                    <input type="file" name="photo" id="adminPhoto" accept="image/*" class="d-none" onchange="previewAdminAvatar(this)">
                                </label>
                            </div>
                            <p class="small text-lux-greyBlue mt-2 mb-0">Cliquez sur l'icône pour changer la photo</p>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Prénom</label>
                                <input type="text" name="first_name" id="adminFirstName" class="form-control" required style="border-color: rgba(138, 150, 166, 0.3);" oninput="updateAdminAvatarInitials()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Nom</label>
                                <input type="text" name="last_name" id="adminLastName" class="form-control" required style="border-color: rgba(138, 150, 166, 0.3);" oninput="updateAdminAvatarInitials()">
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Email</label>
                                <input type="email" name="email" id="adminEmail" class="form-control" required style="border-color: rgba(138, 150, 166, 0.3);">
                            </div>
                            <div class="col-12" id="passwordFields">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Mot de passe</label>
                                <input type="password" name="password" id="password" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);">
                            </div>
                            <div class="col-12" id="passwordConfirmationFields">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);">
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Téléphone</label>
                                <input type="text" name="phone" id="adminPhone" class="form-control" style="border-color: rgba(138, 150, 166, 0.3);">
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Rôle</label>
                                <select name="role_id" id="adminRoleId" class="form-select" required style="border-color: rgba(138, 150, 166, 0.3);">
                                    <option value="">Sélectionner un rôle</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->formatted_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="adminIsActive" value="1" checked>
                                    <label class="form-check-label small text-lux-dark-blue" for="adminIsActive">Compte actif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn bg-lux-blue text-white">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Historique des Paramètres -->
    <div class="modal fade" id="settingsHistoryModal" tabindex="-1" aria-labelledby="settingsHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-serif text-lux-dark-blue" id="settingsHistoryModalLabel" style="font-family: 'Playfair Display', serif;">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>Historique des Modifications
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="historyLoading" class="text-center py-5">
                        <div class="spinner-border text-lux-gold" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                    <div id="historyContent" class="d-none">
                        <div id="historyList" class="list-group list-group-flush">
                            <!-- L'historique sera chargé ici dynamiquement -->
                        </div>
                        <div id="historyPagination" class="mt-3">
                            <!-- Pagination sera chargée ici -->
                        </div>
                    </div>
                    <div id="historyEmpty" class="text-center py-5 d-none">
                        <i class="fa-solid fa-inbox fa-3x text-lux-greyBlue mb-3" style="opacity: 0.3;"></i>
                        <p class="text-lux-greyBlue mb-0">Aucune modification enregistrée</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Politique d'Annulation -->
    <div class="modal fade" id="policyModal" tabindex="-1" aria-labelledby="policyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-serif text-lux-dark-blue" id="policyModalLabel" style="font-family: 'Playfair Display', serif;">Nouvelle Politique d'Annulation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="policyForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="policyMethod" value="POST">
                    <input type="hidden" name="policy_id" id="policyId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Nom de la politique</label>
                            <input type="text" name="name" id="policyName" class="form-control" required style="border-color: rgba(138, 150, 166, 0.3);">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Description</label>
                            <textarea name="description" id="policyDescription" class="form-control" rows="3" style="border-color: rgba(138, 150, 166, 0.3);"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Icône (FontAwesome)</label>
                                <input type="text" name="icon" id="policyIcon" class="form-control" value="fa-regular fa-handshake" placeholder="fa-regular fa-handshake" style="border-color: rgba(138, 150, 166, 0.3);">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2">Couleur</label>
                                <select name="color" id="policyColor" class="form-select" style="border-color: rgba(138, 150, 166, 0.3);">
                                    <option value="primary">Primary</option>
                                    <option value="success">Success</option>
                                    <option value="danger">Danger</option>
                                    <option value="warning">Warning</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-0">Règles de remboursement</label>
                                <button type="button" class="btn btn-sm text-lux-gold border border-lux-gold bg-transparent" onclick="addRefundRule()" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-plus me-1"></i>Ajouter une règle
                                </button>
                            </div>
                            <div id="refundRulesContainer">
                                <!-- Les règles seront ajoutées dynamiquement ici -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_default" id="policyIsDefault" value="1">
                                    <label class="form-check-label small text-lux-dark-blue" for="policyIsDefault">Politique par défaut</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="policyIsActive" value="1" checked>
                                    <label class="form-check-label small text-lux-dark-blue" for="policyIsActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn bg-lux-blue text-white">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let ruleCounter = 0;
let editingPolicyId = null;

function openPolicyModal() {
    editingPolicyId = null;
    document.getElementById('policyForm').reset();
    document.getElementById('policyMethod').value = 'POST';
    document.getElementById('policyForm').action = '{{ route("admin.cancellation-policies.store") }}';
    document.getElementById('policyId').value = '';
    document.getElementById('policyModalLabel').textContent = 'Nouvelle Politique d\'Annulation';
    document.getElementById('refundRulesContainer').innerHTML = '';
    ruleCounter = 0;
    addRefundRule();
}

function editPolicy(policyId) {
    editingPolicyId = policyId;
    fetch(`/admin/cancellation-policies/${policyId}`)
        .then(response => response.json())
        .then(policy => {
            document.getElementById('policyId').value = policy.id;
            document.getElementById('policyName').value = policy.name;
            document.getElementById('policyDescription').value = policy.description || '';
            document.getElementById('policyIcon').value = policy.icon || 'fa-regular fa-handshake';
            document.getElementById('policyColor').value = policy.color || 'primary';
            document.getElementById('policyIsDefault').checked = policy.is_default;
            document.getElementById('policyIsActive').checked = policy.is_active;
            
            document.getElementById('policyMethod').value = 'PUT';
            document.getElementById('policyForm').action = `/admin/cancellation-policies/${policyId}`;
            document.getElementById('policyModalLabel').textContent = 'Modifier la Politique';
            
            // Charger les règles
            document.getElementById('refundRulesContainer').innerHTML = '';
            ruleCounter = 0;
            if (policy.refund_rules && policy.refund_rules.length > 0) {
                policy.refund_rules.forEach(rule => {
                    addRefundRule(rule.days_before, rule.refund_percentage);
                });
            } else {
                addRefundRule();
            }
            
            new bootstrap.Modal(document.getElementById('policyModal')).show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement de la politique');
        });
}

function addRefundRule(daysBefore = '', refundPercentage = '') {
    ruleCounter++;
    const container = document.getElementById('refundRulesContainer');
    const ruleDiv = document.createElement('div');
    ruleDiv.className = 'border rounded p-3 mb-2';
    ruleDiv.style.borderColor = 'rgba(138, 150, 166, 0.2)';
    ruleDiv.innerHTML = `
        <div class="row g-2">
            <div class="col-md-5">
                <label class="small text-lux-greyBlue mb-1">Jours avant l'arrivée</label>
                <input type="number" name="refund_rules[${ruleCounter}][days_before]" class="form-control form-control-sm" value="${daysBefore}" min="0" required style="border-color: rgba(138, 150, 166, 0.3);">
            </div>
            <div class="col-md-5">
                <label class="small text-lux-greyBlue mb-1">Pourcentage de remboursement (%)</label>
                <input type="number" name="refund_rules[${ruleCounter}][refund_percentage]" class="form-control form-control-sm" value="${refundPercentage}" min="0" max="100" step="0.1" required style="border-color: rgba(138, 150, 166, 0.3);">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-danger w-100" onclick="this.closest('.border').remove()">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(ruleDiv);
}

// Le formulaire sera soumis normalement, Laravel gérera la conversion des refund_rules

// Gestion de la déconnexion pour le bouton "Voir le site"
document.addEventListener('DOMContentLoaded', function() {
    const viewSiteBtn = document.getElementById('admin-view-site-btn');
    if (viewSiteBtn) {
        viewSiteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const logoutUrl = '{{ route("api.auth.logout") }}';
            const csrfToken = '{{ csrf_token() }}';
            const homeUrl = '{{ route("home") }}';
            
            const formData = new FormData();
            formData.append('_token', csrfToken);
            
            fetch(logoutUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                window.location.href = homeUrl;
            })
            .catch(error => {
                console.error('Erreur lors de la déconnexion:', error);
                window.location.href = homeUrl;
            });
        });
    }
});

// Gestion des administrateurs
let editingAdminId = null;

function openAdminModal() {
    editingAdminId = null;
    document.getElementById('adminForm').reset();
    document.getElementById('adminMethod').value = 'POST';
    document.getElementById('adminForm').action = '{{ route("admin.settings.admins.store") }}';
    document.getElementById('adminModalLabel').textContent = 'Ajouter un Administrateur';
    document.getElementById('adminId').value = '';
    document.getElementById('passwordFields').style.display = 'block';
    document.getElementById('passwordConfirmationFields').style.display = 'block';
    document.getElementById('password').required = true;
    document.getElementById('password_confirmation').required = true;
    // Réinitialiser l'avatar
    if (document.getElementById('adminAvatarImage')) {
        document.getElementById('adminAvatarImage').classList.add('d-none');
        document.getElementById('adminAvatarInitials').classList.remove('d-none');
        updateAdminAvatarInitials();
    }
}

function editAdmin(adminId) {
    editingAdminId = adminId;
    fetch(`/admin/settings/admins/${adminId}`)
        .then(response => response.json())
        .then(admin => {
            document.getElementById('adminId').value = admin.id;
            document.getElementById('adminFirstName').value = admin.first_name;
            document.getElementById('adminLastName').value = admin.last_name;
            document.getElementById('adminEmail').value = admin.email;
            document.getElementById('adminPhone').value = admin.phone || '';
            document.getElementById('adminRoleId').value = admin.role_id || '';
            document.getElementById('adminIsActive').checked = admin.is_active;
            
            // Afficher l'avatar si disponible
            if (admin.photo_url) {
                document.getElementById('adminAvatarImage').src = admin.photo_url;
                document.getElementById('adminAvatarImage').classList.remove('d-none');
                document.getElementById('adminAvatarInitials').classList.add('d-none');
            } else {
                document.getElementById('adminAvatarImage').classList.add('d-none');
                document.getElementById('adminAvatarInitials').classList.remove('d-none');
                updateAdminAvatarInitials();
            }
            
            document.getElementById('adminMethod').value = 'PUT';
            document.getElementById('adminForm').action = `/admin/settings/admins/${adminId}`;
            document.getElementById('adminModalLabel').textContent = 'Modifier l\'Administrateur';
            document.getElementById('passwordFields').style.display = 'none';
            document.getElementById('passwordConfirmationFields').style.display = 'none';
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
            
            new bootstrap.Modal(document.getElementById('adminModal')).show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement de l\'administrateur');
        });
}

function previewAdminAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('adminAvatarImage').src = e.target.result;
            document.getElementById('adminAvatarImage').classList.remove('d-none');
            document.getElementById('adminAvatarInitials').classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateAdminAvatarInitials() {
    const firstName = document.getElementById('adminFirstName').value || '';
    const lastName = document.getElementById('adminLastName').value || '';
    const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'A';
    document.getElementById('adminAvatarInitials').textContent = initials;
    
    // Si pas d'image, afficher les initiales
    if (!document.getElementById('adminAvatarImage').src || document.getElementById('adminAvatarImage').classList.contains('d-none')) {
        document.getElementById('adminAvatarInitials').classList.remove('d-none');
    }
}

function deleteAdmin(adminId) {
    if (!confirm('Êtes-vous sûr de vouloir retirer cet administrateur ?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/settings/admins/${adminId}`;
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = '{{ csrf_token() }}';
    form.appendChild(tokenInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Gestion de l'historique des paramètres
function loadSettingsHistory(page = 1) {
    const loadingEl = document.getElementById('historyLoading');
    const contentEl = document.getElementById('historyContent');
    const emptyEl = document.getElementById('historyEmpty');
    const listEl = document.getElementById('historyList');
    const paginationEl = document.getElementById('historyPagination');

    if (!loadingEl || !contentEl || !emptyEl || !listEl || !paginationEl) {
        console.error('Éléments du modal d\'historique non trouvés');
        return;
    }

    loadingEl.classList.remove('d-none');
    contentEl.classList.add('d-none');
    emptyEl.classList.add('d-none');
    listEl.innerHTML = '';

    fetch(`/admin/settings/history?page=${page}`)
        .then(response => response.json())
        .then(data => {
            loadingEl.classList.add('d-none');

            if (data.data && data.data.length > 0) {
                contentEl.classList.remove('d-none');
                
                data.data.forEach(entry => {
                    const entryEl = document.createElement('div');
                    entryEl.className = 'list-group-item border-0 px-0 py-3';
                    
                    const date = new Date(entry.created_at);
                    const formattedDate = date.toLocaleDateString('fr-FR', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const settingName = getSettingName(entry.setting_key);
                    const oldValue = formatSettingValue(entry.setting_key, entry.old_value);
                    const newValue = formatSettingValue(entry.setting_key, entry.new_value);
                    const userName = entry.changed_by_user ? 
                        `${entry.changed_by_user.first_name} ${entry.changed_by_user.last_name}` : 
                        'Système';

                    entryEl.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-fill">
                                <h6 class="mb-1 text-lux-dark-blue fw-semibold">${settingName}</h6>
                                <p class="small text-lux-greyBlue mb-0">
                                    <i class="fa-solid fa-user me-1"></i>${userName}
                                    <span class="ms-2"><i class="fa-solid fa-clock me-1"></i>${formattedDate}</span>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 small flex-wrap">
                            <span class="text-lux-greyBlue">Ancienne valeur:</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">${oldValue}</span>
                            <i class="fa-solid fa-arrow-right text-lux-gold"></i>
                            <span class="text-lux-greyBlue">Nouvelle valeur:</span>
                            <span class="badge bg-lux-gold bg-opacity-10 text-lux-gold px-2 py-1">${newValue}</span>
                        </div>
                    `;
                    listEl.appendChild(entryEl);
                });

                if (data.last_page > 1) {
                    let paginationHTML = '<nav><ul class="pagination pagination-sm justify-content-center mb-0">';
                    if (data.current_page > 1) {
                        paginationHTML += `<li class="page-item"><a class="page-link text-lux-gold" href="#" onclick="loadSettingsHistory(${data.current_page - 1}); return false;">Précédent</a></li>`;
                    }
                    for (let i = 1; i <= data.last_page; i++) {
                        if (i === 1 || i === data.last_page || (i >= data.current_page - 1 && i <= data.current_page + 1)) {
                            paginationHTML += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                                <a class="page-link ${i === data.current_page ? 'bg-lux-gold border-lux-gold' : 'text-lux-gold'}" href="#" onclick="loadSettingsHistory(${i}); return false;">${i}</a>
                            </li>`;
                        } else if (i === data.current_page - 2 || i === data.current_page + 2) {
                            paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    if (data.current_page < data.last_page) {
                        paginationHTML += `<li class="page-item"><a class="page-link text-lux-gold" href="#" onclick="loadSettingsHistory(${data.current_page + 1}); return false;">Suivant</a></li>`;
                    }
                    paginationHTML += '</ul></nav>';
                    paginationEl.innerHTML = paginationHTML;
                } else {
                    paginationEl.innerHTML = '';
                }
            } else {
                emptyEl.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement de l\'historique:', error);
            loadingEl.classList.add('d-none');
            emptyEl.classList.remove('d-none');
            emptyEl.innerHTML = '<i class="fa-solid fa-exclamation-triangle fa-3x text-warning mb-3"></i><p class="text-lux-greyBlue mb-0">Erreur lors du chargement de l\'historique</p>';
        });
}

function getSettingName(key) {
    const names = {
        'global_tax_rate': 'TVA Globale',
        'tourist_tax_per_night': 'Taxe de Séjour (Par nuit/pers)',
        'tourist_tax_enabled': 'Taxe de Séjour (Activation)',
        'service_fee_percentage': 'Frais de Service (%)',
        'deposit_percentage_min': 'Acompte Minimum (%)',
        'deposit_percentage_max': 'Acompte Maximum (%)',
        'balance_due_days_before_checkin': 'Jours avant arrivée - Paiement du solde',
        'deposit_guarantee_days_before_checkin': 'Jours avant arrivée - Dépôt de garantie',
        'cancellation_policy_days': 'Délai d\'annulation (jours)',
    };
    return names[key] || key;
}

function formatSettingValue(key, value) {
    if (value === null || value === undefined || value === '') {
        return 'N/A';
    }
    if (key === 'tourist_tax_enabled') {
        return value === '1' || value === true || value === 'true' ? 'Activée' : 'Désactivée';
    }
    if (['global_tax_rate', 'service_fee_percentage', 'deposit_percentage_min', 'deposit_percentage_max'].includes(key)) {
        return parseFloat(value).toFixed(2).replace('.', ',') + ' %';
    }
    if (key === 'tourist_tax_per_night') {
        return parseFloat(value).toFixed(2).replace('.', ',') + ' €';
    }
    if (['balance_due_days_before_checkin', 'deposit_guarantee_days_before_checkin', 'cancellation_policy_days'].includes(key)) {
        return parseInt(value) + ' jour' + (parseInt(value) > 1 ? 's' : '');
    }
    return value;
}

// Gestion des Saisons
function openSeasonModal() {
    document.getElementById('seasonForm').reset();
    document.getElementById('seasonMethod').value = 'POST';
    document.getElementById('seasonForm').action = '{{ route("admin.seasons.store") }}';
    document.getElementById('seasonModalLabel').textContent = 'Nouvelle Saison';
}

function editSeason(id) {
    fetch(`/admin/seasons/${id}`)
        .then(response => response.json())
        .then(season => {
            document.getElementById('seasonName').value = season.name;
            document.getElementById('seasonStartMonth').value = season.start_month;
            document.getElementById('seasonStartDay').value = season.start_day;
            document.getElementById('seasonEndMonth').value = season.end_month;
            document.getElementById('seasonEndDay').value = season.end_day;
            document.getElementById('seasonIsActive').checked = season.is_active;
            
            document.getElementById('seasonMethod').value = 'PUT';
            document.getElementById('seasonForm').action = `/admin/seasons/${id}`;
            document.getElementById('seasonModalLabel').textContent = 'Modifier la Saison';
            
            new bootstrap.Modal(document.getElementById('seasonModal')).show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement de la saison');
        });
}

// Ouvrir automatiquement l'onglet des saisons si demandé
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab === 'seasons') {
        // Scroller jusqu'à la section des saisons
        const seasonSection = document.querySelector('.btn-new-season');
        if (seasonSection) {
            seasonSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
});
</script>
@endpush

