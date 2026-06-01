@extends('layouts.admin')

@section('title', 'Modérer un avis | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.villa-reviews.index') }}" class="text-white-50 text-decoration-none">Avis</a>
    <span class="text-white mx-2">/</span>
    <span class="text-white">Détail</span>
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.villa-reviews.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Retour</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border shadow-sm mb-4" style="border-radius: 0.75rem;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 text-lux-dark-blue">Avis de {{ $villaReview->user->first_name }}</h2>
                    <span class="badge {{ $villaReview->status === 'approved' ? 'bg-success' : ($villaReview->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                        {{ $villaReview->statusLabel() }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        {{ $villaReview->villa->name }} — séjour {{ $villaReview->reservation->check_in_date->format('d/m/Y') }} → {{ $villaReview->reservation->check_out_date->format('d/m/Y') }}
                    </p>
                    <div class="mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $villaReview->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                        @endfor
                        <span class="ms-2 text-lux-greyBlue small">{{ $villaReview->rating }}/5</span>
                    </div>
                    <p class="mb-0" style="white-space: pre-line;">{{ $villaReview->comment }}</p>
                    <p class="small text-muted mt-3 mb-0">
                        Déposé le {{ ($villaReview->submitted_at ?? $villaReview->created_at)->format('d/m/Y à H:i') }}
                        @if($villaReview->published_at)
                            · Publié le {{ $villaReview->published_at->format('d/m/Y à H:i') }}
                        @endif
                    </p>
                </div>
            </div>

            @if($villaReview->isApproved())
                <div class="card border shadow-sm" style="border-radius: 0.75rem;">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 text-lux-dark-blue">Réponse publique LUXÎLES</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.villa-reviews.response', $villaReview) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <textarea name="admin_response" class="form-control mb-3" rows="4" placeholder="Réponse visible sous l'avis sur la fiche villa…">{{ old('admin_response', $villaReview->admin_response) }}</textarea>
                            <button type="submit" class="btn btn-sm btn-lux-primary text-white">Enregistrer la réponse</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if($villaReview->isPending())
                <div class="card border shadow-sm" style="border-radius: 0.75rem;">
                    <div class="card-body">
                        <h3 class="h6 text-lux-dark-blue mb-3">Modération</h3>
                        <form action="{{ route('admin.villa-reviews.approve', $villaReview) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Publier l'avis</button>
                        </form>
                        <form action="{{ route('admin.villa-reviews.reject', $villaReview) }}" method="POST" onsubmit="return confirm('Refuser cet avis ? Il ne sera pas visible sur le site.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">Refuser</button>
                        </form>
                    </div>
                </div>
            @elseif($villaReview->moderated_at)
                <div class="card border shadow-sm small text-muted" style="border-radius: 0.75rem;">
                    <div class="card-body">
                        Modéré le {{ $villaReview->moderated_at->format('d/m/Y à H:i') }}
                        @if($villaReview->published_at)
                            <br>Publié le {{ $villaReview->published_at->format('d/m/Y') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
