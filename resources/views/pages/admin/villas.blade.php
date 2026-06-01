@extends('layouts.admin')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Gestion des Villas | LUXÎLES - Back-office')

@section('admin-breadcrumbs')
    <span class="text-white">Villas</span>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Gestion des Villas
            </h1>
            <p class="text-muted small mb-0">Gérez votre parc immobilier de prestige.</p>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            <div class="position-relative d-none d-sm-block">
                <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-lux-gray"></i>
                <input type="text" id="villa-search-input" placeholder="Rechercher une villa..." class="form-control form-control-sm ps-5" style="width: 256px; border-color: rgba(138, 150, 166, 0.3); transition: all 0.3s;" onfocus="this.style.borderColor='var(--lux-gold)'; this.style.boxShadow='0 0 0 1px var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.3)'; this.style.boxShadow='none'">
            </div>
            <a href="{{ route('admin.villas.create') }}" class="btn btn-lux-primary btn-sm d-flex align-items-center gap-2 shadow-sm text-white text-decoration-none">
                <i class="fa-solid fa-plus text-white"></i> <span class="text-white">Ajouter une villa</span>
            </a>
        </div>
    </div>

    <!-- Messages d'erreur/succès -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Overview -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-lux-dark-blue" style="width: 48px; height: 48px; background-color: rgba(10, 26, 47, 0.05);">
                            <i class="fa-solid fa-house fs-5"></i>
                        </div>
                        <div>
                            <p class="small text-lux-gray text-uppercase fw-medium mb-1" style="font-size: 0.75rem;">Total Villas</p>
                            <p class="h4 font-serif mb-0 fw-bold" style="color: var(--lux-dark-blue);">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px; background-color: rgba(25, 135, 84, 0.1);">
                            <i class="fa-solid fa-check fs-5"></i>
                        </div>
                        <div>
                            <p class="small text-lux-gray text-uppercase fw-medium mb-1" style="font-size: 0.75rem;">Disponibles</p>
                            <p class="h4 font-serif mb-0 fw-bold" style="color: var(--lux-dark-blue);">{{ $stats['active'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 48px; height: 48px; background-color: rgba(255, 193, 7, 0.1);">
                            <i class="fa-solid fa-screwdriver-wrench fs-5"></i>
                        </div>
                        <div>
                            <p class="small text-lux-gray text-uppercase fw-medium mb-1" style="font-size: 0.75rem;">Maintenance</p>
                            <p class="h4 font-serif mb-0 fw-bold" style="color: var(--lux-dark-blue);">{{ $stats['inactive'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-lux-gold" style="width: 48px; height: 48px; background-color: rgba(203, 174, 130, 0.1);">
                            <i class="fa-solid fa-star fs-5"></i>
                        </div>
                        <div>
                            <p class="small text-lux-gray text-uppercase fw-medium mb-1" style="font-size: 0.75rem;">Note Moyenne</p>
                            <p class="h4 font-serif mb-0 fw-bold" style="color: var(--lux-dark-blue);">{{ $stats['featured'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle Tabs -->
    <div class="mb-4 border-bottom" style="border-color: rgba(138, 150, 166, 0.2) !important;">
        <ul class="nav nav-tabs border-0">
            <li class="nav-item">
                <button class="nav-link active border-0 bg-transparent text-lux-dark-blue fw-medium d-flex align-items-center gap-2" onclick="showVillaView('table')" id="btn-view-table" style="padding-bottom: 0.75rem; border-bottom: 2px solid var(--lux-gold) !important; transition: all 0.3s;">
                    <i class="fa-solid fa-list"></i> Liste Détaillée
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link border-0 bg-transparent text-lux-gray fw-medium d-flex align-items-center gap-2" onclick="showVillaView('grid')" id="btn-view-grid" style="padding-bottom: 0.75rem; border-bottom: 2px solid transparent !important; transition: all 0.3s;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="if(!this.classList.contains('active')) { this.style.color='var(--lux-gray)'; }">
                    <i class="fa-solid fa-border-all"></i> Grille
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link border-0 bg-transparent text-lux-gray fw-medium d-flex align-items-center gap-2" onclick="showVillaView('horizontal')" id="btn-view-horizontal" style="padding-bottom: 0.75rem; border-bottom: 2px solid transparent !important; transition: all 0.3s;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="if(!this.classList.contains('active')) { this.style.color='var(--lux-gray)'; }">
                    <i class="fa-solid fa-grip-lines"></i> Cartes Horizontales
                </button>
            </li>
        </ul>
    </div>

    <!-- VARIANT A: Premium Table View (Default) -->
    <div id="view-table" class="card border shadow-sm mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="bg-light" style="background-color: rgba(10, 26, 47, 0.05) !important;">
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Villa</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Lieu</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Prix / Nuit</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Statut</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Capacité</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villas as $villa)
                        @php
                            $primaryPhoto = $villa->photos->where('is_primary', true)->first() ?? $villa->photos->first();
                            $photoUrl = $primaryPhoto ? Storage::url($primaryPhoto->file_path) : 'https://via.placeholder.com/96x64?text=No+Photo';
                        @endphp
                        <tr style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(248, 248, 246, 0.5)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded overflow-hidden flex-shrink-0" style="width: 96px; height: 64px;">
                                        <img src="{{ $photoUrl }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $villa->name }}">
                                    </div>
                                    <div>
                                        <p class="font-serif text-lux-dark-blue fw-medium h5 mb-1">{{ $villa->name }}</p>
                                        <p class="small text-lux-gray mb-0">Ref: V-{{ str_pad($villa->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 small text-lux-dark-blue">{{ $villa->island->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 small fw-medium text-lux-dark-blue">{{ number_format($villa->base_price_per_night, 0, ',', ' ') }} €</td>
                            <td class="px-4 py-3">
                                @if($villa->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small border border-success border-opacity-25 d-inline-flex align-items-center gap-1">
                                        <span class="rounded-circle bg-success" style="width: 6px; height: 6px;"></span> Actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill small border border-secondary border-opacity-25 d-inline-flex align-items-center gap-1">
                                        <span class="rounded-circle bg-secondary" style="width: 6px; height: 6px;"></span> Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 small text-lux-gray">
                                <i class="fa-solid fa-users text-lux-gold opacity-70 me-1"></i> {{ $villa->max_capacity }} Pers.
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="{{ route('admin.villas.edit', $villa->id) }}" class="btn btn-link p-2 rounded text-decoration-none" style="color: var(--lux-gold); border: 1px solid rgba(138, 150, 166, 0.2); transition: all 0.3s;" onmouseover="this.style.color='var(--lux-gold)'; this.style.borderColor='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gold)'; this.style.borderColor='rgba(138, 150, 166, 0.2)'" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.villas.destroy', $villa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette villa ? Cette action est irréversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-2 rounded" style="color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.3); transition: all 0.3s;" onmouseover="this.style.color='#b02a37'; this.style.borderColor='#b02a37'" onmouseout="this.style.color='#dc3545'; this.style.borderColor='rgba(220, 53, 69, 0.3)'" title="Supprimer">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center">
                                <div class="d-flex flex-column align-items-center gap-3">
                                    <i class="fa-solid fa-house-circle-exclamation fs-1 text-lux-gray opacity-50"></i>
                                    <p class="text-lux-gray mb-0">Aucune villa enregistrée</p>
                                    <a href="{{ route('admin.villas.create') }}" class="btn btn-lux-primary btn-sm text-white text-decoration-none">
                                        <i class="fa-solid fa-plus me-2"></i> Créer votre première villa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        @if($villas->hasPages())
        <div class="card-footer bg-light border-top d-flex align-items-center justify-content-between py-3 px-4" style="background-color: rgba(248, 248, 246, 0.3) !important; border-color: rgba(138, 150, 166, 0.1) !important;">
            <span class="small text-lux-gray">
                Affichage de {{ $villas->firstItem() }} à {{ $villas->lastItem() }} sur {{ $villas->total() }} villas
            </span>
            <div class="d-flex gap-2">
                @if($villas->onFirstPage())
                    <button class="btn btn-sm border rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-gray);" disabled>
                        <i class="fa-solid fa-chevron-left small"></i>
                    </button>
                @else
                    <a href="{{ $villas->previousPageUrl() }}" class="btn btn-sm border rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-gray); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'; this.style.color='var(--lux-gray)'">
                        <i class="fa-solid fa-chevron-left small"></i>
                    </a>
                @endif
                
                @foreach($villas->getUrlRange(1, min(5, $villas->lastPage())) as $page => $url)
                    @if($page == $villas->currentPage())
                        <button class="btn btn-sm border rounded d-flex align-items-center justify-content-center bg-lux-gold text-white shadow-sm" style="width: 32px; height: 32px; border-color: var(--lux-gold);">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" class="btn btn-sm border rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-gray); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'; this.style.color='var(--lux-gray)'">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($villas->hasMorePages())
                    <a href="{{ $villas->nextPageUrl() }}" class="btn btn-sm border rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-gray); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'; this.style.color='var(--lux-gray)'">
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                @else
                    <button class="btn btn-sm border rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-gray);" disabled>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- VARIANT B: Grid View -->
    <div id="view-grid" class="d-none row g-4 mb-4">
        @forelse($villas as $villa)
            @php
                $primaryPhoto = $villa->photos->where('is_primary', true)->first() ?? $villa->photos->first();
                $photoUrl = $primaryPhoto ? Storage::url($primaryPhoto->file_path) : 'https://via.placeholder.com/400x300?text=No+Photo';
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border shadow-sm h-100" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem; overflow: hidden; transition: box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                    <div class="position-relative" style="height: 192px; overflow: hidden;">
                        @if($villa->is_active)
                            <span class="position-absolute top-0 end-0 m-3 badge bg-success bg-opacity-90 text-white px-2 py-1 rounded-pill small border border-success border-opacity-25 d-inline-flex align-items-center gap-1" style="z-index: 10;">
                                <span class="rounded-circle bg-white" style="width: 6px; height: 6px;"></span> Actif
                            </span>
                        @else
                            <span class="position-absolute top-0 end-0 m-3 badge bg-secondary bg-opacity-90 text-white px-2 py-1 rounded-pill small border border-secondary border-opacity-25 d-inline-flex align-items-center gap-1" style="z-index: 10;">
                                <span class="rounded-circle bg-white" style="width: 6px; height: 6px;"></span> Inactif
                            </span>
                        @endif
                        <img src="{{ $photoUrl }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s; z-index: 1;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" alt="{{ $villa->name }}">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h3 class="font-serif text-lux-dark-blue fw-medium h5 mb-1">{{ $villa->name }}</h3>
                                <p class="small text-lux-gray mb-0">{{ $villa->island->name ?? 'N/A' }} • Ref: V-{{ str_pad($villa->id, 3, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="h4 font-serif text-lux-dark-blue fw-bold mb-0">{{ number_format($villa->base_price_per_night, 0, ',', ' ') }} €<span class="small text-lux-gray fw-normal">/nuit</span></span>
                            <span class="small text-lux-gray"><i class="fa-solid fa-users text-lux-gold opacity-70 me-1"></i> {{ $villa->max_capacity }} Pers.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.villas.edit', $villa->id) }}" class="btn btn-lux-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2 text-white text-decoration-none">
                                <i class="fa-solid fa-pen-to-square text-white"></i> <span class="text-white">Modifier</span>
                            </a>
                            <form action="{{ route('admin.villas.destroy', $villa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette villa ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border shadow-sm text-center py-5">
                    <div class="d-flex flex-column align-items-center gap-3">
                        <i class="fa-solid fa-house-circle-exclamation fs-1 text-lux-gray opacity-50"></i>
                        <p class="text-lux-gray mb-0">Aucune villa enregistrée</p>
                        <a href="{{ route('admin.villas.create') }}" class="btn btn-lux-primary btn-sm text-white text-decoration-none">
                            <i class="fa-solid fa-plus me-2"></i> Créer votre première villa
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- VARIANT C: Horizontal Cards View -->
    <div id="view-horizontal" class="d-none mb-4">
        <div class="d-flex flex-column gap-3">
            @forelse($villas as $villa)
                @php
                    $primaryPhoto = $villa->photos->where('is_primary', true)->first() ?? $villa->photos->first();
                    $photoUrl = $primaryPhoto ? Storage::url($primaryPhoto->file_path) : 'https://via.placeholder.com/600x400?text=No+Photo';
                @endphp
                <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem; overflow: hidden; transition: box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                    <div class="row g-0">
                        <div class="col-12 col-md-4">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="{{ $photoUrl }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" alt="{{ $villa->name }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="card-body p-4 d-flex flex-column justify-content-between h-100">
                                <div>
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h3 class="font-serif text-lux-dark-blue fw-medium h3 mb-1">{{ $villa->name }}</h3>
                                            <p class="small text-lux-gray mb-0">{{ $villa->island->name ?? 'N/A' }} • Ref: V-{{ str_pad($villa->id, 3, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                        @if($villa->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill small border border-success border-opacity-25 d-inline-flex align-items-center gap-1">
                                                <span class="rounded-circle bg-success" style="width: 6px; height: 6px;"></span> Actif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill small border border-secondary border-opacity-25 d-inline-flex align-items-center gap-1">
                                                <span class="rounded-circle bg-secondary" style="width: 6px; height: 6px;"></span> Inactif
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-4 small text-lux-gray mb-3">
                                        <span><i class="fa-solid fa-users text-lux-gold opacity-70 me-2"></i> {{ $villa->max_capacity }} Personnes</span>
                                        <span><i class="fa-solid fa-bed text-lux-gold opacity-70 me-2"></i> {{ $villa->bedrooms }} Chambres</span>
                                        <span><i class="fa-solid fa-bath text-lux-gold opacity-70 me-2"></i> {{ $villa->bathrooms }} Salles de bain</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="h3 font-serif text-lux-dark-blue fw-bold mb-0">{{ number_format($villa->base_price_per_night, 0, ',', ' ') }} €<span class="small text-lux-gray fw-normal">/nuit</span></span>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.villas.edit', $villa->id) }}" class="btn btn-lux-primary btn-sm d-flex align-items-center gap-2 text-white text-decoration-none">
                                            <i class="fa-solid fa-pen-to-square text-white"></i> <span class="text-white">Modifier</span>
                                        </a>
                                        <form action="{{ route('admin.villas.destroy', $villa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette villa ? Cette action est irréversible.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border shadow-sm text-center py-5">
                    <div class="d-flex flex-column align-items-center gap-3">
                        <i class="fa-solid fa-house-circle-exclamation fs-1 text-lux-gray opacity-50"></i>
                        <p class="text-lux-gray mb-0">Aucune villa enregistrée</p>
                        <a href="{{ route('admin.villas.create') }}" class="btn btn-lux-primary btn-sm text-white text-decoration-none">
                            <i class="fa-solid fa-plus me-2"></i> Créer votre première villa
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showVillaView(viewType) {
        const tableView = document.getElementById('view-table');
        const gridView = document.getElementById('view-grid');
        const horizontalView = document.getElementById('view-horizontal');
        const btnTable = document.getElementById('btn-view-table');
        const btnGrid = document.getElementById('btn-view-grid');
        const btnHorizontal = document.getElementById('btn-view-horizontal');
        
        // Hide all views
        tableView.classList.add('d-none');
        gridView.classList.add('d-none');
        horizontalView.classList.add('d-none');
        
        // Remove active class and reset styles from all buttons
        [btnTable, btnGrid, btnHorizontal].forEach(btn => {
            btn.classList.remove('active', 'text-lux-dark-blue');
            btn.classList.add('text-lux-gray');
            btn.style.setProperty('border-bottom', '2px solid transparent', 'important');
            btn.style.setProperty('color', 'var(--lux-gray)', 'important');
        });
        
        // Show selected view and activate only the clicked button
        if (viewType === 'table') {
            tableView.classList.remove('d-none');
            btnTable.classList.add('active', 'text-lux-dark-blue');
            btnTable.classList.remove('text-lux-gray');
            btnTable.style.setProperty('border-bottom', '2px solid var(--lux-gold)', 'important');
            btnTable.style.setProperty('color', 'var(--lux-dark-blue)', 'important');
        } else if (viewType === 'grid') {
            gridView.classList.remove('d-none');
            btnGrid.classList.add('active', 'text-lux-dark-blue');
            btnGrid.classList.remove('text-lux-gray');
            btnGrid.style.setProperty('border-bottom', '2px solid var(--lux-gold)', 'important');
            btnGrid.style.setProperty('color', 'var(--lux-dark-blue)', 'important');
        } else if (viewType === 'horizontal') {
            horizontalView.classList.remove('d-none');
            btnHorizontal.classList.add('active', 'text-lux-dark-blue');
            btnHorizontal.classList.remove('text-lux-gray');
            btnHorizontal.style.setProperty('border-bottom', '2px solid var(--lux-gold)', 'important');
            btnHorizontal.style.setProperty('color', 'var(--lux-dark-blue)', 'important');
        }
    }

    // Fonctionnalité de recherche
    (function() {
        function initSearch() {
            const searchInput = document.getElementById('villa-search-input');
            if (!searchInput) {
                setTimeout(initSearch, 100);
                return;
            }

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                // Fonction pour filtrer les éléments (pour grille et cartes horizontales)
                function filterElements(container, searchTerm) {
                    if (!container) return;
                    
                    const items = container.querySelectorAll('.col-12.col-md-6.col-lg-4, .col-12.col-md-6');
                    let visibleCount = 0;
                    
                    items.forEach(item => {
                        if (item.classList.contains('no-results-message')) {
                            return;
                        }
                        
                        const text = item.textContent.toLowerCase();
                        const matches = searchTerm === '' || text.includes(searchTerm);
                        
                        if (matches) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    let emptyMessage = container.querySelector('.no-results-message');
                    if (searchTerm !== '' && visibleCount === 0) {
                        if (!emptyMessage) {
                            emptyMessage = document.createElement('div');
                            emptyMessage.className = 'col-12 text-center py-5 no-results-message';
                            emptyMessage.innerHTML = `
                                <i class="fa-solid fa-search text-lux-gray mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-lux-gray mb-0">Aucune villa trouvée pour "${searchTerm}"</p>
                            `;
                            container.appendChild(emptyMessage);
                        }
                    } else if (emptyMessage) {
                        emptyMessage.remove();
                    }
                }
                
                // Filtrer dans toutes les vues
                const tableView = document.getElementById('view-table');
                const gridView = document.getElementById('view-grid');
                const horizontalView = document.getElementById('view-horizontal');
                
                if (tableView) {
                    const tbody = tableView.querySelector('tbody');
                    if (tbody) {
                        const rows = tbody.querySelectorAll('tr');
                        let visibleCount = 0;
                        rows.forEach(row => {
                            if (row.querySelector('th')) return; // Ignorer l'en-tête
                            const text = row.textContent.toLowerCase();
                            const matches = searchTerm === '' || text.includes(searchTerm);
                            if (matches) {
                                row.style.display = '';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        });
                        
                        let emptyMsg = tbody.querySelector('.no-results-message');
                        if (searchTerm !== '' && visibleCount === 0) {
                            if (!emptyMsg) {
                                emptyMsg = document.createElement('tr');
                                emptyMsg.className = 'no-results-message';
                                emptyMsg.innerHTML = `
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fa-solid fa-search text-lux-gray mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="text-lux-gray mb-0">Aucune villa trouvée pour "${searchTerm}"</p>
                                    </td>
                                `;
                                tbody.appendChild(emptyMsg);
                            }
                        } else if (emptyMsg) {
                            emptyMsg.remove();
                        }
                    }
                }
                
                if (gridView) {
                    filterElements(gridView, searchTerm);
                }
                
                if (horizontalView) {
                    filterElements(horizontalView, searchTerm);
                }
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSearch);
        } else {
            initSearch();
        }
    })();
</script>
@endpush

