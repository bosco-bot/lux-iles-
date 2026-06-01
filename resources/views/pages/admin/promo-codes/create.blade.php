@extends('layouts.admin')

@section('title', 'Nouveau code promo | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.promo-codes.index') }}" class="text-white-50">Codes promo</a>
    <span class="text-white"> / Nouveau</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-serif mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Nouveau code promotionnel</h1>
        <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-outline-secondary btn-sm">Retour</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="card border shadow-sm p-4" style="border-radius: 0.75rem;">
        <form action="{{ route('admin.promo-codes.store') }}" method="POST">
            @csrf
            @include('pages.admin.promo-codes._form')
            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-lux-primary text-white">Créer le code</button>
            </div>
        </form>
    </div>
@endsection
