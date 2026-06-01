@props(['reservation', 'reviewService', 'btnClass' => 'btn btn-lux-primary w-100 py-2 rounded small fw-medium text-decoration-none'])

@php
    $user = auth()->user();
    $canReview = $user && $reviewService->canUserSubmitReview($user, $reservation);
    $review = $reservation->review;
@endphp

@if($canReview)
    <a href="{{ route('espace-client.reviews.create', $reservation) }}" class="{{ $btnClass }}">
        <i class="fa-solid fa-star me-2"></i> Déposer un avis
    </a>
@elseif($review?->isPending())
    <span class="badge bg-warning bg-opacity-10 text-warning w-100 py-2">Avis en cours de modération</span>
@elseif($review?->isPublished())
    <span class="badge bg-success bg-opacity-10 text-success w-100 py-2">Avis publié — merci !</span>
@endif
