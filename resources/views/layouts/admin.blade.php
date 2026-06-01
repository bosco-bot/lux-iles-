<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin | LUXÎLES - Administration')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%230A1A2F'/%3E%3Ctext x='50' y='70' font-family='Playfair Display' font-size='60' font-weight='bold' fill='%23CBAE82' text-anchor='middle'%3EL%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body.admin-body {
            height: 100vh;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        .admin-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-header {
            height: 80px;
            background-color: var(--lux-dark-blue);
            color: white;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .hover-lux-gold:hover {
            color: var(--lux-gold) !important;
        }
        .admin-main {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        .admin-sidebar {
            width: 280px;
            background-color: white;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            flex-shrink: 0;
            display: none;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
        }
        @media (min-width: 992px) {
            .admin-sidebar {
                display: flex;
            }
        }
        .admin-content {
            flex: 1;
            overflow-y: auto;
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .admin-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--lux-gray);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        .admin-menu-item:hover {
            background-color: rgba(10, 26, 47, 0.05);
            color: var(--lux-dark-blue);
        }
        .admin-menu-item.active {
            background-color: rgba(10, 26, 47, 0.05);
            color: var(--lux-dark-blue);
            font-weight: 500;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .admin-menu-item.active i {
            color: var(--lux-gold);
        }
        .admin-menu-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--lux-gray);
            padding: 16px 20px 8px;
            font-weight: 600;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
    </style>
    @stack('styles')
</head>
<body class="admin-body font-sans" style="font-family: 'Montserrat', sans-serif;">
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header d-flex align-items-center justify-content-between px-4 px-md-5">
            <!-- Logo + Mobile Menu Toggle -->
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-white p-0 d-lg-none border-0 bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar" aria-controls="adminMobileSidebar" style="text-decoration: none;">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('Social_Media_Profil_Beige_Color.png') }}" alt="LUX Îles" style="height: 40px; width: auto; object-fit: contain;">
                    <span class="text-xl font-serif text-white d-none d-sm-inline" style="font-family: 'Playfair Display', serif; letter-spacing: 0.1em; line-height: 1;">
                        LUX<span class="text-lux-gold italic">Îles</span> <span class="small opacity-75 ms-2">Admin</span>
                    </span>
                </a>
            </div>

            <!-- Breadcrumbs / Page Title -->
            <nav class="d-none d-md-flex align-items-center small fw-medium text-white-50">
                <a href="{{ route('admin.dashboard') }}" class="text-white-50 text-decoration-none hover-lux-gold">Dashboard</a>
                @hasSection('admin-breadcrumbs')
                    <span class="mx-2">/</span>
                    @yield('admin-breadcrumbs')
                @endif
            </nav>

            <!-- User Actions -->
            <div class="d-flex align-items-center gap-4">
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-white-50 p-0 position-relative border-0 bg-transparent" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                        <i class="fa-regular fa-bell fs-5"></i>
                        <span id="notifications-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; display: none;">
                            <span id="notifications-count">0</span>
                            <span class="visually-hidden">notifications non lues</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" style="min-width: 350px; max-width: 400px; max-height: 500px; overflow-y: auto;">
                        <li>
                            <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <button id="mark-all-read-btn" class="btn btn-link p-0 small text-decoration-none" style="font-size: 0.75rem;">Tout marquer comme lu</button>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li id="notifications-list">
                            <div class="px-3 py-4 text-center text-muted">
                                <i class="fa-regular fa-bell-slash mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="small mb-0">Chargement...</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="vr d-none d-md-block" style="height: 30px; opacity: 0.3;"></div>
                <div class="d-flex align-items-center gap-3 d-none d-md-flex">
                    <div class="text-end">
                        <p class="text-white small mb-0 fw-medium">{{ auth()->user()->first_name ?? 'Admin' }} {{ auth()->user()->last_name ?? '' }}</p>
                        <p class="text-lux-gold small mb-0" style="font-size: 0.75rem;">Administrateur</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-white p-0 border-0 bg-transparent" type="button" id="adminUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                            <div class="user-avatar-circle position-relative overflow-hidden" style="width: 32px; height: 32px;">
                                @if(auth()->user()->photo_url)
                                    <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Admin Avatar" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover;">
                                @else
                                    <span class="user-initials" style="font-size: 0.75rem;">{{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                                @endif
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdown">
                            <li><h6 class="dropdown-header">{{ auth()->user()->first_name ?? 'Admin' }} {{ auth()->user()->last_name ?? '' }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('espace-client.profile') }}"><i class="far fa-user me-2"></i>Mon Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" id="admin-logout-link"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                        <form id="admin-logout-form" action="{{ route('api.auth.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
                <div class="dropdown d-md-none">
                    <button class="btn btn-link text-white p-0 border-0 bg-transparent" type="button" id="adminUserDropdownMobile" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                        <div class="user-avatar-circle position-relative overflow-hidden" style="width: 32px; height: 32px;">
                            @if(auth()->user()->photo_url)
                                <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" alt="Admin Avatar" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover;">
                            @else
                                <span class="user-initials" style="font-size: 0.75rem;">{{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                            @endif
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdownMobile">
                        <li><h6 class="dropdown-header">{{ auth()->user()->first_name ?? 'Admin' }} {{ auth()->user()->last_name ?? '' }}</h6></li>
                        <li><a class="dropdown-item" href="{{ route('espace-client.profile') }}"><i class="far fa-user me-2"></i>Mon Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" id="admin-logout-link-mobile"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                    </ul>
                    <form id="admin-logout-form-mobile" action="{{ route('api.auth.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Layout -->
        <div class="admin-main">
            <!-- Sidebar Desktop -->
            <aside class="admin-sidebar flex-column p-4">
                <div class="admin-menu-title mb-3">Principal</div>
                <nav class="d-flex flex-column gap-1">
                    <a href="{{ route('admin.dashboard') }}" class="admin-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.traffic') }}" class="admin-menu-item {{ request()->routeIs('admin.traffic*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Statistiques trafic</span>
                    </a>
                    <a href="{{ route('admin.villas') }}" class="admin-menu-item {{ request()->routeIs('admin.villas*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-house-chimney"></i>
                        <span>Villas</span>
                    </a>
                    <a href="{{ route('admin.islands') }}" class="admin-menu-item {{ request()->routeIs('admin.islands*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Destinations</span>
                    </a>
                    <a href="{{ route('admin.equipments.index') }}" class="admin-menu-item {{ request()->routeIs('admin.equipments*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Équipements</span>
                    </a>
                    <a href="{{ route('admin.reservations') }}" class="admin-menu-item {{ request()->routeIs('admin.reservations*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>Réservations</span>
                    </a>
                    <a href="{{ route('admin.calendar') }}" class="admin-menu-item {{ request()->routeIs('admin.calendar*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-calendar-days"></i>
                        <span>Calendrier</span>
                    </a>
                    <a href="{{ route('admin.clients') }}" class="admin-menu-item {{ request()->routeIs('admin.clients*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-user"></i>
                        <span>Clients</span>
                    </a>
                    <a href="{{ route('admin.messages') }}" class="admin-menu-item {{ request()->routeIs('admin.messages*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-envelope"></i>
                        <span>Messagerie</span>
                        @php
                            $unreadCount = \App\Models\Message::where('recipient_id', auth()->id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-lux-gold text-lux-blue rounded-pill ms-auto">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.payments') }}" class="admin-menu-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-credit-card"></i>
                        <span>Paiements</span>
                    </a>
                    <a href="{{ route('admin.villa-reviews.index') }}" class="admin-menu-item {{ request()->routeIs('admin.villa-reviews*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-star"></i>
                        <span>Avis voyageurs</span>
                        @php $pendingReviewsCount = \App\Models\VillaReview::pending()->count(); @endphp
                        @if($pendingReviewsCount > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pendingReviewsCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.promo-codes.index') }}" class="admin-menu-item {{ request()->routeIs('admin.promo-codes*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-tag"></i>
                        <span>Codes promo</span>
                    </a>
                </nav>

                <div class="admin-menu-title mb-3 mt-4">Configuration</div>
                <nav class="d-flex flex-column gap-1">
                    <a href="{{ route('admin.synchronization') }}" class="admin-menu-item {{ request()->routeIs('admin.synchronization*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-rotate"></i>
                        <span>Synchronisation</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="admin-menu-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-gear"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="#" class="admin-menu-item text-danger" id="admin-logout-link-sidebar" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Déconnexion</span>
                    </a>
                </nav>
            </aside>

            <!-- Content -->
            <main class="admin-content">
                @yield('content')
            </main>
        </div>

        <!-- Mobile Sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="adminMobileSidebar" aria-labelledby="adminMobileSidebarLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="adminMobileSidebarLabel">Menu Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-4">
                <div class="admin-menu-title mb-3">Principal</div>
                <nav class="d-flex flex-column gap-1">
                    <a href="{{ route('admin.dashboard') }}" class="admin-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.traffic') }}" class="admin-menu-item {{ request()->routeIs('admin.traffic*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Statistiques trafic</span>
                    </a>
                    <a href="{{ route('admin.villas') }}" class="admin-menu-item {{ request()->routeIs('admin.villas*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-house-chimney"></i>
                        <span>Villas</span>
                    </a>
                    <a href="{{ route('admin.islands') }}" class="admin-menu-item {{ request()->routeIs('admin.islands*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Destinations</span>
                    </a>
                    <a href="{{ route('admin.equipments.index') }}" class="admin-menu-item {{ request()->routeIs('admin.equipments*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Équipements</span>
                    </a>
                    <a href="{{ route('admin.reservations') }}" class="admin-menu-item {{ request()->routeIs('admin.reservations*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>Réservations</span>
                    </a>
                    <a href="{{ route('admin.calendar') }}" class="admin-menu-item {{ request()->routeIs('admin.calendar*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-calendar-days"></i>
                        <span>Calendrier</span>
                    </a>
                    <a href="{{ route('admin.clients') }}" class="admin-menu-item {{ request()->routeIs('admin.clients*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-user"></i>
                        <span>Clients</span>
                    </a>
                    <a href="{{ route('admin.messages') }}" class="admin-menu-item {{ request()->routeIs('admin.messages*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-envelope"></i>
                        <span>Messagerie</span>
                        @php
                            $unreadCountMobile = \App\Models\Message::where('recipient_id', auth()->id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        @if($unreadCountMobile > 0)
                            <span class="badge bg-lux-gold text-lux-blue rounded-pill ms-auto">{{ $unreadCountMobile }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.payments') }}" class="admin-menu-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-regular fa-credit-card"></i>
                        <span>Paiements</span>
                    </a>
                    <a href="{{ route('admin.villa-reviews.index') }}" class="admin-menu-item {{ request()->routeIs('admin.villa-reviews*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-star"></i>
                        <span>Avis voyageurs</span>
                        @php $pendingReviewsCountMobile = \App\Models\VillaReview::pending()->count(); @endphp
                        @if($pendingReviewsCountMobile > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pendingReviewsCountMobile }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.promo-codes.index') }}" class="admin-menu-item {{ request()->routeIs('admin.promo-codes*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-tag"></i>
                        <span>Codes promo</span>
                    </a>
                </nav>
                <div class="admin-menu-title mb-3 mt-4">Configuration</div>
                <nav class="d-flex flex-column gap-1">
                    <a href="{{ route('admin.synchronization') }}" class="admin-menu-item {{ request()->routeIs('admin.synchronization*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-rotate"></i>
                        <span>Synchronisation</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="admin-menu-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-gear"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="#" class="admin-menu-item text-danger" id="admin-logout-link-mobile-sidebar" data-bs-dismiss="offcanvas" style="border-left: none; border-radius: 0.5rem;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Déconnexion</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutUrl = '{{ route("api.auth.logout") }}';
            const csrfToken = '{{ csrf_token() }}';
            const homeUrl = '{{ route("home") }}';

            function handleLogout(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('_token', csrfToken);
                
                fetch(logoutUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    window.location.href = homeUrl;
                })
                .catch(error => {
                    console.error('Erreur lors de la déconnexion:', error);
                    window.location.href = homeUrl;
                });
            }

            // Gestion de la déconnexion pour tous les liens
            const logoutLinks = [
                'admin-logout-link',
                'admin-logout-link-mobile',
                'admin-logout-link-sidebar',
                'admin-logout-link-mobile-sidebar'
            ];

            logoutLinks.forEach(linkId => {
                const link = document.getElementById(linkId);
                if (link) {
                    link.addEventListener('click', handleLogout);
                }
            });
        });

        // Gestion des notifications
        (function() {
            const notificationsUrl = '{{ route("admin.notifications.index") }}';
            const unreadCountUrl = '{{ route("admin.notifications.unread-count") }}';
            const markAsReadUrl = '{{ route("admin.notifications.mark-as-read", ":id") }}';
            const markAllAsReadUrl = '{{ route("admin.notifications.mark-all-as-read") }}';
            const csrfToken = '{{ csrf_token() }}';

            let notificationsListEl = document.getElementById('notifications-list');
            let notificationsBadgeEl = document.getElementById('notifications-badge');
            let notificationsCountEl = document.getElementById('notifications-count');
            let markAllReadBtn = document.getElementById('mark-all-read-btn');

            function loadNotifications() {
                fetch(notificationsUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateNotificationsList(data.notifications);
                            updateUnreadCount(data.unread_count);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des notifications:', error);
                    });
            }

            function updateUnreadCount(count) {
                if (count > 0) {
                    notificationsCountEl.textContent = count > 99 ? '99+' : count;
                    notificationsBadgeEl.style.display = 'inline-block';
                } else {
                    notificationsBadgeEl.style.display = 'none';
                }
            }

            function updateNotificationsList(notifications) {
                if (notifications.length === 0) {
                    notificationsListEl.innerHTML = `
                        <div class="px-3 py-4 text-center text-muted">
                            <i class="fa-regular fa-bell-slash mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="small mb-0">Aucune notification</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                notifications.forEach(function(notif) {
                    const iconColor = notif.color || 'primary';
                    html += `
                        <li>
                            <a class="dropdown-item notification-item ${notif.read_at ? '' : 'bg-light'}" href="${notif.url}" data-notification-id="${notif.id}" style="cursor: pointer;">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid ${notif.icon} text-${iconColor}" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium small mb-1">${notif.title}</div>
                                        <div class="text-muted small mb-1" style="font-size: 0.75rem;">${notif.message}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">${notif.created_at}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                    `;
                });
                notificationsListEl.innerHTML = html;

                // Ajouter les event listeners pour marquer comme lu
                document.querySelectorAll('.notification-item').forEach(function(item) {
                    item.addEventListener('click', function(e) {
                        const notificationId = this.getAttribute('data-notification-id');
                        if (notificationId) {
                            // Ne pas empêcher la navigation, marquer comme lu en arrière-plan
                            markAsRead(notificationId);
                        }
                    });
                });
            }

            function markAsRead(notificationId) {
                const url = markAsReadUrl.replace(':id', notificationId);
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateUnreadCount(data.unread_count);
                        // Recharger les notifications si le dropdown est ouvert
                        const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('notificationsDropdown'));
                        if (dropdown && dropdown._isShown()) {
                            loadNotifications();
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du marquage comme lu:', error);
                });
            }

            function markAllAsRead() {
                fetch(markAllAsReadUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateUnreadCount(0);
                        loadNotifications();
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du marquage de toutes les notifications:', error);
                });
            }

            // Charger les notifications au chargement de la page
            loadNotifications();

            // Recharger les notifications toutes les 30 secondes
            setInterval(loadNotifications, 30000);

            // Recharger quand le dropdown est ouvert
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            if (notificationsDropdown) {
                notificationsDropdown.addEventListener('show.bs.dropdown', function() {
                    loadNotifications();
                });
            }

            // Marquer toutes comme lues
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markAllAsRead();
                });
            }

            // Écouter les notifications en temps réel avec Laravel Echo
            if (typeof window.Echo !== 'undefined' && window.Echo) {
                const userId = {{ auth()->id() }};
                
                // Écouter les notifications
                window.Echo.private('user.' + userId)
                    .notification((notification) => {
                        // Recharger les notifications quand une nouvelle arrive
                        loadNotifications();
                        
                        // Optionnel: Afficher une notification toast
                        if (notification.type && notification.title) {
                            showNotificationToast(notification);
                        }
                    });

                // Écouter les événements de notifications personnalisés
                window.Echo.private('user.' + userId)
                    .listen('.notification.created', (data) => {
                        if (data.notification) {
                            loadNotifications();
                            showNotificationToast(data.notification);
                        }
                    });

                // Écouter les messages en temps réel
                window.Echo.private('user.' + userId)
                    .listen('.message.sent', (data) => {
                        if (data.message) {
                            // Recharger les messages si on est sur la page de messages
                            if (window.location.pathname.includes('/messages') || window.location.pathname.includes('/espace-client/messages')) {
                                // Déclencher un événement personnalisé pour recharger les messages
                                window.dispatchEvent(new CustomEvent('newMessageReceived', { detail: data.message }));
                            }
                        }
                    });
            }

            // Fonction helper pour afficher une notification toast
            function showNotificationToast(notification) {
                // Créer une notification toast temporaire
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-0 end-0 m-3 p-3 bg-white rounded shadow-lg';
                toast.style.zIndex = '9999';
                toast.style.minWidth = '300px';
                toast.style.borderLeft = '4px solid var(--lux-gold)';
                toast.innerHTML = `
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa-solid ${notification.icon || 'fa-bell'} text-${notification.color || 'primary'}" style="font-size: 1.5rem;"></i>
                        <div class="flex-grow-1">
                            <div class="fw-medium mb-1">${notification.title || 'Notification'}</div>
                            <div class="text-muted small mb-2">${notification.message || ''}</div>
                            <a href="${notification.url || '#'}" class="btn btn-sm btn-outline-primary">Voir</a>
                        </div>
                        <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                
                // Supprimer automatiquement après 5 secondes
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 5000);
            }
        })();
    </script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>

