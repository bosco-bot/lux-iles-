@extends('layouts.admin')

@section('title', 'Gestion des Clients | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Clients</span>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Gestion des Clients
            </h1>
            <p class="small text-lux-greyBlue mb-0">Liste de tous les clients de la plateforme</p>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            <a href="{{ route('admin.clients.create') }}" class="btn text-white d-flex align-items-center gap-2" style="background-color: var(--lux-dark-blue); text-decoration: none;">
                <i class="fa-solid fa-user-plus"></i>
                <span>Nouveau client</span>
            </a>
            <div class="position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-greyBlue); z-index: 10;"></i>
                <form method="GET" action="{{ route('admin.clients') }}" class="d-inline">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un client..." class="form-control ps-5" style="width: 300px; border-color: rgba(0,0,0,0.1);" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <section class="bg-white rounded shadow-sm p-4 mb-4 border" style="border-color: rgba(0,0,0,0.05) !important;">
        <form method="GET" action="{{ route('admin.clients') }}" id="filters-form">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</label>
                    <div class="position-relative">
                        <i class="fa-solid fa-filter position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                        <select name="status" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;" onchange="document.getElementById('filters-form').submit()">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                            <option value="invitation" {{ request('status') == 'invitation' ? 'selected' : '' }}>Invitation envoyée</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Réservations</label>
                    <div class="position-relative">
                        <i class="fa-solid fa-calendar-check position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                        <select name="reservations_filter" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;" onchange="document.getElementById('filters-form').submit()">
                            <option value="">Tous les clients</option>
                            <option value="with_reservations" {{ request('reservations_filter') == 'with_reservations' ? 'selected' : '' }}>Avec réservations</option>
                            <option value="without_reservations" {{ request('reservations_filter') == 'without_reservations' ? 'selected' : '' }}>Sans réservations</option>
                            <option value="vip" {{ request('reservations_filter') == 'vip' ? 'selected' : '' }}>VIP (3+ réservations)</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Tri</label>
                    <div class="position-relative">
                        <i class="fa-solid fa-sort position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                        <select name="sort_by" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;" onchange="document.getElementById('filters-form').submit()">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
                            <option value="first_name" {{ request('sort_by') == 'first_name' ? 'selected' : '' }}>Nom</option>
                            <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <!-- Clients Table -->
    <section class="bg-white rounded shadow-sm border" style="border-color: rgba(0,0,0,0.05) !important;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="border-bottom" style="border-color: rgba(0,0,0,0.1) !important; background-color: rgba(248, 248, 246, 0.5);">
                        <th class="py-3 ps-4 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Client</th>
                        <th class="py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Contact</th>
                        <th class="py-3 small text-uppercase fw-medium text-lux-greyBlue text-center" style="font-size: 0.7rem; letter-spacing: 0.05em;">Réservations</th>
                        <th class="py-3 small text-uppercase fw-medium text-lux-greyBlue text-center" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</th>
                        <th class="py-3 small text-uppercase fw-medium text-lux-greyBlue text-end pe-4" style="font-size: 0.7rem; letter-spacing: 0.05em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr class="border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
                            <td class="py-3 ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden position-relative" style="width: 40px; height: 40px; background-color: var(--lux-gold); display: flex; align-items-center; justify-content: center; color: white; font-weight: 600; flex-shrink: 0;">
                                        @if($client->photo_url)
                                            <img src="{{ asset('storage/' . $client->photo_url) }}" alt="{{ $client->first_name }} {{ $client->last_name }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <span>{{ strtoupper(substr($client->first_name ?? '', 0, 1) . substr($client->last_name ?? '', 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-lux-dark-blue fw-medium mb-0">{{ $client->first_name }} {{ $client->last_name }}</p>
                                        <p class="small text-lux-greyBlue mb-0">
                                            <i class="fa-regular fa-calendar me-1"></i>
                                            Inscrit le {{ $client->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="small">
                                    <p class="text-lux-dark-blue mb-1">
                                        <i class="fa-regular fa-envelope me-2 text-lux-greyBlue"></i>{{ $client->email }}
                                    </p>
                                    @if($client->phone)
                                        <p class="text-lux-greyBlue mb-0">
                                            <i class="fa-solid fa-phone me-2"></i>{{ $client->phone }}
                                        </p>
                                    @else
                                        <p class="text-lux-greyBlue mb-0">
                                            <i class="fa-solid fa-phone me-2"></i>Non renseigné
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 text-center">
                                <div>
                                    <span class="badge px-3 py-2 fw-medium" style="background-color: var(--lux-gold); color: var(--lux-dark-blue);">
                                        <i class="fa-regular fa-calendar-check me-1"></i>{{ $client->reservations_count }}
                                    </span>
                                    @if($client->upcoming_reservations_count > 0)
                                        <p class="small text-lux-greyBlue mb-0 mt-1">
                                            <i class="fa-solid fa-clock me-1"></i>{{ $client->upcoming_reservations_count }} à venir
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 text-center">
                                @if(!$client->is_active)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 small fw-medium">Inactif</span>
                                @elseif($client->must_set_password)
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 small fw-medium">Invitation envoyée</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 small fw-medium">Actif</span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-sm bg-lux-gold text-white p-2 border-0" style="text-decoration: none;" title="Voir détails">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.clients.toggle-status', $client->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $client->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} p-2" title="{{ $client->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="fa-solid fa-{{ $client->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-lux-greyBlue">
                                <i class="fa-solid fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                <p class="mb-0">Aucun client trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clients->hasPages())
            <div class="p-4 border-top">
                <div class="d-flex justify-content-center">
                    {{ $clients->links() }}
                </div>
            </div>
        @endif
    </section>
@endsection

