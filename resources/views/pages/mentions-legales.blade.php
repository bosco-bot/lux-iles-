@extends('layouts.app')

@section('title', 'Mentions Légales | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Mentions Légales Section -->
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
                                <h1 class="h2 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Mentions Légales</h1>
                                <p class="text-muted small">(Conformément à la loi n°2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique - LCEN)</p>
                            </div>

                            <div class="legal-content" style="line-height: 1.8; color: var(--lux-dark-blue);">
                                <p class="mb-4">
                                    Conformément aux dispositions de la loi n°2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique (LCEN),<br>
                                    Il est précisé aux utilisateurs du site <strong>LUXÎLES</strong> l'identité des différents intervenants dans le cadre de sa réalisation et de son suivi.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 1 – ÉDITEUR DU SITE</h3>
                                <p class="mb-4">
                                    Le présent site est édité par :<br>
                                    <strong>BLUE SECRET</strong>, Société par actions simplifiée, au capital de cinq cent euros, immatriculée au RCS de POINTE-A-PITRE sous le numéro 852 624 154, dont le siège social est situé Palais Sainte-Marguerite - 4 Lot Domaine du Grand Bleu - 97160 LE MOULE.
                                </p>
                                <p class="mb-4">
                                    <strong>Téléphone :</strong> +33 7 66 33 41 98<br>
                                    <strong>Adresse électronique :</strong> <a href="mailto:contact.luxiles@gmail.com" class="text-lux-gold">contact.luxiles@gmail.com</a>
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 2 – DIRECTEUR DE LA PUBLICATION</h3>
                                <p class="mb-4">
                                    Le Directeur de la publication est <strong>Elisabeth TONY</strong>, en qualité de directrice, représentant légal de la société éditrice.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 3 – HÉBERGEMENT DU SITE</h3>
                                <p class="mb-4">
                                    Le site est hébergé par :<br>
                                    <strong>Hostinger</strong>, SAS,<br>
                                    dont le siège social est situé : Palais Sainte-Marguerite 4 Domaine du Grand Bleu, 97160 Le Moule<br>
                                    <strong>Téléphone :</strong> +33 7 66 33 41 98
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 4 – ACCÈS AU SITE</h3>
                                <p class="mb-4">
                                    Le site est accessible gratuitement à tout utilisateur disposant d'un accès à internet.<br>
                                    L'éditeur met en œuvre tous les moyens raisonnables à sa disposition pour assurer un accès de qualité au site, mais n'est tenu à aucune obligation de résultat.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 5 – PROPRIÉTÉ INTELLECTUELLE</h3>
                                <p class="mb-4">
                                    L'ensemble des contenus présents sur le site (textes, images, graphismes, logos, icônes, sons, logiciels, etc.) est protégé par les dispositions du Code de la propriété intellectuelle.
                                </p>
                                <p class="mb-4">
                                    Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de l'éditeur.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 6 – LIMITATION DE RESPONSABILITÉ</h3>
                                <p class="mb-4">
                                    L'éditeur ne saurait être tenu responsable des dommages directs ou indirects causés au matériel de l'utilisateur lors de l'accès au site, résultant notamment de l'utilisation d'un matériel ne répondant pas aux spécifications requises ou de l'apparition d'un bug ou d'une incompatibilité.
                                </p>
                                <p class="mb-4">
                                    L'éditeur ne pourra également être tenu responsable des dommages indirects consécutifs à l'utilisation du site.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 7 – LIENS HYPERTEXTES</h3>
                                <p class="mb-4">
                                    Le site peut contenir des liens hypertextes vers d'autres sites internet.<br>
                                    L'éditeur n'exerce aucun contrôle sur ces sites et décline toute responsabilité quant à leur contenu.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 8 – DONNÉES PERSONNELLES</h3>
                                <p class="mb-4">
                                    Les informations relatives à la collecte et au traitement des données personnelles sont détaillées dans la <a href="{{ route('politique-confidentialite') }}" class="text-lux-gold text-decoration-none">politique de confidentialité</a> accessible sur le site, conformément au RGPD.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 9 – COOKIES</h3>
                                <p class="mb-4">
                                    La navigation sur le site est susceptible de provoquer l'installation de cookies sur le terminal de l'utilisateur.<br>
                                    Les modalités de gestion des cookies sont détaillées dans la <a href="{{ route('politique-cookies') }}" class="text-lux-gold text-decoration-none">politique de cookies</a> accessible sur le site.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 10 – DROIT APPLICABLE ET ATTRIBUTION DE JURIDICTION</h3>
                                <p class="mb-4">
                                    Les présentes mentions légales sont régies par le droit français.<br>
                                    En cas de litige et à défaut de résolution amiable, les tribunaux français seront seuls compétents.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 11 – CONTACT</h3>
                                <p class="mb-4">
                                    Pour toute question ou demande d'information concernant le site, l'utilisateur peut contacter l'éditeur à l'adresse suivante : <a href="mailto:contact.luxiles@gmail.com" class="text-lux-gold">contact.luxiles@gmail.com</a>
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