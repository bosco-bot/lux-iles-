@extends('layouts.dashboard')

@section('title', 'Mes Documents | LUXÎLES - Dashboard')

@section('content')
    <!-- Page Header / Hero Minimal -->
    <section id="documents-hero" class="position-relative" style="height: 250px; background-color: var(--lux-dark-blue); overflow: hidden; margin-top: -1rem; margin-left: -1rem; margin-right: -1rem; margin-bottom: 2rem;">
        <style>
            @media (min-width: 768px) {
                #documents-hero {
                    margin-top: -2rem !important;
                    margin-left: -2rem !important;
                    margin-right: -2rem !important;
                }
            }
        </style>
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-40">
            <img src="{{ asset('documents-hero.png') }}" class="w-100 h-100" style="object-fit: cover;" alt="Mes Documents">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(10,26,47,0.7) 0%, rgba(10,26,47,0.4) 50%, rgba(10,26,47,0.8) 100%);"></div>
        </div>
        <div class="position-relative z-10 h-100 d-flex align-items-center justify-content-center text-center" style="padding-top: 3rem;">
            <div>
                <h1 class="h1 font-serif text-white mb-2" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">Mes Documents</h1>
                <p class="text-lux-gold text-uppercase small fw-medium mb-0" style="letter-spacing: 0.2em; font-size: 0.875rem;">Contrats & Factures</p>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="container-fluid px-4">
        <!-- Filters & Actions Bar -->
        <div class="card border mb-4 shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
            <div class="card-body p-4">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-auto">
                        <form method="GET" action="{{ route('espace-client.documents') }}" id="filterForm">
                            <div class="position-relative d-flex align-items-center">
                                <i class="fa-regular fa-calendar position-absolute start-0 ms-3 text-lux-gold" style="z-index: 10;"></i>
                                <select name="period" class="form-select form-select-sm ps-5" style="border-color: transparent; background-color: rgba(248, 248, 246, 0.5); transition: border-color 0.3s; height: 31.5px;" onchange="document.getElementById('filterForm').submit();" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.3)'" onmouseout="this.style.borderColor='transparent'" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='transparent'">
                                    <option value="">Tous les documents</option>
                                    <option value="30days" {{ request('period') == '30days' ? 'selected' : '' }}>Derniers 30 jours</option>
                                    @foreach($availableYears as $year)
                                        <option value="year" data-year="{{ $year }}" {{ request('period') == 'year' && request('year') == $year ? 'selected' : '' }}>Année {{ $year }}</option>
                                    @endforeach
                                </select>
                                @if(request('period') == 'year')
                                    <input type="hidden" name="year" value="{{ request('year', now()->year) }}">
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-auto ms-md-auto">
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: rgba(138, 150, 166, 0.2); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'; this.style.color='inherit'">
                                    <i class="fa-solid fa-filter text-lux-gold"></i> Filtrer par type
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                                    <li><a class="dropdown-item" href="{{ route('espace-client.documents', array_merge(request()->except('type'), ['type' => ''])) }}">Tous les types</a></li>
                                    @foreach($availableTypes as $typeKey => $typeLabel)
                                        <li><a class="dropdown-item" href="{{ route('espace-client.documents', array_merge(request()->except('type'), ['type' => $typeKey])) }}">{{ $typeLabel }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            @if($documents->count() > 0)
                                <a href="#" class="btn btn-lux-primary btn-sm d-flex align-items-center gap-2 shadow-sm" onclick="downloadAll(); return false;">
                                    <i class="fa-solid fa-download"></i> Tout télécharger
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($recentClientDocuments) && $recentClientDocuments->count() > 0)
        <section id="client-dossier" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 font-serif mb-0" style="color: var(--lux-dark-blue);">Mon dossier</h2>
                <div class="small text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Contrats & accords LUXÎLES</div>
            </div>
            <div class="row g-4">
                @foreach($recentClientDocuments as $clientDoc)
                    <div class="col-12 col-md-6">
                        <div class="card border h-100" style="border-color: rgba(138, 150, 166, 0.15) !important; border-radius: 0.5rem;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="rounded d-flex align-items-center justify-content-center text-lux-dark-blue" style="width: 48px; height: 48px; background-color: rgba(10, 26, 47, 0.05);">
                                        <i class="fa-solid fa-file-{{ $clientDoc->extension === 'pdf' ? 'pdf' : 'word' }} fs-5 text-danger"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-serif h5 mb-1" style="color: var(--lux-dark-blue);">{{ $clientDoc->title }}</h3>
                                        <p class="small text-lux-gray mb-0">Dossier personnel</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between small text-lux-gray border-top pt-3">
                                    <span><i class="fa-regular fa-clock me-2"></i>{{ $clientDoc->created_at->format('d M Y') }}</span>
                                    <span class="fw-medium text-lux-dark-blue">{{ $clientDoc->formatted_file_size }}</span>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('espace-client.documents.client-documents.download', $clientDoc) }}" class="btn btn-sm btn-lux-primary w-100 text-decoration-none">
                                        <i class="fa-solid fa-download me-1"></i> Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(isset($clientDocuments) && $clientDocuments->count() > $recentClientDocuments->count())
                <p class="small text-lux-gray mt-3 mb-0">Voir tous les documents du dossier dans l'historique ci-dessous.</p>
            @endif
        </section>
        @endif

        <!-- Variant A: Card View (Default) -->
        <section id="documents-cards" class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 font-serif mb-0" style="color: var(--lux-dark-blue);">Documents Récents</h2>
                <div class="small text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Vue Cartes</div>
            </div>
            
            @if($recentDocuments->count() > 0)
                <div class="row g-4">
                    @foreach($recentDocuments as $document)
                        @php
                            $typeIcons = [
                                'contract' => 'fa-file-contract',
                                'invoice' => 'fa-file-invoice',
                                'receipt' => 'fa-file-lines',
                                'deposit_receipt' => 'fa-file-lines',
                                'balance_receipt' => 'fa-file-lines',
                                'guarantee_receipt' => 'fa-file-shield',
                                'cancellation' => 'fa-file-times',
                            ];
                            $icon = $typeIcons[$document->type] ?? 'fa-file-pdf';
                            
                            // Certaines icônes nécessitent fa-solid au lieu de fa-regular
                            $solidIcons = ['fa-file-contract', 'fa-file-invoice', 'fa-file-times', 'fa-file-pdf'];
                            $iconClass = in_array($icon, $solidIcons) ? 'fa-solid' : 'fa-regular';
                            
                            $typeLabels = [
                                'contract' => 'Contrat de Location',
                                'invoice' => 'Facture',
                                'receipt' => 'Reçu',
                                'deposit_receipt' => 'Reçu d\'arrhes',
                                'balance_receipt' => 'Reçu de solde',
                                'guarantee_receipt' => 'Reçu de caution',
                                'cancellation' => 'Annulation',
                            ];
                            $typeLabel = $typeLabels[$document->type] ?? 'Document';
                            
                            $villaName = $document->reservation && $document->reservation->villa 
                                ? $document->reservation->villa->name 
                                : 'N/A';
                            $islandName = $document->reservation && $document->reservation->villa && $document->reservation->villa->island
                                ? $document->reservation->villa->island->name
                                : '';
                            $location = $villaName . ($islandName ? ' - ' . $islandName : '');
                        @endphp
                        <div class="col-12 col-md-6">
                            <div class="card border h-100 position-relative overflow-hidden" style="border-color: transparent; border-radius: 0.5rem; transition: all 0.3s;" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.2)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='none'">
                                <div class="position-absolute top-0 end-0 rounded-bottom-start" style="width: 64px; height: 64px; background-color: rgba(203, 174, 130, 0.05); transform: translate(32px, -32px); transition: transform 0.5s;" onmouseover="this.style.transform='translate(32px, -32px) scale(1.5)'" onmouseout="this.style.transform='translate(32px, -32px) scale(1)'"></div>
                                <div class="card-body p-4 position-relative" style="z-index: 10;">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded d-flex align-items-center justify-content-center text-lux-dark-blue" style="width: 48px; height: 48px; background-color: rgba(10, 26, 47, 0.05); transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-gold)'; this.style.color='white'" onmouseout="this.style.backgroundColor='rgba(10, 26, 47, 0.05)'; this.style.color='var(--lux-dark-blue)'">
                                                <i class="{{ $iconClass }} {{ $icon }} fs-5"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-serif h5 mb-1" style="color: var(--lux-dark-blue);">{{ $typeLabel }}</h3>
                                                <p class="small text-lux-gray mb-0">{{ $location }}</p>
                                            </div>
                                        </div>
                                        @if($document->is_signed)
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 small border border-success border-opacity-25">Signé</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 small border border-secondary border-opacity-25">Non signé</span>
                                        @endif
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between small text-lux-gray border-top pt-3 mt-2" style="border-color: rgba(0,0,0,0.1) !important;">
                                        <span><i class="fa-regular fa-clock me-2"></i>{{ $document->created_at->format('d M Y') }}</span>
                                        <span class="fw-medium text-lux-dark-blue">{{ $document->formatted_file_size }}</span>
                                    </div>
                                    
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="{{ route('espace-client.documents.download', $document) }}" target="_blank" class="btn btn-sm flex-grow-1 btn-document-preview text-decoration-none">Aperçu</a>
                                        <a href="{{ route('espace-client.documents.download', $document) }}" class="btn btn-sm flex-grow-1 btn-document-download text-decoration-none">
                                            <i class="fa-solid fa-download me-1"></i> PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-regular fa-file-lines fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                    <p class="text-lux-gray mb-0">Aucun document disponible</p>
                </div>
            @endif
        </section>

        @if(isset($clientDocuments) && $clientDocuments->count() > 0)
        <section id="client-dossier-table" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">Dossier personnel</h2>
            </div>
            <div class="card border shadow-sm mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="bg-light border-bottom" style="background-color: rgba(248, 248, 246, 0.5) !important;">
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase">Document</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase">Date</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase">Fichier</th>
                                <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientDocuments as $clientDoc)
                                <tr>
                                    <td class="px-4 py-3 fw-medium text-lux-dark-blue">{{ $clientDoc->title }}</td>
                                    <td class="px-4 py-3 text-lux-gray">{{ $clientDoc->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-lux-gray small">{{ $clientDoc->file_name }} ({{ $clientDoc->formatted_file_size }})</td>
                                    <td class="px-4 py-3 text-end">
                                        <a href="{{ route('espace-client.documents.client-documents.download', $clientDoc) }}" class="btn btn-link text-lux-gold p-2 border-0 text-decoration-none">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif

        <!-- Variant B: Table View -->
        <section id="documents-table" class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">Historique réservations</h2>
                <div class="small text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Vue Liste Détaillée</div>
            </div>

            @if($documents->count() > 0)
                <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="bg-light border-bottom" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Document</th>
                                    <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Date</th>
                                    <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Référence</th>
                                    <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Statut</th>
                                    <th class="px-4 py-3 small fw-bold text-lux-gray text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    @php
                                        $typeIcons = [
                                            'contract' => 'fa-file-contract',
                                            'invoice' => 'fa-file-invoice',
                                            'receipt' => 'fa-file-lines',
                                            'deposit_receipt' => 'fa-file-lines',
                                            'balance_receipt' => 'fa-file-lines',
                                            'guarantee_receipt' => 'fa-file-shield',
                                            'cancellation' => 'fa-file-times',
                                        ];
                                        $icon = $typeIcons[$document->type] ?? 'fa-file-pdf';
                                        
                                        // Certaines icônes nécessitent fa-solid au lieu de fa-regular
                                        $solidIcons = ['fa-file-contract', 'fa-file-invoice', 'fa-file-times', 'fa-file-pdf'];
                                        $iconClass = in_array($icon, $solidIcons) ? 'fa-solid' : 'fa-regular';
                                        
                                        $typeLabels = [
                                            'contract' => 'Contrat de Location',
                                            'invoice' => 'Facture',
                                            'receipt' => 'Reçu',
                                            'deposit_receipt' => 'Reçu d\'arrhes',
                                            'balance_receipt' => 'Reçu de solde',
                                            'guarantee_receipt' => 'Reçu de caution',
                                            'cancellation' => 'Annulation',
                                        ];
                                        $typeLabel = $typeLabels[$document->type] ?? 'Document';
                                    @endphp
                                    <tr style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="{{ $iconClass }} {{ $icon }} text-danger fs-5"></i>
                                                <span class="fw-medium text-lux-dark-blue">{{ $typeLabel }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-lux-gray">{{ $document->created_at->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-lux-gray small" style="font-family: monospace; font-size: 0.75rem;">{{ $document->document_number }}</td>
                                        <td class="px-4 py-3">
                                            @if($document->is_signed)
                                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small border border-success border-opacity-25 d-inline-flex align-items-center gap-1">
                                                    <span class="rounded-circle bg-success" style="width: 6px; height: 6px;"></span> Signé
                                                </span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill small border border-secondary border-opacity-25 d-inline-flex align-items-center gap-1">
                                                    <span class="rounded-circle bg-secondary" style="width: 6px; height: 6px;"></span> Non signé
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <a href="{{ route('espace-client.documents.download', $document) }}" class="btn btn-link text-lux-gold p-2 rounded-circle border-0 text-decoration-none" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.1)'; this.style.color='var(--lux-light-gold)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-gold)'">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($documents->hasPages())
                        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                            <div class="small text-lux-gray">
                                Affichage de {{ $documents->firstItem() }} à {{ $documents->lastItem() }} sur {{ $documents->total() }} documents
                            </div>
                            <div>
                                {{ $documents->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="card border shadow-sm text-center py-5" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
                    <i class="fa-regular fa-file-lines fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                    <p class="text-lux-gray mb-0">Aucun document disponible</p>
                </div>
            @endif
        </section>

        <!-- Variant C: Simple List -->
        <section id="documents-simple-list" class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 font-serif mb-0" style="color: var(--lux-dark-blue);">Tous les Documents</h2>
                <div class="small text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Vue Simplifiée</div>
            </div>

            @if($documents->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($documents as $document)
                        @php
                            $typeLabels = [
                                'contract' => 'Contrat de Location',
                                'invoice' => 'Facture',
                                'receipt' => 'Reçu',
                                'deposit_receipt' => 'Reçu d\'arrhes',
                                'balance_receipt' => 'Reçu de solde',
                                'guarantee_receipt' => 'Reçu de caution',
                                'cancellation' => 'Annulation',
                            ];
                            $typeLabel = $typeLabels[$document->type] ?? 'Document';
                            
                            $villaName = $document->reservation && $document->reservation->villa 
                                ? $document->reservation->villa->name 
                                : '';
                            $documentTitle = $typeLabel . ($villaName ? ' - ' . $villaName : '');
                        @endphp
                        <a href="{{ route('espace-client.documents.download', $document) }}" class="card border shadow-sm text-decoration-none" style="border-color: transparent; border-radius: 0.5rem; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.3)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-lux-gray" style="width: 40px; height: 40px; background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">
                                            <i class="fa-solid fa-file small"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="small fw-medium text-lux-dark-blue" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-dark-blue)'">{{ $documentTitle }}</span>
                                            <span class="small text-lux-gray">Ajouté le {{ $document->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-chevron-right small text-lux-gray" style="transition: transform 0.3s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($documents->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $documents->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fa-regular fa-file-lines fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                    <p class="text-lux-gray mb-0">Aucun document disponible</p>
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
<script>
    // Toggle between views (if needed in the future)
    function showDocumentsView(viewType) {
        document.getElementById('documents-cards').classList.add('d-none');
        document.getElementById('documents-table').classList.add('d-none');
        document.getElementById('documents-simple-list').classList.add('d-none');
        
        if (viewType === 'cards') {
            document.getElementById('documents-cards').classList.remove('d-none');
        } else if (viewType === 'table') {
            document.getElementById('documents-table').classList.remove('d-none');
        } else if (viewType === 'list') {
            document.getElementById('documents-simple-list').classList.remove('d-none');
        }
    }
    
    // Télécharger tous les documents
    function downloadAll() {
        const downloadLinks = document.querySelectorAll('a[href*="/documents/"][href*="/download"]');
        if (downloadLinks.length === 0) {
            alert('Aucun document à télécharger');
            return;
        }
        
        // Télécharger chaque document avec un délai pour éviter de bloquer le navigateur
        downloadLinks.forEach((link, index) => {
            setTimeout(() => {
                window.open(link.href, '_blank');
            }, index * 500); // 500ms entre chaque téléchargement
        });
    }
</script>
@endpush

