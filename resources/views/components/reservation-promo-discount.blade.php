@props(['reservation', 'class' => 'small text-success mb-1'])

@if($reservation->discount_amount > 0 && $reservation->promoCode)
    <p {{ $attributes->merge(['class' => $class]) }}>
        Réduction ({{ $reservation->promoCode->code }}) : -{{ number_format($reservation->discount_amount, 2, ',', ' ') }} €
    </p>
@endif
