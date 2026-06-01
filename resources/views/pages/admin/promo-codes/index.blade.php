@extends('layouts.admin')

@section('title', 'Codes promotionnels | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Codes promo</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Codes promotionnels
            </h1>
            <p class="text-muted small mb-0">Gestion manuelle des réductions — §3.2 CDC</p>
        </div>
        <a href="{{ route('admin.promo-codes.create') }}" class="btn btn-lux-primary btn-sm mt-3 mt-md-0 text-white text-decoration-none">
            <i class="fa-solid fa-plus me-1"></i> Nouveau code
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card border shadow-sm" style="border-radius: 0.75rem;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="px-4 py-3 small text-uppercase">Code</th>
                        <th class="px-4 py-3 small text-uppercase">Type / Valeur</th>
                        <th class="px-4 py-3 small text-uppercase">Validité</th>
                        <th class="px-4 py-3 small text-uppercase text-center">Utilisations</th>
                        <th class="px-4 py-3 small text-uppercase text-center">Statut</th>
                        <th class="px-4 py-3 small text-uppercase text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promoCodes as $promo)
                        <tr>
                            <td class="px-4 py-3 fw-semibold text-lux-dark-blue">{{ $promo->code }}</td>
                            <td class="px-4 py-3">
                                {{ $promo->type_label }}
                                @if($promo->type === 'percent')
                                    — {{ number_format($promo->value, 0) }} %
                                @else
                                    — {{ number_format($promo->value, 2, ',', ' ') }} €
                                @endif
                            </td>
                            <td class="px-4 py-3 small text-lux-greyBlue">
                                @if($promo->valid_from || $promo->valid_until)
                                    {{ $promo->valid_from?->format('d/m/Y') ?? '—' }}
                                    →
                                    {{ $promo->valid_until?->format('d/m/Y') ?? '—' }}
                                @else
                                    Sans limite de dates
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $promo->uses_count }}
                                @if($promo->max_uses)
                                    / {{ $promo->max_uses }}
                                @else
                                    / ∞
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($promo->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.promo-codes.edit', $promo) }}" class="btn btn-sm btn-outline-secondary" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.promo-codes.toggle', $promo) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="{{ $promo->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="fa-solid fa-{{ $promo->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    @if($promo->uses_count === 0)
                                        <form action="{{ route('admin.promo-codes.destroy', $promo) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce code ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-lux-greyBlue">Aucun code promotionnel</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($promoCodes->hasPages())
            <div class="p-3">{{ $promoCodes->links() }}</div>
        @endif
    </div>
@endsection
