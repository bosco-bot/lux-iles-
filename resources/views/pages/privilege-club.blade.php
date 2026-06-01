@extends('layouts.dashboard')

@section('title', 'LUXÎLES PRIVILEGE CLUB | Espace Client')

@section('content')
    <div class="mb-4">
        <span class="text-lux-gold text-uppercase small fw-medium d-block mb-2" style="letter-spacing: 0.2em;">Programme de fidélité</span>
        <h1 class="h2 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">LUXÎLES PRIVILEGE CLUB</h1>
        <p class="text-lux-greyBlue small mb-0">§3.1 — Avantages exclusifs réservés aux voyageurs LUXÎLES.</p>
    </div>

    @if($notifications->whereNull('read_at')->isNotEmpty())
        <div class="mb-4">
            @foreach($notifications->whereNull('read_at') as $notification)
                <div class="alert alert-light border d-flex justify-content-between align-items-start gap-3 mb-2" style="border-color: rgba(203, 174, 130, 0.4) !important;">
                    <div>
                        <i class="fa-solid fa-{{ $notification->type === 'tier_up' ? 'arrow-up text-success' : 'bell text-warning' }} me-2"></i>
                        {{ $notification->message }}
                        <span class="d-block small text-muted mt-1">{{ $notification->created_at->format('d/m/Y') }}</span>
                    </div>
                    <form action="{{ route('espace-client.privilege-club.notifications.read', $notification) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">OK</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card border shadow-sm mb-4" style="border-radius: 0.75rem; border-color: rgba(203, 174, 130, 0.3) !important;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="small text-uppercase text-lux-greyBlue mb-1">Votre statut actuel</p>
                    @if($currentTier)
                        <h2 class="h3 font-serif text-lux-dark-blue mb-2">
                            {{ $tierDefinitions[$currentTier]['emoji'] ?? '' }}
                            {{ $tierDefinitions[$currentTier]['label'] ?? strtoupper($currentTier) }}
                        </h2>
                    @else
                        <h2 class="h4 text-lux-dark-blue mb-2">Pas encore membre</h2>
                        <p class="text-lux-greyBlue small mb-0">Votre premier séjour confirmé et réalisé vous ouvrira le niveau INSIDER.</p>
                    @endif
                    @if($user->privilege_tier_manual_override)
                        <span class="badge bg-secondary bg-opacity-10 text-secondary mt-2">Statut ajusté par l'équipe LUXÎLES</span>
                    @endif
                </div>
                <div class="col-md-4 mt-3 mt-md-0 text-md-end">
                    <p class="small text-lux-greyBlue mb-1">Séjours comptabilisés (3 ans glissants)</p>
                    <p class="display-6 font-serif text-lux-gold mb-0">{{ $qualifyingStays }}</p>
                    @if(!$user->privilege_tier_manual_override && $earnedTier && $earnedTier !== $currentTier)
                        <p class="small text-muted mt-2 mb-0">Palier calculé : {{ $tierDefinitions[$earnedTier]['label'] ?? $earnedTier }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($tierDefinitions as $tierKey => $tier)
            <div class="col-md-4">
                <div class="card h-100 border shadow-sm {{ $currentTier === $tierKey ? 'border-warning' : '' }}" style="border-radius: 0.75rem; {{ $currentTier === $tierKey ? 'box-shadow: 0 0 0 2px rgba(203, 174, 130, 0.35);' : '' }}">
                    <div class="card-body p-4">
                        @if($currentTier === $tierKey)
                            <span class="badge bg-warning text-dark mb-2">Votre niveau</span>
                        @endif
                        <h3 class="h5 font-serif text-lux-dark-blue mb-1">
                            {{ $tier['emoji'] }} {{ $tier['label'] }}
                        </h3>
                        <p class="small text-lux-greyBlue mb-3">{{ $tier['condition'] }}</p>
                        <ul class="small text-lux-dark-blue ps-3 mb-3">
                            @foreach($tier['benefits'] as $benefit)
                                <li class="mb-1">{{ $benefit }}</li>
                            @endforeach
                        </ul>
                        <p class="small text-muted mb-0"><i class="fa-solid fa-calendar-check me-1"></i> {{ $tier['maintenance'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 bg-light p-4" style="border-radius: 0.75rem;">
        <h3 class="h6 text-lux-dark-blue mb-2">Règles du programme</h3>
        <ul class="small text-lux-greyBlue mb-0 ps-3">
            <li>Seuls les séjours confirmés et réalisés sont comptabilisés (les annulations sont exclues).</li>
            <li>Le calcul des paliers est effectué sur les 3 dernières années glissantes.</li>
            <li>Pour conserver votre statut, effectuez au minimum une réservation par an.</li>
            <li>En l'absence de réservation sur une année civile, une rétrogradation automatique s'applique au 1<sup>er</sup> janvier.</li>
            <li>Les codes promotionnels restent attribués manuellement par l'équipe — aucune remise automatique liée au palier.</li>
        </ul>
    </div>
@endsection
