<!-- Footer -->
<footer id="footer" class="lux-footer">
    <div class="container">
        <div class="row g-5 mb-5">
            <!-- Brand Column -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="d-flex align-items-center gap-2 mb-4 text-decoration-none">
                    <img src="{{ asset('Social_Media_Profil_Beige_Color.png') }}" alt="LUX Îles" style="height: 60px; width: auto; object-fit: contain;">
                    <span class="text-2xl font-serif text-white" style="font-family: 'Playfair Display', serif; letter-spacing: 0.1em; line-height: 1;">
                        LUX<span class="text-lux-gold italic">Îles</span>
                    </span>
                </div>
                <p class="text-white-50 small mb-4" style="line-height: 1.75;">L'excellence des Caraïbes à votre portée. Location de villas de prestige à Guadeloupe, Marie-Galante et Saint-Martin.</p>
                <div class="d-flex align-items-center gap-4">
                    <a href="https://www.instagram.com/luxilesvillas/" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-circle border border-white border-opacity-20 d-flex align-items-center justify-content-center text-white text-decoration-none footer-social-link">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/profile.php?id=61579747463876" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-circle border border-white border-opacity-20 d-flex align-items-center justify-content-center text-white text-decoration-none footer-social-link">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <!-- <a href="#" class="w-10 h-10 rounded-circle border border-white border-opacity-20 d-flex align-items-center justify-content-center text-white text-decoration-none footer-social-link">
                        <i class="fab fa-linkedin-in"></i>
                    </a> -->
                </div>
            </div>

            <!-- Destinations -->
            <div class="col-6 col-md-3 col-lg-2">
                <h4 class="font-serif text-white mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.125rem;">Nos Destinations</h4>
                <ul class="list-unstyled footer-links">
                    @foreach($footerDestinations ?? [] as $destination)
                        <li class="mb-3"><a href="{{ route('villas.index', ['island' => $destination->id]) }}" class="text-white-50 text-decoration-none small footer-link">{{ $destination->name }}</a></li>
                    @endforeach
                    <li class="mb-3"><a href="{{ route('villas.index') }}" class="text-white-50 text-decoration-none small footer-link">Toutes les villas</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-6 col-md-3 col-lg-3">
                <h4 class="font-serif text-white mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.125rem;">Services</h4>
                <ul class="list-unstyled footer-links">
                    <li class="mb-3"><a href="{{ route('services.conciergerie') }}" class="text-white-50 text-decoration-none small footer-link">Conciergerie 24/7</a></li>
                    <li class="mb-3"><a href="{{ route('services.chef-domicile') }}" class="text-white-50 text-decoration-none small footer-link">Chef à domicile</a></li>
                    <li class="mb-3"><a href="{{ route('services.transferts-prives') }}" class="text-white-50 text-decoration-none small footer-link">Transferts privés</a></li>
                    <li class="mb-3"><a href="{{ route('services.activites-exclusives') }}" class="text-white-50 text-decoration-none small footer-link">Activités exclusives</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-12 col-md-6 col-lg-4">
                <h4 class="font-serif text-white mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.125rem;">Contact</h4>
                <ul class="list-unstyled footer-contact">
                    <li class="mb-4 d-flex align-items-start gap-3 small">
                        <i class="fas fa-phone text-lux-gold mt-1"></i>
                        <div>
                            @php
                                $companyPhone = \App\Helpers\SettingsHelper::get('company_phone', '+33 7 66 33 41 98');
                            @endphp
                            <p class="text-white-50 small mb-0">{{ $companyPhone }}</p>
                            <p class="text-white-50" style="font-size: 0.75rem; opacity: 0.5;">Lun-Dim 8h-20h</p>
                        </div>
                    </li>
                    <li class="mb-4 d-flex align-items-start gap-3 small">
                        <i class="fas fa-envelope text-lux-gold mt-1"></i>
                        @php
                            $companyEmail = \App\Helpers\SettingsHelper::get('company_email', 'contact.luxiles@gmail.com');
                        @endphp
                        <a href="mailto:{{ $companyEmail }}" class="text-white-50 text-decoration-none small footer-link">{{ $companyEmail }}</a>
                    </li>
                    <li class="mb-4 d-flex align-items-start gap-3 small">
                        <i class="fas fa-location-dot text-lux-gold mt-1"></i>
                        @php
                            $companyAddress = \App\Helpers\SettingsHelper::get('company_address', '4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE');
                        @endphp
                        <p class="text-white-50 small mb-0">{!! nl2br(e($companyAddress)) !!}</p>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-top border-white border-opacity-10 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4">
            <p class="text-white-50 small mb-0">© {{ date('Y') }} LUXÎLES. Tous droits réservés.</p>
            <div class="d-flex align-items-center gap-3 text-sm small">
                <a href="{{ route('mentions-legales') }}" class="text-white-50 text-decoration-none small footer-link">Mentions légales</a>
                <span class="text-white-50 mx-2">|</span>
                <a href="{{ route('politique-confidentialite') }}" class="text-white-50 text-decoration-none small footer-link">Politique de confidentialité</a>
                <span class="text-white-50 mx-2">|</span>
                <a href="{{ route('politique-cookies') }}" class="text-white-50 text-decoration-none small footer-link">Politique de cookies</a>
                <span class="text-white-50 mx-2">|</span>
                <a href="{{ route('cgv') }}" class="text-white-50 text-decoration-none small footer-link">CGV</a>
            </div>
        </div>
    </div>
</footer>


