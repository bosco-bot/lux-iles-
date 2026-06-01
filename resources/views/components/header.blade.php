<!-- Header Navigation -->
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

            <!-- Actions -->
            <div class="d-none d-md-flex align-items-center gap-4">
                @auth
                    <!-- Avatar utilisateur connecté -->
                    <div class="user-avatar-dropdown position-relative">
                        <button class="user-avatar-btn border-0 bg-transparent p-0" type="button" id="userAvatarBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-circle position-relative overflow-hidden">
                                @if(auth()->user()->photo_url)
                                    <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Avatar" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover;">
                                @else
                                    <span class="user-initials">{{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                                @endif
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userAvatarBtn" style="min-width: 200px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="small fw-medium text-dark">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                                <div class="small text-muted">{{ auth()->user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item" href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('espace-client.index') }}"><i class="far fa-user me-2"></i>Mon espace</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" id="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                    </div>
                @else
                    <!-- Bouton connexion si non connecté -->
                    <a href="{{ route('login') }}" class="text-white-50 text-decoration-none small" style="transition: color 0.3s;"><i class="far fa-user me-2"></i>Connexion</a>
                @endauth
                <a href="{{ route('villas.index') }}" class="btn btn-lux-primary px-5 py-2 rounded text-decoration-none small fw-medium" style="transition: all 0.3s; box-shadow: 0 10px 15px -3px rgba(203, 174, 130, 0.2);">
                    Réserver
                </a>
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
                    @auth
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <div class="user-avatar-circle position-relative overflow-hidden" style="width: 32px; height: 32px;">
                                @if(auth()->user()->photo_url)
                                    <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Avatar" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover;">
                                @else
                                    <span class="user-initials" style="font-size: 0.75rem;">{{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-white fw-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                                <a href="#" class="small text-white-50 text-decoration-none" id="logout-btn-mobile">Déconnexion</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-white-50 text-decoration-none small mt-2"><i class="far fa-user me-2"></i>Connexion</a>
                    @endauth
                    <a href="{{ route('villas.index') }}" class="btn btn-lux-primary px-4 py-2 rounded text-decoration-none small fw-medium mt-2">
                        Réserver
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>


