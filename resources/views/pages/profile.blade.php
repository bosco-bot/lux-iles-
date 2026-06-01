@extends('layouts.dashboard')

@section('title', 'Mon Profil | LUXÎLES - Dashboard')

@section('content')
    <!-- Profile Header / Hero Minimal -->
    <section id="profile-hero" class="position-relative" style="height: 250px; background-color: var(--lux-dark-blue); overflow: hidden; margin-top: -1rem; margin-left: -1rem; margin-right: -1rem; margin-bottom: 2rem;">
        <style>
            @media (min-width: 768px) {
                #profile-hero {
                    margin-top: -2rem !important;
                    margin-left: -2rem !important;
                    margin-right: -2rem !important;
                }
            }
        </style>
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-30">
            <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2070&auto=format&fit=crop" class="w-100 h-100" style="object-fit: cover;" alt="Luxury Background">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(10,26,47,0.7) 0%, rgba(10,26,47,0.4) 50%, rgba(10,26,47,0.8) 100%);"></div>
        </div>
        <div class="position-relative z-10 container h-100 d-flex flex-column flex-md-row align-items-end align-items-md-center pb-4">
            <div class="position-relative mb-3 mb-md-0 me-md-4">
                <div class="rounded-circle border border-4 border-white overflow-hidden shadow-lg position-relative" style="width: 96px; height: 96px;" id="profile-avatar-container">
                    @if(auth()->user()->photo_url)
                        <img id="profile-avatar-image" src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Profile" class="w-100 h-100" style="object-fit: cover;">
                        <div class="w-100 h-100 bg-lux-gold d-flex align-items-center justify-content-center d-none position-absolute top-0 start-0" style="font-size: 2.5rem; color: var(--lux-white);" id="profile-avatar-initials">
                            <span class="user-initials">{{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                        </div>
                    @else
                        <div class="w-100 h-100 bg-lux-gold d-flex align-items-center justify-content-center position-absolute top-0 start-0" style="font-size: 2.5rem; color: var(--lux-white);" id="profile-avatar-initials">
                            <span class="user-initials">{{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                        </div>
                        <img id="profile-avatar-image" src="" alt="Profile" class="w-100 h-100 d-none position-absolute top-0 start-0" style="object-fit: cover;">
                    @endif
                </div>
                <input type="file" id="profile-photo-input" accept="image/*" class="d-none">
                <button type="button" class="position-absolute bg-lux-gold text-white p-2 rounded-circle border-0 shadow-lg" style="bottom: 4px; right: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background-color 0.3s; cursor: pointer;" id="profile-photo-btn" onmouseover="this.style.backgroundColor='#A48C64'" onmouseout="this.style.backgroundColor='var(--lux-gold)'">
                    <i class="fa-solid fa-camera" style="font-size: 0.75rem;"></i>
                </button>
            </div>
            <div class="mb-2">
                <h1 class="h2 font-serif text-white mb-1" style="font-family: 'Playfair Display', serif;">{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</h1>
                <p class="small d-flex align-items-center gap-2 mb-0" style="color: #8A96A6;">
                    <i class="fa-solid fa-star text-lux-gold" style="font-size: 0.75rem;"></i> Membre Prestige depuis {{ auth()->user()->created_at ? auth()->user()->created_at->format('Y') : '2024' }}
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Content Column -->
            <div class="col-12">
                <!-- Variant A: Formulaire Simple (Standard Layout) -->
                <section id="variant-a-simple-form" class="card border border-gray-100 mb-4" style="border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div class="card-header border-bottom p-4 bg-light bg-opacity-50 d-flex justify-content-between align-items-center" style="border-color: rgba(138, 150, 166, 0.1);">
                        <h2 class="h4 font-serif text-lux-dark-blue mb-0">Informations Personnelles</h2>
                        <span class="badge px-2 py-1 small fw-bold text-uppercase" style="font-size: 0.625rem; letter-spacing: 0.1em; background-color: rgba(203, 174, 130, 0.1); color: var(--lux-gold); border: 1px solid var(--lux-gold);">Variante A</span>
                    </div>
                    <div class="card-body p-4">
                        <form class="space-y-4" id="profile-form">
                            <div class="row g-3 mb-4">
                                <!-- First Name -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium text-uppercase d-block mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em; font-size: 0.75rem;">Prénom</label>
                                    <div class="position-relative input-focus-ring rounded border" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                        <i class="fa-regular fa-user position-absolute top-50 start-0 translate-middle-y ms-3" style="color: var(--lux-gray);"></i>
                                        <input type="text" id="first_name" value="{{ auth()->user()->first_name ?? '' }}" class="form-control border-0 bg-transparent ps-5 py-3" style="outline: none; color: var(--lux-dark-blue); font-weight: 500;">
                                    </div>
                                </div>
                                
                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium text-uppercase d-block mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em; font-size: 0.75rem;">Nom</label>
                                    <div class="position-relative input-focus-ring rounded border" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                        <i class="fa-regular fa-user position-absolute top-50 start-0 translate-middle-y ms-3" style="color: var(--lux-gray);"></i>
                                        <input type="text" id="last_name" value="{{ auth()->user()->last_name ?? '' }}" class="form-control border-0 bg-transparent ps-5 py-3" style="outline: none; color: var(--lux-dark-blue); font-weight: 500;">
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label small fw-medium text-uppercase d-block mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em; font-size: 0.75rem;">Email</label>
                                <div class="position-relative input-focus-ring rounded border d-flex align-items-center" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                    <div class="flex-grow-1 position-relative">
                                        <i class="fa-regular fa-envelope position-absolute top-50 start-0 translate-middle-y ms-3" style="color: var(--lux-gray);"></i>
                                        <input type="email" id="email" value="{{ auth()->user()->email ?? '' }}" class="form-control border-0 bg-transparent ps-5 py-3" style="outline: none; color: var(--lux-dark-blue); font-weight: 500;">
                                    </div>
                                    <button type="button" class="btn btn-link text-lux-gold text-decoration-none fw-medium px-3" style="font-size: 0.875rem; border: none; background: none;" onmouseover="this.style.color='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'">Modifier</button>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="mb-4">
                                <label class="form-label small fw-medium text-uppercase d-block mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em; font-size: 0.75rem;">Téléphone</label>
                                <div class="position-relative input-focus-ring rounded border d-flex align-items-center" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                    <div class="flex-grow-1 position-relative">
                                        <i class="fa-solid fa-phone position-absolute top-50 start-0 translate-middle-y ms-3" style="color: var(--lux-gray);"></i>
                                        <input type="tel" id="phone" value="{{ auth()->user()->phone ?? '' }}" class="form-control border-0 bg-transparent ps-5 py-3" style="outline: none; color: var(--lux-dark-blue); font-weight: 500;">
                                    </div>
                                    <button type="button" class="btn btn-link text-lux-gold text-decoration-none fw-medium px-3" style="font-size: 0.875rem; border: none; background: none;" onmouseover="this.style.color='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'">Modifier</button>
                                </div>
                            </div>

                            <!-- Preferences Textarea -->
                            <div class="mb-4">
                                <label class="form-label small fw-medium text-uppercase d-block mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em; font-size: 0.75rem;">Préférences de séjour</label>
                                <div class="position-relative input-focus-ring rounded border" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                    <textarea rows="3" class="form-control border-0 bg-transparent p-3" style="outline: none; color: var(--lux-dark-blue); resize: none; font-size: 0.875rem;" placeholder="Allergies, préférences alimentaires, type d'oreiller..."></textarea>
                                </div>
                            </div>

                            <div class="pt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-lux-primary px-5 py-3 shadow-lg" style="box-shadow: 0 10px 15px -3px rgba(203, 174, 130, 0.2); transition: all 0.3s; transform: translateY(0);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Variant B: Card Segments (Grouped Information) -->
                <section id="variant-b-card-segments" class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h4 font-serif text-lux-dark-blue mb-0">Coordonnées</h2>
                        <span class="badge px-2 py-1 small fw-bold text-uppercase" style="font-size: 0.625rem; letter-spacing: 0.1em; background-color: rgba(203, 174, 130, 0.1); color: var(--lux-gold); border: 1px solid var(--lux-gold);">Variante B</span>
                    </div>

                    <div class="row g-4">
                        <!-- Identity Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border border-gray-100 shadow-sm" style="border-radius: 0.5rem; transition: box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h3 class="h5 font-serif text-lux-dark-blue mb-0">Identité</h3>
                                        <i class="fa-regular fa-id-card text-lux-gold" style="font-size: 1.25rem;"></i>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="small text-lux-gray d-block mb-1">Nom complet</label>
                                            <p class="fw-medium text-lux-dark-blue mb-0">{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</p>
                                        </div>
                                        <div>
                                            <label class="small text-lux-gray d-block mb-1">Date de naissance</label>
                                            <p class="fw-medium text-lux-dark-blue mb-0">{{ auth()->user()->birth_date ? \Carbon\Carbon::parse(auth()->user()->birth_date)->format('d F Y') : 'Non renseignée' }}</p>
                                        </div>
                                    </div>
                                    <button class="btn btn-link text-lux-gold text-decoration-none p-0 mt-4 small fw-medium" style="border: none; background: none;" onmouseover="this.style.color='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'">
                                        Éditer <i class="fa-solid fa-pen ms-1" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border border-gray-100 shadow-sm" style="border-radius: 0.5rem; transition: box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h3 class="h5 font-serif text-lux-dark-blue mb-0">Contact</h3>
                                        <i class="fa-regular fa-address-book text-lux-gold" style="font-size: 1.25rem;"></i>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <label class="small text-lux-gray d-block mb-1">Email</label>
                                                <p class="fw-medium text-lux-dark-blue mb-0">{{ auth()->user()->email ?? '' }}</p>
                                            </div>
                                            <button class="btn btn-link text-lux-gray text-decoration-none p-0" style="opacity: 0; transition: all 0.3s; border: none; background: none;" onmouseover="this.style.opacity='1'; this.style.color='var(--lux-gold)'" onmouseout="this.style.opacity='0'">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <label class="small text-lux-gray d-block mb-1">Téléphone</label>
                                                <p class="fw-medium text-lux-dark-blue mb-0">{{ auth()->user()->phone ?? 'Non renseigné' }}</p>
                                            </div>
                                            <button class="btn btn-link text-lux-gray text-decoration-none p-0" style="opacity: 0; transition: all 0.3s; border: none; background: none;" onmouseover="this.style.opacity='1'; this.style.color='var(--lux-gold)'" onmouseout="this.style.opacity='0'">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-lux-gold w-100 mt-4 py-2 border-lux-gold text-lux-gold small fw-medium" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-gold)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-gold)'">
                                        Gérer mes préférences de contact
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Variant C: Layout Colonne (Detailed Vertical Layout) -->
                <section id="variant-c-column-layout" class="card border border-gray-100 overflow-hidden">
                    <div class="card-header text-white p-4 d-flex justify-content-between align-items-center" style="background-color: #0A1A2F;">
                        <div>
                            <h2 class="h5 font-serif mb-1">Profil Complet</h2>
                            <p class="small text-lux-gray mb-0">Gérez toutes vos informations en un seul endroit</p>
                        </div>
                        <span class="badge px-2 py-1 small fw-bold text-uppercase" style="font-size: 0.625rem; letter-spacing: 0.1em; background-color: rgba(203, 174, 130, 0.1); color: var(--lux-gold); border: 1px solid var(--lux-gold);">Variante C</span>
                    </div>
                    
                    <div class="divide-y divide-gray-100 bg-white">
                        <!-- Row 1 -->
                        <div class="p-4 d-md-flex align-items-start justify-content-between" style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(248, 248, 246, 0.5)'" onmouseout="this.style.backgroundColor='white'">
                            <div class="mb-3 mb-md-0" style="width: 33.333%;">
                                <h4 class="text-lux-dark-blue fw-medium mb-1">Informations de base</h4>
                                <p class="small text-lux-gray mb-0">Votre nom et photo visible par nos hôtes.</p>
                            </div>
                            <div class="flex-grow-1 ps-md-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="user-avatar-circle position-relative overflow-hidden" style="width: 48px; height: 48px;" id="profile-avatar-container-bottom">
                                        @if(auth()->user()->photo_url)
                                            <img id="profile-avatar-image-bottom" src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Profile" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover;">
                                            <div id="profile-avatar-initials-bottom" class="w-100 h-100 d-none position-absolute top-0 start-0 d-flex align-items-center justify-content-center">
                                                <span class="user-initials">{{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                                            </div>
                                        @else
                                            <div id="profile-avatar-initials-bottom" class="w-100 h-100 position-absolute top-0 start-0 d-flex align-items-center justify-content-center">
                                                <span class="user-initials">{{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                                            </div>
                                            <img id="profile-avatar-image-bottom" src="" alt="Profile" class="w-100 h-100 d-none position-absolute top-0 start-0" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-link text-lux-gold text-decoration-none p-0 small fw-medium" style="border: none; background: none; cursor: pointer;" id="profile-photo-btn-bottom" onmouseover="this.style.color='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'">Changer la photo</button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" id="first_name_c" value="{{ auth()->user()->first_name ?? '' }}" class="form-control border-0 border-bottom rounded-0 px-0 py-2 bg-transparent" style="border-color: rgba(138, 150, 166, 0.3) !important; outline: none; color: var(--lux-dark-blue); transition: border-color 0.3s;" placeholder="Prénom" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.3)'">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="last_name_c" value="{{ auth()->user()->last_name ?? '' }}" class="form-control border-0 border-bottom rounded-0 px-0 py-2 bg-transparent" style="border-color: rgba(138, 150, 166, 0.3) !important; outline: none; color: var(--lux-dark-blue); transition: border-color 0.3s;" placeholder="Nom" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.3)'">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="p-4 d-md-flex align-items-start justify-content-between" style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(248, 248, 246, 0.5)'" onmouseout="this.style.backgroundColor='white'">
                            <div class="mb-3 mb-md-0" style="width: 33.333%;">
                                <h4 class="text-lux-dark-blue fw-medium mb-1">Coordonnées</h4>
                                <p class="small text-lux-gray mb-0">Pour vous contacter au sujet de vos réservations.</p>
                            </div>
                            <div class="flex-grow-1 ps-md-4">
                                <div class="mb-3">
                                    <label class="small text-lux-gray d-block mb-1">Email</label>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2" style="border-color: rgba(138, 150, 166, 0.3) !important;">
                                        <span class="text-lux-dark-blue">{{ auth()->user()->email ?? '' }}</span>
                                        <button class="btn btn-link text-lux-gray text-decoration-none p-0 small text-uppercase fw-bold" style="border: none; background: none; transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Modifier</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="small text-lux-gray d-block mb-1">Téléphone</label>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2" style="border-color: rgba(138, 150, 166, 0.3) !important;">
                                        <span class="text-lux-dark-blue">{{ auth()->user()->phone ?? 'Non renseigné' }}</span>
                                        <button class="btn btn-link text-lux-gray text-decoration-none p-0 small text-uppercase fw-bold" style="border: none; background: none; transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Modifier</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3 - Action -->
                        <div class="p-4" style="background-color: rgba(248, 248, 246, 0.3);">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <button type="button" class="btn btn-link text-lux-gray text-decoration-none px-3 py-2 small fw-medium" style="border: none; background: none; transition: color 0.3s;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="this.style.color='var(--lux-gray)'">Annuler</button>
                                <button type="button" class="btn text-white px-4 py-2 small fw-medium shadow-md" id="save-profile-btn" style="background-color: var(--lux-dark-blue); transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">Enregistrer les modifications</button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .input-focus-ring:focus-within {
        box-shadow: 0 0 0 2px rgba(203, 174, 130, 0.2);
        border-color: var(--lux-gold) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoBtn = document.getElementById('profile-photo-btn');
        const photoBtnBottom = document.getElementById('profile-photo-btn-bottom');
        const photoInput = document.getElementById('profile-photo-input');
        const avatarContainer = document.getElementById('profile-avatar-container');
        const avatarImage = document.getElementById('profile-avatar-image');
        const avatarInitials = document.getElementById('profile-avatar-initials');
        const avatarContainerBottom = document.getElementById('profile-avatar-container-bottom');
        const avatarImageBottom = document.getElementById('profile-avatar-image-bottom');
        const avatarInitialsBottom = document.getElementById('profile-avatar-initials-bottom');
        

        // Fonction pour mettre à jour les deux avatars
        function updateAvatars(imageSrc) {
            // Avatar du haut (hero)
            if (avatarImage && avatarInitials) {
                avatarImage.src = imageSrc;
                avatarImage.classList.remove('d-none');
                avatarInitials.classList.add('d-none');
            }
            
            // Avatar du bas (section Profil Complet)
            if (avatarImageBottom && avatarInitialsBottom) {
                avatarImageBottom.src = imageSrc;
                avatarImageBottom.classList.remove('d-none');
                avatarInitialsBottom.classList.add('d-none');
            }
            
            // Mettre à jour les avatars dans le header (si présent sur la page)
            updateHeaderAvatars(imageSrc);
        }
        
        // Fonction pour mettre à jour les avatars dans le header
        function updateHeaderAvatars(imageSrc) {
            // Avatar desktop
            const headerAvatarDesktop = document.querySelector('.user-avatar-dropdown .user-avatar-circle');
            if (headerAvatarDesktop) {
                let headerImg = headerAvatarDesktop.querySelector('img');
                if (!headerImg) {
                    headerImg = document.createElement('img');
                    headerImg.className = 'w-100 h-100 position-absolute top-0 start-0';
                    headerImg.style.objectFit = 'cover';
                    headerImg.alt = 'Avatar';
                    headerAvatarDesktop.appendChild(headerImg);
                }
                headerImg.src = imageSrc;
                const headerInitials = headerAvatarDesktop.querySelector('.user-initials');
                if (headerInitials) {
                    headerInitials.classList.add('d-none');
                }
            }
            
            // Avatar mobile
            const headerAvatarMobile = document.querySelector('#mobileMenu .user-avatar-circle');
            if (headerAvatarMobile) {
                let mobileImg = headerAvatarMobile.querySelector('img');
                if (!mobileImg) {
                    mobileImg = document.createElement('img');
                    mobileImg.className = 'w-100 h-100 position-absolute top-0 start-0';
                    mobileImg.style.objectFit = 'cover';
                    mobileImg.alt = 'Avatar';
                    headerAvatarMobile.appendChild(mobileImg);
                }
                mobileImg.src = imageSrc;
                const mobileInitials = headerAvatarMobile.querySelector('.user-initials');
                if (mobileInitials) {
                    mobileInitials.classList.add('d-none');
                }
            }
        }

        // Variable pour stocker le fichier photo sélectionné
        let selectedPhotoFile = null;

        // Fonction pour gérer la sélection de fichier
        function handleFileSelect(file) {
            if (!file) {
                return;
            }

            // Vérifier que c'est une image
            if (!file.type.startsWith('image/')) {
                alert('Veuillez sélectionner une image valide.');
                selectedPhotoFile = null;
                return;
            }

            // Vérifier la taille (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('L\'image est trop volumineuse. Taille maximale : 5MB.');
                selectedPhotoFile = null;
                return;
            }

            // Stocker le fichier pour l'upload ultérieur
            selectedPhotoFile = file;

            // Afficher un aperçu dans les deux avatars
            const reader = new FileReader();
            reader.onload = function(e) {
                updateAvatars(e.target.result);
            };
            reader.readAsDataURL(file);
        }

        // Ouvrir le sélecteur de fichier au clic sur le bouton du haut
        if (photoBtn && photoInput) {
            photoBtn.addEventListener('click', function(e) {
                e.preventDefault();
                photoInput.click();
            });
        }

        // Ouvrir le sélecteur de fichier au clic sur le bouton du bas
        if (photoBtnBottom && photoInput) {
            photoBtnBottom.addEventListener('click', function(e) {
                e.preventDefault();
                photoInput.click();
            });
        }

        // Gérer la sélection de fichier
        if (photoInput) {
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    handleFileSelect(file);
                } else {
                    selectedPhotoFile = null;
                }
            });
        }

        // Gérer le clic sur "Enregistrer les modifications"
        const saveProfileBtn = document.getElementById('save-profile-btn');
        if (saveProfileBtn) {
            saveProfileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Vérifier aussi directement depuis l'input au cas où selectedPhotoFile serait perdu
                if (!selectedPhotoFile && photoInput && photoInput.files && photoInput.files.length > 0) {
                    selectedPhotoFile = photoInput.files[0];
                }
                
                const btn = this;
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';

                // Préparer les données du formulaire
                const formData = new FormData();
                
                // Ajouter les données des champs de la variante C
                const firstNameC = document.getElementById('first_name_c');
                const lastNameC = document.getElementById('last_name_c');
                
                if (firstNameC && firstNameC.value) {
                    formData.append('first_name', firstNameC.value);
                }
                if (lastNameC && lastNameC.value) {
                    formData.append('last_name', lastNameC.value);
                }

                // Ajouter la photo - vérifier d'abord selectedPhotoFile, puis l'input directement
                let fileToUpload = selectedPhotoFile;
                
                // Si selectedPhotoFile est null, essayer de récupérer depuis l'input
                if (!fileToUpload && photoInput && photoInput.files && photoInput.files.length > 0) {
                    fileToUpload = photoInput.files[0];
                }
                
                if (fileToUpload) {
                    formData.append('photo', fileToUpload, fileToUpload.name);
                }

                // Envoyer les données via Axios
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const headers = {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                    'Accept': 'application/json'
                };
                
                axios.post('/api/profile/update', formData, {
                    headers: headers,
                    timeout: 30000
                })
                .then(response => {
                    if (response.data.success) {
                        // Afficher un message de succès
                        alert('Profil mis à jour avec succès !');
                        
                        // Si une photo a été uploadée, mettre à jour les avatars avec l'URL retournée
                        if (response.data.user && response.data.user.photo_url) {
                            updateAvatars(response.data.user.photo_url);
                        }
                        
                        // Réinitialiser le fichier sélectionné
                        selectedPhotoFile = null;
                        if (photoInput) {
                            photoInput.value = '';
                        }
                        
                        // Recharger la page pour afficher les changements depuis la base de données
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert('Erreur: ' + (response.data.message || 'Une erreur est survenue'));
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la mise à jour du profil:', error);
                    let errorMessage = 'Erreur lors de la mise à jour du profil.';
                    
                    if (error.response && error.response.data) {
                        if (error.response.data.message) {
                            errorMessage = error.response.data.message;
                        } else if (error.response.data.errors) {
                            const errors = Object.values(error.response.data.errors).flat();
                            errorMessage = errors.join('\n');
                        } else if (error.response.data.error) {
                            errorMessage = error.response.data.error;
                        }
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                    
                    alert('Erreur: ' + errorMessage);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                });
            });
        }
    });
</script>
@endpush
