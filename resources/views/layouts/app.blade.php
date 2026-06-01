<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LUXÎLES - Location de Villas de Luxe')</title>
    {{-- Favicon / logo dans l’onglet du navigateur --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('styles')
</head>
<body class="bg-lux-beige font-sans" style="font-family: 'Montserrat', sans-serif;">
    @if(request()->routeIs('bookings.create') || request()->routeIs('bookings.payment'))
        @include('components.header-booking')
    @else
        @include('components.header')
    @endif

    {{-- Bannière de consentement cookies --}}
    @if (!request()->cookies->has('cookie_consent'))
        <div class="position-fixed bottom-0 start-0 end-0" style="z-index: 1080;">
            <div class="container pb-4">
                <div class="bg-white border rounded-3 shadow-lg px-4 py-3 py-md-4" style="border-color: rgba(138,150,166,0.2);">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-8">
                            <p class="mb-1 small text-lux-greyBlue text-uppercase" style="letter-spacing: 0.08em;">
                                Gestion des cookies
                            </p>
                            <h2 class="h6 mb-2 text-lux-dark-blue fw-semibold" style="font-family: 'Playfair Display', serif;">
                                Nous respectons votre vie privée
                            </h2>
                            <p class="mb-0 small text-lux-greyBlue" style="line-height: 1.7;">
                                Nous utilisons des cookies essentiels pour le bon fonctionnement du site et, avec votre accord,
                                des cookies de mesure d’audience pour améliorer votre expérience. Vous pouvez modifier votre choix à tout moment dans nos mentions légales.
                            </p>
                        </div>
                        <div class="col-12 col-md-4 d-flex justify-content-md-end gap-2 mt-2 mt-md-0">
                            <form method="POST" action="{{ route('cookies.reject') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm w-100 w-md-auto"
                                        style="border-radius: 999px; border: 1px solid rgba(138,150,166,0.4); background-color: white; color: var(--lux-dark-blue); font-weight: 500;">
                                    Continuer sans accepter
                                </button>
                            </form>
                            <form method="POST" action="{{ route('cookies.accept') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm w-100 w-md-auto"
                                        style="border-radius: 999px; background-color: var(--lux-gold); border: none; color: var(--lux-dark-blue); font-weight: 600; box-shadow: 0 8px 18px rgba(203,174,130,0.35);">
                                    Accepter et continuer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <main>
        @yield('content')
    </main>
    
    @include('components.footer')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    @stack('scripts')
</body>
</html>


