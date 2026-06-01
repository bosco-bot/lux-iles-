@extends('layouts.admin')

@section('title', 'Avis voyageurs | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Avis voyageurs</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Avis voyageurs
            </h1>
            <p class="text-muted small mb-0">§3.4 CDC — modération, publication et réponses publiques</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.villa-reviews.index', ['status' => 'pending']) }}">
                En attente @if($counts['pending'])<span class="badge bg-warning text-dark ms-1">{{ $counts['pending'] }}</span>@endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'published' ? 'active' : '' }}" href="{{ route('admin.villa-reviews.index', ['status' => 'published']) }}">Publiés ({{ $counts['published'] }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('admin.villa-reviews.index', ['status' => 'rejected']) }}">Refusés ({{ $counts['rejected'] }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.villa-reviews.index', ['status' => 'all']) }}">Tous</a>
        </li>
    </ul>

    <div class="card border shadow-sm" style="border-radius: 0.75rem;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="px-4 py-3 small text-uppercase">Date</th>
                        <th class="px-4 py-3 small text-uppercase">Villa</th>
                        <th class="px-4 py-3 small text-uppercase">Voyageur</th>
                        <th class="px-4 py-3 small text-uppercase text-center">Note</th>
                        <th class="px-4 py-3 small text-uppercase">Statut</th>
                        <th class="px-4 py-3 small text-uppercase text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="px-4 py-3 small">{{ $review->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 fw-medium text-lux-dark-blue">{{ $review->villa->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $review->user->first_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star small {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                @endfor
                            </td>
                            <td class="px-4 py-3">
                                @if($review->status === 'published')
                                    <span class="badge bg-success bg-opacity-10 text-success">{{ $review->statusLabel() }}</span>
                                @elseif($review->status === 'rejected')
                                    <span class="badge bg-danger bg-opacity-10 text-danger">{{ $review->statusLabel() }}</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning">{{ $review->statusLabel() }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.villa-reviews.show', $review) }}" class="btn btn-sm btn-outline-secondary">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-lux-greyBlue">Aucun avis dans cette catégorie</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
            <div class="card-footer bg-white">{{ $reviews->links() }}</div>
        @endif
    </div>
@endsection
