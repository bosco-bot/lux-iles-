<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Client | LUXÎLES - Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body.dashboard-body {
            height: 100vh;
            overflow: hidden;
            background-color: var(--lux-beige);
        }
        .dashboard-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .dashboard-header {
            height: 80px;
            background-color: var(--lux-dark-blue);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }
        .dashboard-main {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        .dashboard-sidebar {
            width: 256px;
            background-color: var(--lux-white);
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            flex-shrink: 0;
            display: none;
        }
        @media (min-width: 992px) {
            .dashboard-sidebar {
                display: flex;
            }
        }
        .dashboard-content {
            flex: 1;
            overflow-y: auto;
            background-color: var(--lux-beige);
            padding: 1rem;
            position: relative;
        }
        @media (min-width: 768px) {
            .dashboard-content {
                padding: 2rem;
            }
        }
        .dashboard-content:has(.messages-page-container) {
            padding: 0;
            overflow: hidden;
            background-color: #F8F8F6;
        }
        .sidebar-menu-section {
            padding: 1.5rem;
        }
        .sidebar-menu-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--lux-gray);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }
        .sidebar-menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(26, 26, 26, 0.7);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0 0.5rem 0.5rem 0;
            border-left: 3px solid transparent;
            transition: all 0.2s;
            margin-bottom: 0.25rem;
        }
        .sidebar-menu-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
            color: var(--lux-dark-blue);
        }
        .sidebar-menu-item.text-danger:hover {
            background-color: rgba(0, 0, 0, 0.02);
            color: rgba(220, 53, 69, 0.8);
        }
        .sidebar-menu-item.active {
            background: linear-gradient(90deg, rgba(203, 174, 130, 0.1) 0%, rgba(203, 174, 130, 0) 100%);
            border-left-color: var(--lux-gold);
            color: var(--lux-gold);
        }
        .sidebar-menu-item i {
            width: 20px;
            text-align: center;
        }
        .badge-notification {
            margin-left: auto;
            background-color: var(--lux-gold);
            color: var(--lux-white);
            font-size: 0.625rem;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
        }
        .help-card {
            background-color: var(--lux-dark-blue);
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin: 1.5rem;
        }
        .help-card::before {
            content: '';
            position: absolute;
            top: -1rem;
            right: -1rem;
            width: 4rem;
            height: 4rem;
            background-color: rgba(203, 174, 130, 0.2);
            border-radius: 50%;
            filter: blur(1rem);
        }
        .help-card h4 {
            color: var(--lux-gold);
            font-family: 'Playfair Display', serif;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 10;
        }
        .help-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 10;
        }
        .help-card button {
            position: relative;
            z-index: 10;
        }
        .reservation-card {
            background-color: var(--lux-white);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .reservation-image {
            height: 256px;
            object-fit: cover;
            width: 100%;
        }
        @media (min-width: 768px) {
            .reservation-image {
                height: auto;
            }
        }
        .widget-card {
            background-color: var(--lux-white);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            transition: box-shadow 0.2s;
        }
        .widget-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .widget-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .widget-card:hover .widget-icon {
            background-color: var(--lux-dark-blue);
            color: var(--lux-white);
        }
        .progress-bar-custom {
            height: 8px;
            background-color: var(--lux-gold);
        }
        .inspiration-card {
            height: 192px;
            border-radius: 0.75rem;
            overflow: hidden;
            position: relative;
            cursor: pointer;
        }
        .inspiration-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s;
        }
        .inspiration-card:hover img {
            transform: scale(1.1);
        }
        .inspiration-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10, 26, 47, 0.6), transparent);
            transition: background 0.3s;
        }
        .inspiration-card:hover::after {
            background: linear-gradient(to top, rgba(10, 26, 47, 0.5), transparent);
        }
        .inspiration-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            z-index: 10;
        }
    </style>
    @stack('styles')
</head>
<body class="dashboard-body font-sans" style="font-family: 'Montserrat', sans-serif;">
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header d-flex align-items-center justify-content-between px-4 px-md-5">
            <!-- Logo + Menu Mobile Button -->
            <div class="d-flex align-items-center gap-3">
                <!-- Bouton menu mobile (visible uniquement sur mobile) -->
                <button class="btn btn-link text-white p-0 d-lg-none border-0 bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" style="text-decoration: none;">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                <!-- Logo -->
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('Social_Media_Profil_Beige_Color.png') }}" alt="LUX Îles" style="height: 40px; width: auto; object-fit: contain;">
                    <span class="text-xl font-serif text-white d-none d-sm-inline" style="font-family: 'Playfair Display', serif; letter-spacing: 0.1em; line-height: 1;">
                        LUX<span class="text-lux-gold italic">Îles</span>
                    </span>
                </a>
            </div>

            <!-- Right Actions -->
            <div class="d-flex align-items-center gap-4">
                <button class="btn btn-link text-white-50 p-0 position-relative border-0 bg-transparent" style="text-decoration: none;">
                    <i class="far fa-bell fs-5"></i>
                    <span class="position-absolute top-0 end-0 translate-middle bg-lux-gold rounded-circle" style="width: 8px; height: 8px;"></span>
                </button>
                <div class="d-flex align-items-center gap-3 ps-4 border-start border-white border-opacity-10">
                    <div class="text-end d-none d-md-block" style="padding-top: 0.875rem;">
                        <p class="text-white small fw-medium mb-0">{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</p>
                        <p class="text-lux-gray small mb-0" style="font-size: 0.75rem;">Membre Gold</p>
                    </div>
                    <button class="user-avatar-btn border-0 bg-transparent p-0" type="button" id="userAvatarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-circle position-relative overflow-hidden" style="width: 40px; height: 40px;">
                            @if(auth()->user()->photo_url)
                                <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Avatar" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover;">
                            @else
                                <span class="user-initials">{{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                            @endif
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userAvatarDropdown">
                        <li class="px-3 py-2 border-bottom">
                            <div class="small fw-medium text-dark">{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</div>
                            <div class="small text-muted">{{ auth()->user()->email ?? '' }}</div>
                        </li>
                        <li><a class="dropdown-item" href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('espace-client.index') }}"><i class="far fa-user me-2"></i>Mon espace</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" id="logout-btn-dashboard"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Layout -->
        <div class="dashboard-main">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar flex-column py-4">
                <div class="sidebar-menu-section">
                    <p class="sidebar-menu-title">Menu Principal</p>
                    <nav>
                        <a href="{{ route('espace-client.index') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Tableau de bord</span>
                        </a>
                        <a href="{{ route('espace-client.reservations') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.reservations') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Mes Réservations</span>
                        </a>
                        <a href="{{ route('espace-client.messages') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.messages') ? 'active' : '' }}">
                            <i class="fa-regular fa-envelope"></i>
                            <span>Messagerie</span>
                            @php
                                $unreadCount = \App\Models\Message::where('recipient_id', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="badge-notification">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('espace-client.favoris') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.favoris') ? 'active' : '' }}">
                            <i class="fa-regular fa-heart"></i>
                            <span>Favoris</span>
                        </a>
                        <a href="{{ route('espace-client.privilege-club') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.privilege-club*') ? 'active' : '' }}">
                            <i class="fa-solid fa-crown"></i>
                            <span>Privilege Club</span>
                            @php $clubNotifCount = auth()->user() ? \App\Models\PrivilegeClubNotification::where('user_id', auth()->id())->whereNull('read_at')->count() : 0; @endphp
                            @if($clubNotifCount > 0)
                                <span class="badge bg-lux-gold text-dark ms-auto">{{ $clubNotifCount }}</span>
                            @endif
                        </a>
                    </nav>
                </div>

                <div class="sidebar-menu-section">
                    <p class="sidebar-menu-title">Gestion</p>
                    <nav>
                        <a href="{{ route('espace-client.documents') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.documents') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>Documents</span>
                        </a>
                        <a href="{{ route('espace-client.payments') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.payments') ? 'active' : '' }}">
                            <i class="fa-regular fa-credit-card"></i>
                            <span>Paiements</span>
                        </a>
                        <a href="{{ route('espace-client.profile') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.profile') ? 'active' : '' }}">
                            <i class="fa-regular fa-user"></i>
                            <span>Mon Profil</span>
                        </a>
                        <a href="#" class="sidebar-menu-item text-danger" id="logout-btn-sidebar">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </a>
                    </nav>
                </div>

                <div class="mt-auto">
                    <div class="help-card">
                        <h4>Besoin d'aide ?</h4>
                        <p>Votre concierge dédié est disponible 24/7.</p>
                        <a href="{{ route('contact.index') }}" class="btn btn-lux-primary w-100 btn-sm text-decoration-none">Contacter</a>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Menu Mobile (Offcanvas) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 280px; background-color: var(--lux-white);">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center gap-2" id="mobileSidebarLabel">
                <img src="{{ asset('Social_Media_Profil_Bleu.png') }}" alt="LUX Îles" style="height: 32px; width: auto; object-fit: contain;">
                <span class="font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif; letter-spacing: 0.1em; line-height: 1;">
                    LUX<span class="text-lux-gold italic">Îles</span>
                </span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <!-- Menu Principal -->
            <div class="sidebar-menu-section">
                <p class="sidebar-menu-title">Menu Principal</p>
                <nav>
                    <a href="{{ route('espace-client.index') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.index') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="{{ route('espace-client.reservations') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.reservations') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Mes Réservations</span>
                    </a>
                    <a href="{{ route('espace-client.messages') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.messages') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-regular fa-envelope"></i>
                        <span>Messagerie</span>
                        @php
                            $unreadCount = \App\Models\Message::where('recipient_id', auth()->id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge-notification">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('espace-client.favoris') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.favoris') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-regular fa-heart"></i>
                        <span>Favoris</span>
                    </a>
                    <a href="{{ route('espace-client.privilege-club') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.privilege-club*') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-solid fa-crown"></i>
                        <span>Privilege Club</span>
                    </a>
                </nav>
            </div>

            <!-- Gestion -->
            <div class="sidebar-menu-section">
                <p class="sidebar-menu-title">Gestion</p>
                <nav>
                    <a href="{{ route('espace-client.documents') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.documents') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Documents</span>
                    </a>
                    <a href="{{ route('espace-client.payments') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.payments') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-regular fa-credit-card"></i>
                        <span>Paiements</span>
                    </a>
                    <a href="{{ route('espace-client.profile') }}" class="sidebar-menu-item {{ request()->routeIs('espace-client.profile') ? 'active' : '' }}" data-bs-dismiss="offcanvas">
                        <i class="fa-regular fa-user"></i>
                        <span>Mon Profil</span>
                    </a>
                    <a href="#" class="sidebar-menu-item text-danger" id="logout-btn-sidebar-mobile" data-bs-dismiss="offcanvas">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </nav>
            </div>

            <!-- Help Card -->
            <div class="mt-auto">
                <div class="help-card" style="margin: 1.5rem;">
                    <h4>Besoin d'aide ?</h4>
                    <p>Votre concierge dédié est disponible 24/7.</p>
                    <a href="{{ route('contact.index') }}" class="btn btn-lux-primary w-100 btn-sm text-decoration-none" data-bs-dismiss="offcanvas">Contacter</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script>
        // Gestion de la déconnexion dans le dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn-dashboard');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (window.AuthAPI && window.AuthAPI.handleLogout) {
                        window.AuthAPI.handleLogout();
                    }
                });
            }
            
            // Gestion de la déconnexion dans le sidebar desktop
            const logoutBtnSidebar = document.getElementById('logout-btn-sidebar');
            if (logoutBtnSidebar) {
                logoutBtnSidebar.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (window.AuthAPI && window.AuthAPI.handleLogout) {
                        window.AuthAPI.handleLogout();
                    }
                });
            }
            
            // Gestion de la déconnexion dans le sidebar mobile
            const logoutBtnSidebarMobile = document.getElementById('logout-btn-sidebar-mobile');
            if (logoutBtnSidebarMobile) {
                logoutBtnSidebarMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (window.AuthAPI && window.AuthAPI.handleLogout) {
                        window.AuthAPI.handleLogout();
                    }
                });
            }
        });
    </script>
    @stack('scripts')
    @vite(['resources/js/app.js'])
</body>
</html>

