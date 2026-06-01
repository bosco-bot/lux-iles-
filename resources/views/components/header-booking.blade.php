<!-- Header Navigation pour la page de réservation -->
<header id="header" class="lux-header">
    <div class="lux-header-container">
        <div class="d-flex align-items-center justify-content-between lux-header-inner">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('Social_Media_Profil_Beige_Color.png') }}" alt="LUX Îles" style="height: 50px; width: auto; object-fit: contain;">
                <span class="text-2xl font-serif text-white" style="font-family: 'Playfair Display', serif; letter-spacing: 0.1em; line-height: 1;">
                    LUX<span class="text-lux-gold italic">Îles</span>
                </span>
            </a>

            <!-- Desktop Nav -->
            <nav class="d-none d-md-flex align-items-center lux-nav-desktop">
                <a href="{{ route('home') }}" class="lux-nav-link text-white-50">Accueil</a>
                <a href="{{ route('villas.index') }}" class="lux-nav-link text-white-50">Villas</a>
                <a href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('espace-client.index') }}" class="lux-nav-link text-white-50">Espace Client</a>
                <a href="{{ route('home') }}#destinations" class="lux-nav-link text-white-50">Destinations</a>
                <a href="{{ route('contact.index') }}" class="lux-nav-link text-white-50">Contact</a>
            </nav>

            <!-- Steps Indicator (Replacing standard Actions for this flow) -->
            <div class="d-none d-md-flex align-items-center text-sm fw-medium" style="gap: 1rem;">
                <div class="d-flex align-items-center text-lux-gold">
                    <span class="rounded-circle border border-lux-gold d-flex align-items-center justify-content-center me-2" style="width: 1.5rem; height: 1.5rem; font-size: 0.75rem;">1</span>
                    Détails
                </div>
                <div style="width: 2rem; height: 1px; background-color: rgba(255, 255, 255, 0.2);"></div>
                <div class="d-flex align-items-center {{ request()->routeIs('bookings.payment') ? 'text-lux-gold' : 'text-white-50' }}">
                    <span class="rounded-circle border {{ request()->routeIs('bookings.payment') ? 'border-lux-gold' : 'border-white-50' }} d-flex align-items-center justify-content-center me-2" style="width: 1.5rem; height: 1.5rem; font-size: 0.75rem;">2</span>
                    Paiement
                </div>
                <div style="width: 2rem; height: 1px; background-color: rgba(255, 255, 255, 0.2);"></div>
                <div class="d-flex align-items-center text-white-50">
                    <span class="rounded-circle border border-white-50 d-flex align-items-center justify-content-center me-2" style="width: 1.5rem; height: 1.5rem; font-size: 0.75rem;">3</span>
                    Confirmation
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button class="d-md-none text-white border-0 bg-transparent fs-5 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="collapse d-md-none" id="mobileMenu">
            <div class="py-4 border-top border-white border-opacity-10">
                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('home') }}" class="lux-nav-link text-white-50 text-decoration-none small fw-medium">Accueil</a>
                    <a href="{{ route('villas.index') }}" class="lux-nav-link text-white-50 text-decoration-none small fw-medium">Villas</a>
                    <a href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('espace-client.index') }}" class="lux-nav-link text-white-50 text-decoration-none small fw-medium">Espace Client</a>
                    <a href="{{ route('home') }}#destinations" class="lux-nav-link text-white-50 text-decoration-none small fw-medium">Destinations</a>
                    <a href="{{ route('contact.index') }}" class="lux-nav-link text-white-50 text-decoration-none small fw-medium">Contact</a>
                    
                    <!-- Mobile Steps Indicator -->
                    <div class="d-flex flex-column mt-3 pt-3 border-top border-white border-opacity-10" style="gap: 0.75rem;">
                        <div class="d-flex align-items-center text-lux-gold small">
                            <span class="rounded-circle border border-lux-gold d-flex align-items-center justify-content-center me-2" style="width: 1.5rem; height: 1.5rem; font-size: 0.75rem;">1</span>
                            Détails
                        </div>
                        <div class="d-flex align-items-center {{ request()->routeIs('bookings.payment') ? 'text-lux-gold' : 'text-white-50' }} small">
                            <span class="rounded-circle border {{ request()->routeIs('bookings.payment') ? 'border-lux-gold' : 'border-white-50' }} d-flex align-items-center justify-content-center me-2" style="width: 1.5rem; height: 1.5rem; font-size: 0.75rem;">2</span>
                            Paiement
                        </div>
                        <div class="d-flex align-items-center text-white-50 small">
                            <span class="rounded-circle border border-white-50 d-flex align-items-center justify-content-center me-2" style="width: 1.5rem; height: 1.5rem; font-size: 0.75rem;">3</span>
                            Confirmation
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

