@extends('layouts.app')

@section('title', 'Politique de Confidentialité | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Politique de Confidentialité Section -->
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
                                <h1 class="h2 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Politique de Confidentialité</h1>
                                <p class="text-muted small">(Règlement Général sur la Protection des Données – RGPD)</p>
                            </div>

                            <div class="legal-content" style="line-height: 1.8; color: var(--lux-dark-blue);">
                                <p class="mb-4">
                                    La présente politique de confidentialité a pour objet d'informer toute personne utilisant le site internet <strong>LUXÎLES</strong> des modalités de collecte, d'utilisation et de protection de ses données personnelles, conformément au Règlement (UE) 2016/679 du 27 avril 2016 (RGPD) et à la loi Informatique et Libertés modifiée.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 1 – IDENTITÉ DU RESPONSABLE DE TRAITEMENT</h3>
                                <p class="mb-4">
                                    Le responsable du traitement des données personnelles est la société <strong>BLUE SECRET</strong>, Société par actions simplifiée, au capital de cinq cent euros, immatriculée au RCS de POINTE-A-PITRE sous le numéro 852 624 154, dont le siège social est situé Palais Sainte-Marguerite - 4 Lot Domaine du Grand Bleu - 97160 LE MOULE.<br>
                                    <strong>Contact :</strong> <a href="mailto:contact.luxiles@gmail.com" class="text-lux-gold">contact.luxiles@gmail.com</a>
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 2 – DONNÉES PERSONNELLES COLLECTÉES</h3>
                                <p class="mb-3">Dans le cadre de l'utilisation du site et des services proposés, les données personnelles suivantes peuvent être collectées :</p>
                                <ul class="mb-4">
                                    <li><strong>Contact :</strong> prénom, nom, adresse email, numéro de téléphone, sujet, message</li>
                                    <li><strong>Identification :</strong> prénom, nom, adresse email, numéro de téléphone, mot de passe (hashé)</li>
                                    <li><strong>Données liées aux réservations :</strong> données utilisateurs, dates de séjour, nombre de voyageurs, demandes spéciales</li>
                                    <li><strong>Profil :</strong> prénom, nom, adresse email, numéro de téléphone, adresse postale</li>
                                </ul>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 3 – MODES DE COLLECTE</h3>
                                <p class="mb-4">Les données personnelles sont collectées notamment lors :</p>
                                <ul class="mb-4">
                                    <li>De la navigation sur le site</li>
                                    <li>De l'utilisation des formulaires de contact ou de réservation</li>
                                    <li>Des échanges par email ou téléphone</li>
                                    <li>Des réservations effectuées via des plateformes partenaires</li>
                                </ul>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 4 – FINALITÉS DU TRAITEMENT</h3>
                                <p class="mb-4">Les données personnelles sont collectées pour les finalités suivantes :</p>
                                <ul class="mb-4">
                                    <li>Gestion des demandes de contact et des réservations</li>
                                    <li>Exécution des prestations de conciergerie et de location saisonnière</li>
                                    <li>Gestion administrative, comptable et fiscale</li>
                                    <li>Amélioration de l'expérience utilisateur et des services</li>
                                    <li>Respect des obligations légales et réglementaires</li>
                                </ul>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 5 – BASES LÉGALES DU TRAITEMENT</h3>
                                <p class="mb-4">Les traitements reposent sur :</p>
                                <ul class="mb-4">
                                    <li>Le consentement de la personne concernée</li>
                                    <li>L'exécution d'un contrat ou de mesures précontractuelles</li>
                                    <li>Le respect d'obligations légales</li>
                                    <li>L'intérêt légitime du responsable de traitement</li>
                                </ul>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 6 – DESTINATAIRES DES DONNÉES</h3>
                                <p class="mb-4">Les données personnelles sont destinées :</p>
                                <ul class="mb-4">
                                    <li>Aux services internes du responsable de traitement</li>
                                    <li>Aux prestataires techniques et informatiques</li>
                                    <li>Aux plateformes de réservation (Airbnb, Booking, Abritel, etc.)</li>
                                    <li>Aux autorités administratives ou judiciaires lorsque la loi l'exige</li>
                                </ul>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 7 – TRANSFERTS HORS UNION EUROPÉENNE</h3>
                                <p class="mb-3">Dans le cadre du fonctionnement du site et des services proposés, certaines données personnelles peuvent être transférées en dehors de l'Union européenne par l'intermédiaire de prestataires tiers.</p>
                                <p class="mb-3"><strong>Services tiers identifiés :</strong></p>
                                <ul class="mb-4">
                                    <li><strong>Stripe (services de paiement) :</strong> États-Unis – transfert hors UE encadré par des Clauses Contractuelles Types (SCC) adoptées par la Commission européenne.</li>
                                    <li><strong>Google (Google Maps, Google Fonts) :</strong> États-Unis – transfert hors UE encadré par des Clauses Contractuelles Types (SCC).</li>
                                    <li><strong>jsDelivr (réseau de diffusion de contenu – CDN) :</strong> États-Unis – transfert hors UE. Le responsable de traitement s'assure de l'existence de garanties appropriées et de mesures contractuelles et techniques adaptées.</li>
                                </ul>
                                <p class="mb-4">
                                    Le responsable de traitement veille à ce que ces transferts soient encadrés par des garanties appropriées conformément aux articles 44 à 49 du RGPD, notamment par la mise en place de Clauses Contractuelles Types et, le cas échéant, de mesures complémentaires.<br>
                                    Certaines données peuvent être transférées hors de l'Union européenne via l'utilisation de plateformes ou d'outils situés hors UE. Dans ce cas, le responsable de traitement s'assure de l'existence de garanties appropriées conformément au RGPD.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 8 – DURÉE DE CONSERVATION DES DONNÉES</h3>
                                <p class="mb-4">Les données personnelles sont conservées uniquement pendant la durée nécessaire aux finalités poursuivies :</p>
                                <ul class="mb-4">
                                    <li><strong>Données clients :</strong> 3 ans à compter du dernier contact actif en application de l'article 5 RGPD et du référentiel CNIL</li>
                                    <li><strong>Données de réservation :</strong> 5 ans après la fin du séjour dans le respect de la prescription civile de l'article 2224 du Code civil</li>
                                    <li><strong>Données de facturation :</strong> 10 ans en application de l'article L.123-22 du Code de commerce</li>
                                    <li><strong>Logs :</strong> 6 mois dans le respect de l'article 32 RGPD "sécurité des systèmes et du référentiel CNIL sécurité</li>
                                    <li><strong>Emails :</strong> 3 ans à compter du dernier échange, sauf mail lié à une facture la durée de conservation est de 10 ans, et sauf mail lié à un litige en cours la durée de conservation est jusqu'à la clôture+prescription applicable</li>
                                </ul>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 9 – SÉCURITÉ DES DONNÉES</h3>
                                <p class="mb-4">
                                    Le responsable de traitement met en œuvre des mesures techniques et organisationnelles appropriées afin de garantir la sécurité, l'intégrité et la confidentialité des données personnelles et d'éviter tout accès non autorisé, perte ou divulgation.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 10 – DROITS DES PERSONNES CONCERNÉES</h3>
                                <p class="mb-4">
                                    Conformément au Règlement Général sur la Protection des Données (RGPD), toute personne dispose d'un droit d'accès, de rectification, d'effacement, de limitation, d'opposition et de portabilité de ses données personnelles.<br>
                                    Ces droits peuvent être exercés à tout moment en adressant une demande à l'adresse suivante : <a href="mailto:contact.luxiles@gmail.com" class="text-lux-gold">contact.luxiles@gmail.com</a>.
                                </p>
                                <p class="mb-4">
                                    Une réponse sera apportée dans un délai maximal d'un (1) mois à compter de la réception de la demande.<br>
                                    La suppression de certaines données peut être limitée lorsque leur conservation est imposée par une obligation légale (notamment comptable ou fiscale).<br>
                                    Les données faisant l'objet d'un droit à la portabilité seront transmises dans un format structuré, couramment utilisé et lisible par machine.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 11 – RÉCLAMATION AUPRÈS DE LA CNIL</h3>
                                <p class="mb-4">
                                    Si la personne concernée estime que ses droits ne sont pas respectés, elle peut introduire une réclamation auprès de la Commission Nationale de l'Informatique et des Libertés (CNIL) – <a href="https://www.cnil.fr" target="_blank" class="text-lux-gold">www.cnil.fr</a>.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 12 – MODIFICATION DE LA POLITIQUE</h3>
                                <p class="mb-4">
                                    La présente politique de confidentialité peut être modifiée à tout moment afin de garantir sa conformité avec la réglementation en vigueur. La version applicable est celle publiée sur le site à la date de consultation.
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