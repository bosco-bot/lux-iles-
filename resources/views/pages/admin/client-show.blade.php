@extends('layouts.admin')

@section('title', 'Détails Client | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.clients') }}" class="text-white-50">Clients</a>
    <span class="text-white"> / {{ $client->first_name }} {{ $client->last_name }}</span>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.clients') }}" class="btn btn-link text-lux-greyBlue p-0" style="text-decoration: none;">
                <i class="fa-solid fa-arrow-left me-2"></i>Retour
            </a>
            <h1 class="h2 font-serif mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                {{ $client->first_name }} {{ $client->last_name }}
            </h1>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.reservations.create', ['client_id' => $client->id]) }}" class="btn btn-outline-lux-gold">
                <i class="fa-solid fa-calendar-plus me-2"></i>Nouvelle réservation
            </a>
            @if($client->must_set_password)
                <form action="{{ route('admin.clients.resend-invitation', $client) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning">
                        <i class="fa-solid fa-paper-plane me-2"></i>Renvoyer l'invitation
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.clients.toggle-status', $client->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn {{ $client->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                    <i class="fa-solid fa-{{ $client->is_active ? 'ban' : 'check' }} me-2"></i>
                    {{ $client->is_active ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Informations Personnelles -->
            <section class="bg-white rounded shadow-sm border p-4 mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
                <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                    <i class="fa-regular fa-user me-2 text-lux-gold"></i>Informations Personnelles
                </h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Prénom</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">{{ $client->first_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Nom</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">{{ $client->last_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Email</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">
                            <a href="mailto:{{ $client->email }}" class="text-lux-dark-blue" style="text-decoration: none;">{{ $client->email }}</a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Téléphone</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}" class="text-lux-dark-blue" style="text-decoration: none;">{{ $client->phone }}</a>
                            @else
                                <span class="text-lux-greyBlue">Non renseigné</span>
                            @endif
                        </p>
                    </div>
                    @if($client->address || $client->city || $client->postal_code)
                    <div class="col-12">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Adresse</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">
                            @if($client->address){{ $client->address }}, @endif
                            @if($client->postal_code){{ $client->postal_code }} @endif
                            @if($client->city){{ $client->city }}@endif
                            @if($client->country), {{ $client->country }}@endif
                        </p>
                    </div>
                    @endif
                    @if($client->birth_date)
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Date de naissance</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">{{ $client->birth_date->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @if($client->nationality)
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Nationalité</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">{{ $client->nationality }}</p>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Statut du compte</label>
                        <p class="mb-0">
                            @if(!$client->is_active)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Inactif</span>
                            @elseif($client->must_set_password)
                                <span class="badge bg-warning bg-opacity-10 text-warning">Invitation envoyée</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success">Actif</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Date d'inscription</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">{{ $client->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem;">Dernière connexion</label>
                        <p class="text-lux-dark-blue fw-medium mb-0">
                            @if($client->last_login_at)
                                {{ $client->last_login_at->format('d/m/Y à H:i') }}
                            @else
                                <span class="text-lux-greyBlue">Jamais connecté</span>
                            @endif
                        </p>
                    </div>
                </div>
            </section>

            <!-- Privilege Club (§3.1 CDC) -->
            <section class="bg-white rounded shadow-sm border p-4 mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
                <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                    <i class="fa-solid fa-crown me-2 text-lux-gold"></i>LUXÎLES PRIVILEGE CLUB
                </h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="small text-uppercase text-lux-greyBlue">Statut actuel</label>
                        <p class="fw-semibold text-lux-dark-blue mb-0">{{ $clubService->tierLabel($client->privilege_tier) }}</p>
                        @if($client->privilege_tier_manual_override)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1">Verrouillé (manuel)</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase text-lux-greyBlue">Séjours (3 ans glissants)</label>
                        <p class="fw-semibold text-lux-dark-blue mb-0">{{ $qualifyingStays }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase text-lux-greyBlue">Palier calculé</label>
                        <p class="fw-semibold text-lux-dark-blue mb-0">{{ $clubService->tierLabel($earnedTier) }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.clients.privilege-club.update', $client) }}" method="POST" class="row g-3 align-items-end mb-3">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="form-label small">Attribuer un palier</label>
                        <select name="privilege_tier" class="form-select form-select-sm">
                            <option value="">— Non membre —</option>
                            @foreach($tierDefinitions as $key => $tier)
                                <option value="{{ $key }}" {{ $client->privilege_tier === $key ? 'selected' : '' }}>{{ $tier['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="privilege_tier_manual_override" value="1" id="tier_locked" {{ $client->privilege_tier_manual_override ? 'checked' : '' }}>
                            <label class="form-check-label small" for="tier_locked">Verrouiller (ignorer le calcul auto)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-sm btn-lux-primary text-white w-100">Forcer le statut</button>
                    </div>
                </form>
                <form action="{{ route('admin.clients.privilege-club.recalculate', $client) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="unlock" value="1">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Recalculer automatiquement</button>
                </form>

                {{-- Checklist WhatsApp manuelle (CDC §3.1) --}}
                <div class="border-top mt-4 pt-4" style="border-color: rgba(0,0,0,0.08) !important;">
                    <h4 class="h6 text-lux-dark-blue mb-2">
                        <i class="fa-brands fa-whatsapp text-success me-2"></i>Messages WhatsApp
                    </h4>
                    <p class="small text-lux-greyBlue mb-3">
                        Lors d'un changement de palier, envoyez le message de félicitations depuis WhatsApp
                        @if($client->phone)
                            (<a href="https://wa.me/{{ preg_replace('/\D+/', '', $client->phone) }}" target="_blank" rel="noopener" class="text-lux-gold">{{ $client->phone }}</a>)
                        @else
                            <span class="text-warning">— aucun téléphone renseigné sur la fiche</span>
                        @endif
                        puis cochez ci-dessous pour tracer l'envoi.
                    </p>

                    @if($pendingWhatsappNotifications->isNotEmpty())
                        <div class="d-flex flex-column gap-3">
                            @foreach($pendingWhatsappNotifications as $notification)
                                <div class="rounded border p-3 d-flex flex-wrap justify-content-between align-items-center gap-3" style="border-color: rgba(203, 174, 130, 0.4) !important; background-color: rgba(203, 174, 130, 0.06);">
                                    <div>
                                        <span class="badge bg-warning text-dark mb-2">WhatsApp en attente</span>
                                        <p class="small mb-1 text-lux-dark-blue">{{ $notification->message }}</p>
                                        <p class="small text-lux-greyBlue mb-0">
                                            {{ $notification->created_at->format('d/m/Y à H:i') }}
                                            @if($notification->old_tier)
                                                — {{ $clubService->tierLabel($notification->old_tier) }}
                                            @else
                                                — Non membre
                                            @endif
                                            → <strong>{{ $clubService->tierLabel($notification->new_tier) }}</strong>
                                        </p>
                                    </div>
                                    <form action="{{ route('admin.clients.privilege-club.whatsapp-sent', [$client, $notification]) }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success text-white">
                                            <i class="fa-brands fa-whatsapp me-1"></i> Marquer WhatsApp envoyé
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="small text-lux-greyBlue mb-0">
                            <i class="fa-solid fa-circle-check text-success me-1"></i> Aucun message WhatsApp en attente pour ce client.
                        </p>
                    @endif

                    @if($recentWhatsappSentNotifications->isNotEmpty())
                        <p class="small text-uppercase fw-medium text-lux-greyBlue mt-4 mb-2" style="font-size: 0.7rem;">Historique récent</p>
                        <ul class="list-unstyled small text-lux-greyBlue mb-0">
                            @foreach($recentWhatsappSentNotifications as $sent)
                                <li class="mb-1">
                                    <i class="fa-solid fa-check text-success me-1"></i>
                                    Envoyé le {{ $sent->whatsapp_sent_at->format('d/m/Y à H:i') }}
                                    — {{ $clubService->tierLabel($sent->new_tier) }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>

            <!-- Dossier documents (§3.10 CDC) -->
            <section class="bg-white rounded shadow-sm border p-4 mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
                <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                    <i class="fa-regular fa-folder-open me-2 text-lux-gold"></i>Dossier documents ({{ $client->clientDocuments->count() }})
                </h3>

                <form action="{{ route('admin.clients.documents.store', $client) }}" method="POST" enctype="multipart/form-data" class="border rounded p-3 mb-4" style="border-color: rgba(0,0,0,0.08) !important; background-color: rgba(248, 248, 246, 0.5);">
                    @csrf
                    <p class="small text-lux-greyBlue mb-3 mb-md-2">PDF ou Word (.docx), 15 Mo maximum par fichier.</p>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="doc_title" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Nom du document</label>
                            <input type="text" name="title" id="doc_title" class="form-control form-control-sm" placeholder="Ex. Contrat séjour juillet 2025" required maxlength="255">
                        </div>
                        <div class="col-md-5">
                            <label for="doc_file" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Fichier</label>
                            <input type="file" name="file" id="doc_file" class="form-control form-control-sm" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm w-100 text-white" style="background-color: var(--lux-dark-blue);">
                                <i class="fa-solid fa-upload me-1"></i>Téléverser
                            </button>
                        </div>
                    </div>
                </form>

                @forelse($client->clientDocuments as $doc)
                    <div class="border-bottom py-3 {{ $loop->last ? 'border-0 pb-0' : '' }}" style="border-color: rgba(0,0,0,0.05) !important;">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
                            <div>
                                <p class="text-lux-dark-blue fw-semibold mb-1">{{ $doc->title }}</p>
                                <p class="small text-lux-greyBlue mb-0">
                                    <i class="fa-regular fa-file me-1"></i>{{ $doc->file_name }}
                                    · {{ $doc->formatted_file_size }}
                                    · {{ $doc->created_at->format('d/m/Y à H:i') }}
                                    @if($doc->uploader)
                                        · par {{ $doc->uploader->first_name }} {{ $doc->uploader->last_name }}
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.clients.documents.download', [$client, $doc]) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#replace-doc-{{ $doc->id }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('admin.clients.documents.destroy', [$client, $doc]) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce document ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="collapse mt-3" id="replace-doc-{{ $doc->id }}">
                            <form action="{{ route('admin.clients.documents.update', [$client, $doc]) }}" method="POST" enctype="multipart/form-data" class="border rounded p-3" style="border-color: rgba(0,0,0,0.08) !important;">
                                @csrf
                                @method('PUT')
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="title" class="form-control form-control-sm" value="{{ $doc->title }}" required maxlength="255">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="file" name="file" class="form-control form-control-sm" accept=".pdf,.docx">
                                        <span class="small text-lux-greyBlue">Laisser vide pour conserver le fichier actuel</span>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-warning w-100">Remplacer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-lux-greyBlue text-center py-3 mb-0 small">Aucun document dans le dossier</p>
                @endforelse
            </section>

            <!-- Réservations -->
            <section class="bg-white rounded shadow-sm border p-4" style="border-color: rgba(0,0,0,0.05) !important;">
                <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                    <i class="fa-regular fa-calendar-check me-2 text-lux-gold"></i>Réservations ({{ $client->reservations->count() }})
                </h3>
                @forelse($client->reservations as $reservation)
                    <div class="border-bottom pb-3 mb-3" style="border-color: rgba(0,0,0,0.05) !important;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="text-lux-dark-blue fw-semibold mb-1">
                                    {{ $reservation->villa->name ?? 'Villa supprimée' }}
                                    @if($reservation->villa && $reservation->villa->island)
                                        <span class="text-lux-greyBlue small">- {{ $reservation->villa->island->name }}</span>
                                    @endif
                                </h6>
                                <p class="small text-lux-greyBlue mb-0">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    {{ $reservation->check_in_date->format('d/m/Y') }} - {{ $reservation->check_out_date->format('d/m/Y') }}
                                    ({{ $reservation->number_of_nights }} nuit{{ $reservation->number_of_nights > 1 ? 's' : '' }})
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge 
                                    @if($reservation->status == 'confirmed') bg-success bg-opacity-10 text-success
                                    @elseif($reservation->status == 'pending') bg-warning bg-opacity-10 text-warning
                                    @elseif($reservation->status == 'cancelled') bg-danger bg-opacity-10 text-danger
                                    @elseif($reservation->status == 'fully_paid') bg-primary bg-opacity-10 text-primary
                                    @else bg-secondary bg-opacity-10 text-secondary
                                    @endif px-2 py-1 small fw-medium">
                                    {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                </span>
                                <p class="small text-lux-dark-blue fw-medium mb-0 mt-1">
                                    {{ number_format($reservation->total_price, 0, ',', ' ') }} €
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="small text-lux-greyBlue mb-0">
                                <i class="fa-solid fa-users me-1"></i>{{ $reservation->number_of_guests }} personne{{ $reservation->number_of_guests > 1 ? 's' : '' }}
                            </p>
                            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-sm btn-outline-lux-gold">
                                <i class="fa-solid fa-eye me-1"></i>Voir détails
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-lux-greyBlue text-center py-4 mb-0">Aucune réservation</p>
                @endforelse
            </section>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Statistiques -->
            <section class="bg-white rounded shadow-sm border p-4 mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
                <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                    <i class="fa-solid fa-chart-pie me-2 text-lux-gold"></i>Statistiques
                </h3>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-lux-greyBlue small">Total réservations</span>
                        <span class="text-lux-dark-blue fw-semibold">{{ $stats['total_reservations'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-lux-greyBlue small">Réservations à venir</span>
                        <span class="text-lux-dark-blue fw-semibold">{{ $stats['upcoming_reservations'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-lux-greyBlue small">Total dépensé</span>
                        <span class="text-lux-dark-blue fw-semibold">{{ number_format($stats['total_spent'], 0, ',', ' ') }} €</span>
                    </div>
                    @if($stats['last_reservation'])
                    <div class="pt-3 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
                        <p class="small text-lux-greyBlue mb-1">Dernière réservation</p>
                        <p class="text-lux-dark-blue fw-medium mb-0 small">
                            {{ $stats['last_reservation']->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Avatar -->
            <section class="bg-white rounded shadow-sm border p-4 text-center" style="border-color: rgba(0,0,0,0.05) !important;">
                <div class="d-inline-block mb-3">
                    <div class="rounded-circle overflow-hidden position-relative mx-auto" style="width: 120px; height: 120px; background-color: var(--lux-gold); display: flex; align-items-center; justify-content: center; color: white; font-weight: 600; font-size: 3rem;">
                        @if($client->photo_url)
                            <img src="{{ asset('storage/' . $client->photo_url) }}" alt="{{ $client->first_name }} {{ $client->last_name }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <span>{{ strtoupper(substr($client->first_name ?? '', 0, 1) . substr($client->last_name ?? '', 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
                <p class="text-lux-dark-blue fw-semibold mb-1">{{ $client->first_name }} {{ $client->last_name }}</p>
                <p class="small text-lux-greyBlue mb-0">{{ $client->email }}</p>
                @if(!$client->is_active)
                    <span class="badge bg-secondary bg-opacity-10 text-secondary mt-2">Compte inactif</span>
                @elseif($client->must_set_password)
                    <span class="badge bg-warning bg-opacity-10 text-warning mt-2">Invitation envoyée</span>
                @else
                    <span class="badge bg-success bg-opacity-10 text-success mt-2">Compte actif</span>
                @endif
            </section>
        </div>
    </div>
@endsection




