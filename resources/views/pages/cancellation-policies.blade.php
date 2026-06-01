@extends('layouts.app')

@section('title', 'Politiques d\'Annulation | LUXÎLES')

@section('content')

<!-- Hero Section -->
<section class="position-relative" style="padding-top: 8rem; padding-bottom: 4rem; background: linear-gradient(135deg, var(--lux-dark-blue) 0%, rgba(10, 26, 47, 0.95) 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center text-white">
                <h1 class="display-4 font-serif mb-3" style="font-family: 'Playfair Display', serif;">Politiques d'Annulation</h1>
                <p class="lead mb-0" style="color: rgba(255, 255, 255, 0.9);">Transparence et flexibilité pour votre sérénité</p>
            </div>
        </div>
    </div>
</section>

<!-- Policies Section -->
<section class="py-5" style="background-color: var(--lux-beige);">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <p class="text-lux-greyBlue">
                    Chez LUXÎLES, nous comprenons que vos plans peuvent changer. C'est pourquoi nous proposons plusieurs options d'annulation adaptées à vos besoins. Chaque villa peut avoir une politique spécifique, indiquée lors de la réservation.
                </p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($policies as $policy)
            <div class="col-md-6 col-lg-4">
                <div class="bg-white rounded shadow-sm border h-100 p-4 position-relative {{ $policy->is_default ? 'border-lux-gold' : '' }}" style="border-color: {{ $policy->is_default ? 'rgba(203, 174, 130, 0.3)' : 'rgba(138, 150, 166, 0.1)' }} !important; {{ $policy->is_default ? 'border-width: 2px;' : '' }} transition: all 0.3s;">
                    @if($policy->is_default)
                    <div class="position-absolute top-0 start-50 translate-middle">
                        <span class="badge bg-lux-gold text-white px-3 py-1 small fw-medium text-uppercase" style="letter-spacing: 0.05em;">Recommandée</span>
                    </div>
                    @endif

                    <div class="text-center mb-4" style="margin-top: {{ $policy->is_default ? '1rem' : '0' }};">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background-color: rgba(203, 174, 130, 0.1);">
                            <i class="{{ $policy->icon ?? 'fa-regular fa-handshake' }} fs-3 {{ $policy->is_default ? 'text-lux-gold' : 'text-lux-dark-blue' }}"></i>
                        </div>
                        <h3 class="h4 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">{{ $policy->name }}</h3>
                        @if($policy->description)
                        <p class="small text-lux-greyBlue mb-0">{{ $policy->description }}</p>
                        @endif>
                    </div>

                    <div class="mb-4">
                        <h4 class="small text-uppercase fw-medium text-lux-greyBlue mb-3" style="font-size: 0.7rem; letter-spacing: 0.05em;">Conditions de remboursement</h4>
                        <ul class="list-unstyled mb-0">
                            @foreach($policy->formatted_rules as $rule)
                            <li class="d-flex align-items-start mb-2">
                                <i class="fa-solid fa-check text-lux-gold me-2 mt-1" style="font-size: 0.875rem;"></i>
                                <span class="small text-lux-dark-blue">{{ $rule['label'] }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    @if($policy->is_default)
                    <div class="text-center pt-3 border-top" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <p class="small text-lux-gold mb-0 fw-medium">
                            <i class="fa-solid fa-star me-1"></i>
                            Politique appliquée par défaut
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fa-regular fa-handshake fs-1 text-lux-greyBlue mb-3" style="opacity: 0.3;"></i>
                    <p class="text-lux-greyBlue mb-0">Aucune politique d'annulation disponible pour le moment.</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Additional Information -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-8">
                <div class="bg-white rounded shadow-sm border p-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <h3 class="h5 font-serif text-lux-dark-blue mb-3 d-flex align-items-center" style="font-family: 'Playfair Display', serif;">
                        <i class="fa-solid fa-circle-info text-lux-gold me-2"></i>
                        Informations importantes
                    </h3>
                    <ul class="small text-lux-greyBlue mb-3" style="line-height: 1.8;">
                        <li>La politique d'annulation applicable est indiquée lors de la réservation et confirmée par email.</li>
                        <li>Les remboursements sont effectués dans un délai de 7 à 14 jours ouvrés sur le moyen de paiement utilisé.</li>
                        <li>En cas de circonstances exceptionnelles (catastrophe naturelle, urgence médicale), nous étudions chaque situation au cas par cas.</li>
                        <li>Les frais de service ne sont pas remboursables, sauf en cas d'annulation par LUXÎLES.</li>
                    </ul>
                    <div class="text-center pt-3 border-top" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <p class="small text-lux-greyBlue mb-2">Des questions sur nos politiques d'annulation ?</p>
                        <a href="{{ route('contact.index', ['subject' => 'Question sur les politiques d\'annulation']) }}" class="btn btn-outline-dark btn-sm px-4 rounded" style="border-color: var(--lux-dark-blue); color: var(--lux-dark-blue); transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-dark-blue)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-dark-blue)'">
                            <i class="fa-regular fa-envelope me-2"></i>Contactez-nous
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
