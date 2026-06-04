@props(['review'])

@php
    $reviewUser = $review->user;
    $locationParts = array_filter([
        $reviewUser?->city,
        $reviewUser?->country,
    ]);
    $reviewLocation = $locationParts !== []
        ? implode(', ', $locationParts)
        : ($review->villa?->island?->name ?? '');
    $reviewInitial = strtoupper(substr($reviewUser?->first_name ?? '?', 0, 1));
    $reviewFullName = trim(($reviewUser?->first_name ?? '') . ' ' . ($reviewUser?->last_name ?? '')) ?: 'Voyageur';
@endphp

<div class="col-md-4">
    <div class="testimonial-card">
        <div class="d-flex align-items-center gap-1 mb-4 text-lux-gold">
            @for($star = 1; $star <= 5; $star++)
                <i class="fas fa-star{{ $star <= $review->rating ? '' : ' opacity-25' }}"></i>
            @endfor
        </div>
        <p class="text-lux-gray fst-italic mb-4" style="line-height: 1.8;">&ldquo;{{ Str::limit(trim($review->comment), 220) }}&rdquo;</p>
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle overflow-hidden flex-shrink-0" style="width: 48px; height: 48px;">
                @if($reviewUser?->photo_url)
                    <img src="{{ asset('storage/' . $reviewUser->photo_url) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $reviewFullName }}">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center fw-medium" style="background-color: rgba(203, 174, 130, 0.25); color: var(--lux-dark-blue); font-size: 1.125rem;">
                        {{ $reviewInitial }}
                    </div>
                @endif
            </div>
            <div>
                <p class="fw-medium mb-0" style="color: var(--lux-dark-blue);">{{ $reviewFullName }}</p>
                @if($reviewLocation !== '')
                    <p class="text-lux-gray small mb-0">{{ $reviewLocation }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
