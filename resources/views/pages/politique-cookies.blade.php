@extends('layouts.app')

@section('title', 'Politique de Cookies | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Politique de Cookies Section -->
    <section class="py-5" style="margin-top: 80px; min-height: calc(100vh - 200px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border shadow-lg" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <div class="card-body p-4 p-lg-5">
                            <div class="text-center mb-5">
                                <div class="text-center mb-3">
                                    <img src="{{ asset('Social_Media_Profil_Bleu.png') }}" alt="LUX Îles" class="mb-3" style="height: 80px; width: auto; object-fit: contain;">
                                </div>
                                <h1 class="h2 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Politique de Cookies</h1>
                                <p class="text-muted small">(Règlement Général sur la Protection des Données – RGPD)</p>
                            </div>

                            <div class="legal-content" style="line-height: 1.8; color: var(--lux-dark-blue);">
                                <p class="mb-4">
                                    La présente politique de cookies a pour objet d'informer les utilisateurs du site <strong>LUXÎLES</strong> de l'utilisation de cookies et autres traceurs, conformément au Règlement Général sur la Protection des Données (RGPD) et aux recommandations de la CNIL.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 1 – DÉFINITION DES COOKIES</h3>
                                <p class="mb-4">
                                    Un cookie est un petit fichier texte susceptible d'être déposé sur le terminal de l'utilisateur (ordinateur, tablette, smartphone) lors de la consultation d'un site internet.<br>
                                    Il permet notamment de reconnaître le terminal de l'utilisateur pendant la durée de validité du cookie.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 2 – FINALITÉS DES COOKIES UTILISÉS</h3>
                                <p class="mb-4">Le site utilise des cookies pour :</p>
                                <ul class="mb-4">
                                    <li>Assurer le bon fonctionnement du site</li>
                                    <li>Améliorer l'expérience utilisateur</li>
                                    <li>Mesurer l'audience et les performances</li>
                                    <li>Sécuriser les parcours de réservation</li>
                                    <li>Mémoriser les préférences de l'utilisateur</li>
                                </ul>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 3 – TYPOLOGIE DES COOKIES</h3>

                                <h4 class="h5 font-serif text-lux-dark-blue mt-4 mb-3" style="font-family: 'Playfair Display', serif;">3.1 Cookies strictement nécessaires</h4>
                                <p class="mb-4">Ces cookies sont indispensables au fonctionnement du site et ne peuvent être désactivés.</p>

                                <h4 class="h5 font-serif text-lux-dark-blue mt-4 mb-3" style="font-family: 'Playfair Display', serif;">3.2 Cookies tiers</h4>
                                <p class="mb-4">Ils peuvent être déposés par des services externes tels que des plateformes de réservation ou outils statistiques.</p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 4 – COOKIES TIERS ET PLATEFORMES PARTENAIRES</h3>
                                <p class="mb-4">
                                    Le site peut intégrer des services fournis par des tiers (Airbnb, Booking, Abritel, outils de paiement ou d'analyse).<br>
                                    Ces tiers peuvent déposer leurs propres cookies, soumis à leurs politiques de confidentialité respectives.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 5 – BASE LÉGALE DU TRAITEMENT</h3>
                                <p class="mb-4">
                                    Les cookies strictement nécessaires reposent sur l'intérêt légitime du responsable de traitement.<br>
                                    Les autres cookies reposent sur le consentement préalable de l'utilisateur.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 6 – RECUEIL DU CONSENTEMENT</h3>
                                <p class="mb-4">
                                    Lors de la première visite sur le site, un bandeau cookies informe l'utilisateur et lui permet :
                                </p>
                                <ul class="mb-4">
                                    <li>D'accepter tous les cookies</li>
                                    <li>De refuser tout ou partie des cookies</li>
                                    <li>De personnaliser ses choix</li>
                                </ul>
                                <p class="mb-4">Aucun cookie soumis à consentement n'est déposé avant l'expression du choix de l'utilisateur.</p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 7 – GESTION ET PARAMÉTRAGE DES COOKIES</h3>
                                <p class="mb-4">
                                    L'utilisateur peut à tout moment modifier ses préférences :
                                </p>
                                <ul class="mb-4">
                                    <li>via le module de gestion des cookies du site</li>
                                    <li>via les paramètres de son navigateur</li>
                                </ul>
                                <p class="mb-4">La désactivation de certains cookies peut affecter le fonctionnement du site.</p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 8 – DURÉE DE CONSERVATION</h3>
                                <p class="mb-4">
                                    Conformément aux recommandations de la CNIL, les cookies sont conservés pour une durée maximale de 13 mois à compter de leur dépôt.<br>
                                    Le consentement de l'utilisateur est conservé pour une durée maximale de 6 mois.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 9 – DONNÉES COLLECTÉES PAR LES COOKIES</h3>
                                <p class="mb-4">Les cookies peuvent collecter :</p>
                                <ul class="mb-4">
                                    <li>Adresse IP anonymisée</li>
                                    <li>Données de navigation</li>
                                    <li>Type de terminal et navigateur</li>
                                    <li>Pages consultées</li>
                                </ul>
                                <p class="mb-4">Aucune donnée sensible n'est collectée via les cookies.</p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 10 – DROITS DE L'UTILISATEUR</h3>
                                <p class="mb-4">
                                    Conformément au RGPD, l'utilisateur dispose de droits sur ses données personnelles.<br>
                                    Pour toute question relative aux cookies, il peut contacter : <a href="mailto:contact.luxiles@gmail.com" class="text-lux-gold">contact.luxiles@gmail.com</a>.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 11 – MODIFICATION DE LA POLITIQUE DE COOKIES</h3>
                                <p class="mb-4">
                                    La présente politique peut être modifiée à tout moment afin de garantir sa conformité avec la réglementation en vigueur.<br>
                                    La version applicable est celle publiée sur le site à la date de consultation.
                                </p>
                            </div>

                            <div class="text-center mt-5">
                                <a href="{{ route('home') }}" class="btn btn-lux-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection