@extends('layouts.app')

@section('title', 'Conditions Générales de Vente | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- CGV Section -->
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
                                <h1 class="h2 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Conditions Générales de Vente</h1>
                                <p class="text-muted small">(CGV - Location saisonnière et services de conciergerie)</p>
                            </div>

                            <div class="legal-content" style="line-height: 1.8; color: var(--lux-dark-blue);">
                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 1 – IDENTIFICATION DU PRESTATAIRE</h3>
                                <p class="mb-4">
                                    Le Prestataire est la société BLUE SECRET, Société par actions simplifiée, au capital de cinq cent euros, immatriculée au RCS de POINTE-A-PITRE sous le numéro 852 624 154, dont le siège social est situé Palais Sainte-Marguerite - 4 Lot Domaine du Grand Bleu - 97160 LE MOULE, représentée par Elisabeth TONY.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 2 – OBJET</h3>
                                <p class="mb-4">
                                    Les présentes Conditions Générales de Vente ont pour objet de définir les conditions dans lesquelles le Prestataire fournit des services de conciergerie, de gestion de location saisonnière et de services annexes pour le compte de propriétaires, au bénéfice de voyageurs.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 3 – CHAMP D'APPLICATION</h3>
                                <p class="mb-4">
                                    Les CGV s'appliquent à toute commande de prestations et prévalent sur tout autre document contradictoire.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 4 – DESCRIPTION DES PRESTATIONS</h3>
                                <p class="mb-4">
                                    Gestion des annonces sur plateforme (Airbnb, Booking, Abritel…), coordination des réservations, accueil des voyageurs, remise et récupération des clés, ménage, maintenance courante, assistance durant le séjour.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 5 – STATUT DU PRESTATAIRE</h3>
                                <p class="mb-4">
                                    Le Prestataire agit exclusivement en qualité d'intermédiaire. Il n'est ni bailleur, ni hébergeur, ni exploitant hôtelier.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 6 – OBLIGATIONS DU PROPRIÉTAIRE</h3>
                                <p class="mb-4">
                                    Le Propriétaire garantit la conformité du logement, sa décence, la validité de ses assurances, ainsi que le respect des obligations fiscales, sociales et administratives applicables.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 7 – OBLIGATIONS DU VOYAGEUR</h3>
                                <p class="mb-4">
                                    Le Voyageur s'engage à user paisiblement du logement, à respecter le règlement intérieur et à répondre de toute dégradation causée.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 8 – PLATEFORMES DE RÉSERVATION</h3>
                                <p class="mb-4">
                                    Les réservations effectuées via des plateformes tierces sont soumises à leurs conditions générales propres. Le Prestataire ne saurait être tenu responsable des dysfonctionnements imputables aux plateformes.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 9 – TARIFS</h3>
                                <p class="mb-4">
                                    Les tarifs sont exprimés en euros hors taxes et communiqués préalablement à toute validation.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 10 – MODALITÉS DE PAIEMENT</h3>
                                <p class="mb-4">
                                    Paiement comptant ou selon échéancier convenu. Tout retard entraîne pénalités légales et indemnité forfaitaire de recouvrement.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 11 – DÉPÔT DE GARANTIE / SÉQUESTRE</h3>
                                <p class="mb-4">
                                    Un dépôt de garantie pourra être exigé du Voyageur afin de couvrir les éventuels dommages, pertes ou manquements constatés à l'issue du séjour.<br>
                                    Ce dépôt pourra être encaissé, conservé ou séquestré selon les modalités définies dans les conditions particulières ou via la plateforme de réservation utilisée.<br>
                                    En cas de dégradations, le montant du dépôt de garantie pourra être conservé partiellement ou totalement, après justification, afin de couvrir les frais de remise en état, de remplacement ou de nettoyage supplémentaire.<br>
                                    Le solde éventuel sera restitué dans un délai maximal de 30 jours après la fin du séjour, sous réserve de l'absence de litige.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 12 – CLAUSE DE COMPENSATION AUTOMATIQUE</h3>
                                <p class="mb-4">
                                    En cas de sommes dues par le Client au titre de pénalités, réparations ou indemnisations, le Prestataire est autorisé à procéder à une compensation automatique avec toute somme détenue pour le compte du Client.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 13 – CLAUSE LITIGES – DÉGRADATIONS / ASSURANCE VILLÉGIATURE</h3>
                                <p class="mb-4">
                                    Le Voyageur est responsable de toute dégradation. Il est recommandé de souscrire une assurance villégiature couvrant les dommages locatifs. À défaut, les frais pourront être imputés au dépôt de garantie.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 14 – CLAUSE PÉNALE</h3>
                                <p class="mb-4">
                                    En cas de manquement contractuel du Client, une indemnité forfaitaire équivalente à 15 % des sommes dues pourra être exigée, sans préjudice de dommages et intérêts complémentaires.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 15 – INDEMNISATION</h3>
                                <p class="mb-4">
                                    Le Client s'engage à indemniser le Prestataire contre toute réclamation, action ou condamnation résultant d'un manquement imputable au Propriétaire ou au Voyageur.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 16 – RESPONSABILITÉ</h3>
                                <p class="mb-4">
                                    La responsabilité du Prestataire est limitée aux dommages directs et plafonnée au montant des sommes encaissées au titre de la prestation concernée.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 17 – FORCE MAJEURE</h3>
                                <p class="mb-4">
                                    Sont notamment considérés comme cas de force majeure : cyclones, tempêtes, inondations, coupures réseaux, grèves, catastrophes naturelles.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 18 – ASSURANCES</h3>
                                <p class="mb-4">
                                    Le Prestataire déclare être titulaire d'une assurance responsabilité civile professionnelle.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 19 – RÉSILIATION</h3>
                                <p class="mb-4">
                                    Chaque partie peut résilier le contrat selon les modalités prévues aux conditions particulières.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 20 – MÉDIATION OBLIGATOIRE</h3>
                                <p class="mb-4">
                                    Conformément aux articles L612-1 et suivants du Code de la consommation, le Client est informé de la possibilité de recourir gratuitement à un médiateur de la consommation avant toute action judiciaire.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 21 – DONNÉES PERSONNELLES</h3>
                                <p class="mb-4">
                                    Les données personnelles sont traitées conformément à la réglementation RGPD.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 22 – CLAUSE RGPD CONTRAT PROPRIÉTAIRE</h3>
                                <p class="mb-4">
                                    Le Propriétaire autorise le Prestataire à traiter les données personnelles des voyageurs en qualité de sous-traitant RGPD.
                                </p>

                                <h3 class="h4 font-serif text-lux-dark-blue mt-5 mb-3" style="font-family: 'Playfair Display', serif;">ARTICLE 23 – DROIT APPLICABLE ET JURIDICTION</h3>
                                <p class="mb-4">
                                    Les présentes CGV sont soumises au droit français. Tout litige relève des tribunaux du ressort du siège social.
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