@php
    $promo = $promoCode ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="code" class="form-label fw-medium text-lux-dark-blue">Code <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', $promo->code ?? '') }}" required maxlength="50" style="text-transform: uppercase;">
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="type" class="form-label fw-medium text-lux-dark-blue">Type <span class="text-danger">*</span></label>
        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="percent" {{ old('type', $promo->type ?? '') === 'percent' ? 'selected' : '' }}>Pourcentage (%)</option>
            <option value="fixed" {{ old('type', $promo->type ?? '') === 'fixed' ? 'selected' : '' }}>Montant fixe (€)</option>
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="value" class="form-label fw-medium text-lux-dark-blue">Valeur <span class="text-danger">*</span></label>
        <input type="number" name="value" id="value" step="0.01" min="0.01"
            class="form-control @error('value') is-invalid @enderror"
            value="{{ old('value', $promo->value ?? '') }}" required>
        @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="valid_from" class="form-label fw-medium text-lux-dark-blue">Valide à partir du</label>
        <input type="date" name="valid_from" id="valid_from" class="form-control @error('valid_from') is-invalid @enderror"
            value="{{ old('valid_from', isset($promo->valid_from) ? $promo->valid_from->format('Y-m-d') : '') }}">
        @error('valid_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="valid_until" class="form-label fw-medium text-lux-dark-blue">Valide jusqu'au</label>
        <input type="date" name="valid_until" id="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
            value="{{ old('valid_until', isset($promo->valid_until) ? $promo->valid_until->format('Y-m-d') : '') }}">
        @error('valid_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="max_uses" class="form-label fw-medium text-lux-dark-blue">Nombre d'utilisations max.</label>
        <input type="number" name="max_uses" id="max_uses" min="1" class="form-control @error('max_uses') is-invalid @enderror"
            value="{{ old('max_uses', $promo->max_uses ?? '') }}" placeholder="Illimité si vide">
        @error('max_uses')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                {{ old('is_active', $promo->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label text-lux-dark-blue" for="is_active">Code actif</label>
        </div>
    </div>
</div>

<p class="small text-lux-greyBlue mt-3 mb-0">
    <i class="fa-solid fa-info-circle me-1"></i>
    Les codes ne sont jamais appliqués automatiquement — le voyageur doit les saisir manuellement (§3.2 CDC).
</p>
