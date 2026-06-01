@extends('layouts.admin')

@section('title', 'Configuration iCal | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Configuration iCal</span>
@endsection

@section('content')
    <!-- Top Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Configuration iCal
            </h1>
            <p class="small text-lux-greyBlue mb-0">Gérez les synchronisations iCal entre vos villas et les plateformes externes</p>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            <a href="{{ route('admin.synchronization') }}" class="btn btn-sm px-3 py-2 rounded small text-lux-greyBlue border">
                <i class="fa-solid fa-arrow-left me-2"></i>Retour
            </a>
            <button class="btn btn-sm px-3 py-2 rounded small fw-medium text-white d-flex align-items-center gap-2" style="background-color: var(--lux-blue);" data-bs-toggle="modal" data-bs-target="#addIcalConfigModal">
                <i class="fa-solid fa-plus"></i>
                Ajouter une configuration
            </button>
        </div>
    </div>
    <!-- Section d'aide -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm d-flex gap-3 p-4" style="background-color: rgba(42, 110, 190, 0.05); border-left: 4px solid var(--lux-blue) !important;">
                <div class="flex-shrink-0 mt-1">
                    <i class="fa-solid fa-circle-info fa-xl" style="color: var(--lux-blue);"></i>
                </div>
                <div>
                    <h4 class="h6 fw-bold mb-2" style="color: var(--lux-dark-blue);">Comment configurer la synchronisation ?</h4>
                    <p class="small mb-3 text-lux-greyBlue">Pour synchroniser vos calendriers, vous devez ajouter l'URL iCal de la plateforme externe (Import) et fournir notre URL iCal à cette même plateforme (Export).</p>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded border">
                                <h5 class="small fw-bold mb-2"><i class="fa-brands fa-airbnb me-2" style="color: #FF5A5F;"></i>Airbnb</h5>
                                <ol class="small mb-0 ps-3 text-lux-greyBlue">
                                    <li>Annonces > Prix et disponibilité</li>
                                    <li>Synchronisation du calendrier</li>
                                    <li>Exporter / Importer le calendrier</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded border">
                                <h5 class="small fw-bold mb-2"><i class="fa-solid fa-hotel me-2" style="color: #003580;"></i>Booking.com</h5>
                                <ol class="small mb-0 ps-3 text-lux-greyBlue">
                                    <li>Tarifs et disponibilités</li>
                                    <li>Synchroniser les calendriers</li>
                                    <li>Importer / Connecter le calendrier</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded border">
                                <h5 class="small fw-bold mb-2"><i class="fa-solid fa-house-chimney me-2" style="color: #2a6ebe;"></i>Abritel / VRBO</h5>
                                <ol class="small mb-0 ps-3 text-lux-greyBlue">
                                    <li>Calendrier > Importation/Exportation</li>
                                    <li>Exporter / Importer le calendrier</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des configurations -->
    <section class="dashboard-card overflow-hidden">
        <div class="p-4 border-bottom">
            <h2 class="h5 font-serif fw-semibold mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Configurations iCal</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem;">Villa</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem;">Plateforme</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem;">URL Export</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem;">URL Import</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem;">Statut</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue text-end" style="font-size: 0.7rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villas as $villa)
                        @php
                            $villaConfigs = $configs->get($villa->id) ?? collect();
                        @endphp
                        @if($villaConfigs->isEmpty())
                            <tr>
                                <td class="py-3 px-4" colspan="6">
                                    <div class="d-flex align-items-center gap-2">
                                        <strong style="color: var(--lux-dark-blue);">{{ $villa->name }}</strong>
                                        <span class="badge bg-secondary small">Aucune configuration</span>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($villaConfigs as $config)
                                <tr>
                                    <td class="py-3 px-4">
                                        <strong style="color: var(--lux-dark-blue);">{{ $villa->name }}</strong>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill small fw-medium" 
                                            style="background-color: @if($config->platform == 'airbnb') rgba(255, 90, 95, 0.1); color: #FF5A5F; @elseif($config->platform == 'booking') rgba(0, 53, 128, 0.1); color: #003580; @else rgba(42, 110, 190, 0.1); color: #2a6ebe; @endif">
                                            {{ $config->platform_name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($config->ical_export_url)
                                            <a href="{{ route('admin.synchronization.ical.export', $villa->id) }}" target="_blank" class="small text-lux-gold">
                                                <i class="fa-solid fa-link me-1"></i>Voir le lien
                                            </a>
                                            <br>
                                            <small class="text-lux-greyBlue">{{ route('admin.synchronization.ical.export', $villa->id) }}</small>
                                        @else
                                            <span class="small text-lux-greyBlue">Non configuré</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($config->ical_import_url)
                                            <span class="small text-lux-greyBlue">{{ \Illuminate\Support\Str::limit($config->ical_import_url, 40) }}</span>
                                        @else
                                            <span class="small text-lux-greyBlue">Non configuré</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($config->is_active)
                                            <span class="badge bg-success small">Actif</span>
                                        @else
                                            <span class="badge bg-secondary small">Inactif</span>
                                        @endif
                                        @if($config->last_sync_at)
                                            <br><span class="small text-lux-greyBlue">{{ $config->last_sync_at->diffForHumans() }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-end">
                                        <button class="btn btn-sm text-lux-gold p-1" onclick="editConfig({{ $config->id }})" title="Modifier">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm text-danger p-1" onclick="deleteConfig({{ $config->id }})" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td class="py-3 px-4 text-center text-lux-greyBlue" colspan="6">Aucune villa disponible</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal Ajouter/Modifier Configuration iCal -->
    <div class="modal fade" id="addIcalConfigModal" tabindex="-1" aria-labelledby="addIcalConfigModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-semibold" id="addIcalConfigModalLabel" style="color: var(--lux-dark-blue);">
                        <i class="fa-solid fa-gear me-2 text-lux-gold"></i>Configuration iCal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="icalConfigForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="config_id" name="config_id">
                        
                        <!-- Section Villa & Plateforme -->
                        <div class="mb-4">
                            <h6 class="small fw-semibold text-uppercase text-lux-greyBlue mb-3" style="letter-spacing: 0.05em;">Informations de base</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="villa_id" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">
                                        <i class="fa-solid fa-house-chimney me-1 text-lux-gold"></i>Villa <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="villa_id" name="villa_id" required style="border-color: rgba(138, 150, 166, 0.2);">
                                        <option value="">Sélectionner une villa</option>
                                        @foreach($villas as $villa)
                                            <option value="{{ $villa->id }}" data-villa-name="{{ $villa->name }}">{{ $villa->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="platform" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">
                                        <i class="fa-solid fa-globe me-1 text-lux-gold"></i>Plateforme <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="platform" name="platform" required style="border-color: rgba(138, 150, 166, 0.2);">
                                        <option value="">Sélectionner une plateforme</option>
                                        <option value="airbnb" data-icon="fa-brands fa-airbnb" data-color="#FF5A5F">Airbnb</option>
                                        <option value="booking" data-icon="fa-solid fa-b" data-color="#003580">Booking.com</option>
                                        <option value="vrbo" data-icon="fa-solid fa-house-laptop" data-color="#2a6ebe">VRBO</option>
                                        <option value="abritel" data-icon="fa-solid fa-building" data-color="#0066cc">Abritel</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section URLs -->
                        <div class="mb-4">
                            <h6 class="small fw-semibold text-uppercase text-lux-greyBlue mb-3" style="letter-spacing: 0.05em;">URLs de synchronisation</h6>
                            
                            <!-- URL Export -->
                            <div class="mb-3">
                                <label for="ical_export_url" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">
                                    <i class="fa-solid fa-arrow-up me-1 text-success"></i>URL Export (Notre site → Plateforme)
                                </label>
                                <div class="input-group">
                                    <input type="url" class="form-control" id="ical_export_url" name="ical_export_url" 
                                           placeholder="Généré automatiquement après sélection de la villa" 
                                           readonly
                                           style="border-color: rgba(138, 150, 166, 0.2); background-color: #f8f9fa;">
                                    <button type="button" class="btn btn-outline-secondary" id="copyExportUrl" title="Copier l'URL">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="fa-solid fa-info-circle me-1"></i>Cette URL sera générée automatiquement. Copiez-la dans les paramètres de votre annonce sur la plateforme.
                                </small>
                            </div>

                            <!-- URL Import -->
                            <div class="mb-3">
                                <label for="ical_import_url" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">
                                    <i class="fa-solid fa-arrow-down me-1 text-primary"></i>URL Import (Plateforme → Notre site) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="url" class="form-control" id="ical_import_url" name="ical_import_url" 
                                           placeholder="https://www.airbnb.fr/calendar/ical/xxx.ics?s=xxx" 
                                           required
                                           style="border-color: rgba(138, 150, 166, 0.2);">
                                    <button type="button" class="btn btn-outline-secondary" id="testImportUrl" title="Tester l'URL">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="fa-solid fa-info-circle me-1"></i>Récupérez cette URL depuis les paramètres de votre annonce sur la plateforme (section Calendrier).
                                </small>
                                <div id="importUrlHelp" class="mt-2 p-2 rounded small" style="background-color: #e7f3ff; border-left: 3px solid #0066cc; display: none;">
                                    <strong class="d-block mb-1">Comment trouver l'URL iCal :</strong>
                                    <ul class="mb-0 ps-3">
                                        <li>Airbnb : Paramètres → Calendrier → Synchroniser les calendriers → Lien de calendrier</li>
                                        <li>Booking.com : Calendrier → Paramètres → Synchronisation → URL iCal</li>
                                        <li>VRBO : Calendrier → Paramètres → Synchronisation → URL iCal</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Section Options -->
                        <div class="mb-3">
                            <h6 class="small fw-semibold text-uppercase text-lux-greyBlue mb-3" style="letter-spacing: 0.05em;">Options</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked style="cursor: pointer;">
                                <label class="form-check-label small fw-medium" for="is_active" style="color: var(--lux-dark-blue); cursor: pointer;">
                                    Activer la synchronisation automatique
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="fa-solid fa-info-circle me-1"></i>Quand activé, la synchronisation se fera automatiquement lors du clic sur "Forcer la synchro".
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times me-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn text-white" style="background-color: var(--lux-blue);">
                            <i class="fa-solid fa-save me-1"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Génération automatique de l'URL d'export
    document.getElementById('villa_id').addEventListener('change', function() {
        const villaId = this.value;
        if (villaId) {
            const exportUrl = '{{ route("admin.synchronization.ical.export", ":id") }}'.replace(':id', villaId);
            document.getElementById('ical_export_url').value = exportUrl;
        } else {
            document.getElementById('ical_export_url').value = '';
        }
    });

    // Afficher l'aide pour l'URL d'import selon la plateforme
    document.getElementById('platform').addEventListener('change', function() {
        const helpDiv = document.getElementById('importUrlHelp');
        if (this.value) {
            helpDiv.style.display = 'block';
        } else {
            helpDiv.style.display = 'none';
        }
    });

    // Copier l'URL d'export
    document.getElementById('copyExportUrl').addEventListener('click', function() {
        const exportUrl = document.getElementById('ical_export_url');
        if (exportUrl.value) {
            exportUrl.select();
            exportUrl.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(exportUrl.value).then(() => {
                const btn = this;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-secondary');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }
    });

    // Tester l'URL d'import
    document.getElementById('testImportUrl').addEventListener('click', function() {
        const importUrl = document.getElementById('ical_import_url').value;
        if (!importUrl) {
            alert('Veuillez d\'abord saisir une URL');
            return;
        }

        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;

        // Test simple : vérifier que l'URL est accessible
        fetch(importUrl, { method: 'HEAD', mode: 'no-cors' })
            .then(() => {
                btn.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-secondary');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                    btn.disabled = false;
                }, 2000);
            })
            .catch(() => {
                btn.innerHTML = '<i class="fa-solid fa-exclamation-triangle text-warning"></i>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 2000);
            });
    });

    // Gestion du formulaire de configuration iCal
    document.getElementById('icalConfigForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Enregistrement...';
        
        const formData = new FormData(this);
        const data = {
            villa_id: formData.get('villa_id'),
            platform: formData.get('platform'),
            ical_export_url: formData.get('ical_export_url'),
            ical_import_url: formData.get('ical_import_url'),
            is_active: formData.get('is_active') === 'on'
        };

        fetch('{{ route("admin.synchronization.configs.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            
            if (data.success) {
                // Afficher un message de succès
                const modal = bootstrap.Modal.getInstance(document.getElementById('addIcalConfigModal'));
                modal.hide();
                
                // Afficher une notification de succès
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                alertDiv.style.zIndex = '9999';
                alertDiv.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i>Configuration enregistrée avec succès<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.body.appendChild(alertDiv);
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            alert('Une erreur est survenue lors de l\'enregistrement');
        });
    });

    // Réinitialiser le formulaire quand le modal est fermé
    document.getElementById('addIcalConfigModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('icalConfigForm').reset();
        document.getElementById('ical_export_url').value = '';
        document.getElementById('importUrlHelp').style.display = 'none';
        document.getElementById('config_id').value = '';
    });

    function editConfig(id) {
        // Charger les données de la configuration
        fetch('{{ route("admin.synchronization.configs.show", ":id") }}'.replace(':id', id), {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.config) {
                const config = data.config;
                document.getElementById('config_id').value = config.id;
                document.getElementById('villa_id').value = config.villa_id;
                document.getElementById('platform').value = config.platform;
                document.getElementById('ical_export_url').value = config.ical_export_url || '{{ route("admin.synchronization.ical.export", ":id") }}'.replace(':id', config.villa_id);
                document.getElementById('ical_import_url').value = config.ical_import_url || '';
                document.getElementById('is_active').checked = config.is_active;
                
                // Afficher l'aide si une plateforme est sélectionnée
                if (config.platform) {
                    document.getElementById('importUrlHelp').style.display = 'block';
                }
                
                // Ouvrir le modal
                const modal = new bootstrap.Modal(document.getElementById('addIcalConfigModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du chargement de la configuration');
        });
    }

    function deleteConfig(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette configuration ? Cette action est irréversible.')) {
            return;
        }

        fetch('{{ route("admin.synchronization.configs.delete", ":id") }}'.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la suppression');
        });
    }
</script>
@endpush

