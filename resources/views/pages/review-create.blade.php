@extends('layouts.dashboard')

@section('title', 'Déposer un avis | LUXÎLES')

@section('content')
    <div class="container-fluid px-4 py-4">
        <a href="{{ route('espace-client.reservations') }}" class="text-decoration-none small text-lux-greyBlue d-inline-flex align-items-center gap-2 mb-4">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes réservations
        </a>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border shadow-sm" style="border-radius: 0.75rem;">
                    <div class="card-body p-4 p-md-5">
                        <span class="text-lux-gold text-uppercase small fw-medium d-block mb-2" style="letter-spacing: 0.15em;">Votre avis</span>
                        <h1 class="h3 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">{{ $reservation->villa->name }}</h1>
                        <p class="text-lux-greyBlue small mb-4">
                            Séjour du {{ $reservation->check_in_date->format('d/m/Y') }} au {{ $reservation->check_out_date->format('d/m/Y') }}
                            @if($daysLeft > 0)
                                — il vous reste {{ $daysLeft }} jour{{ $daysLeft > 1 ? 's' : '' }} pour déposer votre avis.
                            @endif
                        </p>

                        <div class="alert alert-light border small mb-4">
                            Votre avis sera examiné par notre équipe avant publication sur la fiche de la villa (délai habituel : quelques jours).
                        </div>

                        <form action="{{ route('espace-client.reviews.store', $reservation) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-medium text-lux-dark-blue">Note globale <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2 flex-wrap" id="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="mb-0" style="cursor: pointer;">
                                            <input type="radio" name="rating" value="{{ $i }}" class="d-none rating-input" {{ (int) old('rating') === $i ? 'checked' : '' }} required>
                                            <i class="fa-solid fa-star fa-2x rating-star text-muted" data-value="{{ $i }}"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('rating')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="form-label fw-medium text-lux-dark-blue">Votre commentaire <span class="text-danger">*</span></label>
                                <textarea name="comment" id="comment" rows="6" class="form-control" required minlength="10" maxlength="5000" placeholder="Partagez votre expérience de séjour…">{{ old('comment') }}</textarea>
                                @error('comment')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn btn-lux-primary text-white px-4">Envoyer mon avis</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.rating-input').forEach(function(input) {
            input.addEventListener('change', updateStars);
        });
        function updateStars() {
            const val = parseInt(document.querySelector('.rating-input:checked')?.value || 0, 10);
            document.querySelectorAll('.rating-star').forEach(function(star) {
                const n = parseInt(star.dataset.value, 10);
                star.classList.toggle('text-lux-gold', n <= val);
                star.classList.toggle('text-muted', n > val);
            });
        }
        updateStars();
    </script>
    @endpush
@endsection
