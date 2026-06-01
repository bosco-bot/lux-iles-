@extends('layouts.admin')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Gestion des Destinations | LUXÎLES - Back-office')

@section('admin-breadcrumbs')
    <span class="text-white">Destinations</span>
@endsection

@section('content')
    <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Gestion des Destinations
            </h1>
            <p class="text-muted small mb-0">Gérez les îles et destinations de votre plateforme.</p>
        </div>
        <a href="{{ route('admin.islands.create') }}" class="btn btn-lux-primary btn-sm d-flex align-items-center gap-2 shadow-sm text-white text-decoration-none mt-3 mt-md-0">
            <i class="fa-solid fa-plus text-white"></i>
            <span class="text-white">Ajouter une destination</span>
        </a>
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

    <!-- Liste des îles -->
    <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="bg-light" style="background-color: rgba(10, 26, 47, 0.05) !important;">
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Image</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Nom</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Code</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Pays</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Description</th>
                        <th class="px-4 py-3 small fw-bold text-lux-dark-blue text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($islands as $island)
                        <tr>
                            <td class="px-4 py-3">
                                @php
                                    $imageUrl = null;
                                    if ($island->image_path) {
                                        $imageUrl = Storage::url($island->image_path);
                                    } elseif ($island->image_url) {
                                        $imageUrl = $island->image_url;
                                    }
                                @endphp
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $island->name }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 0.5rem;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light" style="width: 80px; height: 60px; border-radius: 0.5rem;">
                                        <i class="fa-solid fa-image text-lux-gray"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="fw-medium" style="color: var(--lux-dark-blue);">{{ $island->name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-lux-gold text-white">{{ $island->code }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-lux-gray">{{ $island->country ?? 'France' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-lux-gray small">{{ Str::limit($island->description ?? 'Aucune description', 50) }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.islands.edit', $island->id) }}" class="btn btn-sm btn-lux-primary text-white d-inline-flex align-items-center gap-2">
                                    <i class="fa-solid fa-pencil"></i>
                                    <span>Modifier</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center text-lux-gray">
                                <i class="fa-solid fa-island-tropical fs-3 mb-3 d-block"></i>
                                <p class="mb-0">Aucune destination disponible</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

