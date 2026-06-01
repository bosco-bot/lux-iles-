@extends('layouts.admin')

@section('title', 'Modifier code promo | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.promo-codes.index') }}" class="text-white-50">Codes promo</a>
    <span class="text-white"> / {{ $promoCode->code }}</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-serif mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Modifier {{ $promoCode->code }}</h1>
        <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-outline-secondary btn-sm">Retour</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @if($promoCode->uses_count > 0)
        <div class="alert alert-info small">
            Ce code a été utilisé {{ $promoCode->uses_count }} fois. La suppression n'est pas possible — désactivez-le si besoin.
        </div>
    @endif

    <div class="card border shadow-sm p-4" style="border-radius: 0.75rem;">
        <form action="{{ route('admin.promo-codes.update', $promoCode) }}" method="POST">
            @csrf
            @method('PUT')
            @include('pages.admin.promo-codes._form', ['promoCode' => $promoCode])
            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-lux-primary text-white">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection
