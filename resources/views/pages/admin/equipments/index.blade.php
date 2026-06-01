@extends('layouts.admin')

@section('title', 'Équipements | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Équipements</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Référentiel équipements
            </h1>
            <p class="text-muted small mb-0">§3.5 CDC — seuls les équipements marqués « filtre de recherche » apparaissent sur la page Villas.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card border shadow-sm mb-4" style="border-radius: 0.75rem;">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 text-lux-dark-blue">Ajouter un équipement</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.equipments.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label for="name" class="form-label small">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control form-control-sm" required maxlength="100" value="{{ old('name') }}">
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label small">Catégorie</label>
                    <input type="text" name="category" id="category" class="form-control form-control-sm" maxlength="50" placeholder="ex. confort" value="{{ old('category') }}">
                </div>
                <div class="col-md-2">
                    <label for="icon" class="form-label small">Icône</label>
                    <input type="text" name="icon" id="icon" class="form-control form-control-sm" maxlength="50" placeholder="fa-wifi" value="{{ old('icon') }}">
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_search_filter" id="is_search_filter_new" value="1" {{ old('is_search_filter') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="is_search_filter_new">Filtre recherche</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-lux-primary text-white w-100">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border shadow-sm" style="border-radius: 0.75rem; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="px-4 py-3 small text-uppercase">Nom</th>
                        <th class="px-4 py-3 small text-uppercase">Catégorie</th>
                        <th class="px-4 py-3 small text-uppercase">Villas</th>
                        <th class="px-4 py-3 small text-uppercase text-center">Filtre recherche</th>
                        <th class="px-4 py-3 small text-uppercase text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipments as $equipment)
                        <tr>
                            <td class="px-4 py-3 fw-medium text-lux-dark-blue">{{ $equipment->name }}</td>
                            <td class="px-4 py-3 text-lux-greyBlue small">{{ $equipment->category ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">{{ $equipment->villas_count }}</td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.equipments.toggle-search-filter', $equipment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $equipment->is_search_filter ? 'btn-success' : 'btn-outline-secondary' }}" title="Utiliser comme filtre de recherche">
                                        <i class="fa-solid fa-{{ $equipment->is_search_filter ? 'check' : 'minus' }}"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit-equipment-{{ $equipment->id }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                @if($equipment->villas_count === 0)
                                    <form action="{{ route('admin.equipments.destroy', $equipment) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet équipement ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        <tr class="collapse" id="edit-equipment-{{ $equipment->id }}">
                            <td colspan="5" class="px-4 py-3 bg-light">
                                <form action="{{ route('admin.equipments.update', $equipment) }}" method="POST" class="row g-2 align-items-end">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-3">
                                        <input type="text" name="name" class="form-control form-control-sm" value="{{ $equipment->name }}" required maxlength="100">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="category" class="form-control form-control-sm" value="{{ $equipment->category }}" maxlength="50" placeholder="Catégorie">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="icon" class="form-control form-control-sm" value="{{ $equipment->icon }}" maxlength="50" placeholder="Icône">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_search_filter" value="1" id="filter-{{ $equipment->id }}" {{ $equipment->is_search_filter ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="filter-{{ $equipment->id }}">Utiliser comme filtre de recherche</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-warning w-100">Enregistrer</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-lux-greyBlue">Aucun équipement dans le référentiel</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
