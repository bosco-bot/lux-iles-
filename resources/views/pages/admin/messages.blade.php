@extends('layouts.admin')

@section('title', 'Messagerie | LUXÎLES - Administration')

@push('styles')
<style>
    .messages-page-container {
        height: calc(100vh - 160px);
        display: flex;
        overflow: hidden;
        background-color: #F8F8F6;
        margin: -2rem;
        padding: 0;
    }
    .conversation-list {
        width: 100%;
        max-width: 384px;
        background-color: white;
        border-right: 1px solid rgba(138, 150, 166, 0.2);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow-y: auto;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE et Edge */
    }
    .conversation-list::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    @media (max-width: 767px) {
        .conversation-list {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 20;
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        .conversation-list.show {
            transform: translateX(0);
        }
    }
    .chat-window {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #F5F5F0;
        position: relative;
        height: 100%;
        overflow: hidden;
    }
    .messages-area {
        flex: 1;
        overflow-y: auto;
        background-image: radial-gradient(#CBAE82 0.5px, transparent 0.5px);
        background-size: 24px 24px;
        background-color: #F8F8F6;
    }
    .message-own::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: -6px;
        width: 12px;
        height: 12px;
        background: #CBAE82;
        clip-path: polygon(0 0, 0% 100%, 100% 100%);
    }
    .message-other::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -6px;
        width: 12px;
        height: 12px;
        background: #FFFFFF;
        clip-path: polygon(100% 0, 100% 100%, 0 100%);
    }
    .right-sidebar {
        width: 20rem;
        background-color: white;
        border-left: 1px solid rgba(138, 150, 166, 0.2);
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    @media (max-width: 1199px) {
        .right-sidebar {
            display: none !important;
        }
    }
    /* Scrollbar pour la zone de messages uniquement */
    .messages-area::-webkit-scrollbar { width: 6px; }
    .messages-area::-webkit-scrollbar-track { background: transparent; }
    .messages-area::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
    .messages-area::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    
    /* Masquer la scrollbar dans la liste des conversations */
    .conversation-list > div::-webkit-scrollbar {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="messages-page-container">
    
    <!-- Sidebar: Conversation List -->
    <aside id="conversation-list" class="conversation-list">
        <!-- Search & Filter -->
        <div class="p-4 border-bottom flex-shrink-0" style="border-color: rgba(138, 150, 166, 0.1) !important;">
            <h2 class="h3 font-serif text-lux-blue mb-4" style="font-family: 'Playfair Display', serif;">Messages</h2>
            <div class="position-relative mb-3">
                <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-lux-greyBlue" style="opacity: 0.6;"></i>
                <input type="text" placeholder="Rechercher une conversation..." class="form-control ps-5 py-2 rounded" style="border-color: transparent; background-color: var(--lux-beige);" id="search-conversations">
            </div>
            <div class="d-flex gap-2 overflow-x-auto pb-1" style="scrollbar-width: none;">
                <button class="btn btn-sm px-3 py-1 rounded-pill bg-lux-blue text-white border-0 small fw-medium" style="white-space: nowrap;" data-filter="all">Tous</button>
                <button class="btn btn-sm px-3 py-1 rounded-pill bg-lux-white text-lux-greyBlue border-0 small fw-medium" style="white-space: nowrap;" data-filter="unread">Non lus</button>
                <button class="btn btn-sm px-3 py-1 rounded-pill bg-lux-white text-lux-greyBlue border-0 small fw-medium" style="white-space: nowrap;" data-filter="archived">Archivés</button>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-grow-1 overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
            @if($conversations->count() == 0)
                <!-- Aucune conversation - Afficher les contacts disponibles -->
                <div class="p-4">
                    <h6 class="small text-uppercase fw-semibold text-lux-blue mb-3" style="letter-spacing: 0.1em; font-size: 0.75rem;">Commencer une conversation</h6>
                    
                    @if(isset($otherAdmins) && $otherAdmins->count() > 0)
                        <div class="mb-4">
                            <p class="small fw-medium text-lux-blue mb-2">Autres administrateurs</p>
                            @foreach($otherAdmins as $admin)
                                <a href="{{ route('admin.messages', ['conversation_id' => 'user_' . $admin->id]) }}" class="d-flex align-items-center gap-3 p-3 rounded text-decoration-none mb-2" style="transition: all 0.3s; background-color: var(--lux-beige);" onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.1)'" onmouseout="this.style.backgroundColor='var(--lux-beige)'">
                                    <div class="position-relative flex-shrink-0">
                                        @if($admin->photo_url)
                                            <img src="{{ asset('storage/' . $admin->photo_url) }}" class="rounded-circle" style="width: 2.5rem; height: 2.5rem; object-fit: cover;" alt="{{ $admin->first_name }}">
                                        @else
                                            <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; font-size: 0.75rem;">
                                                {{ strtoupper(substr($admin->first_name ?? '', 0, 1) . substr($admin->last_name ?? '', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="position-absolute bottom-0 end-0 rounded-circle bg-success border border-var(--lux-blue)" style="width: 0.625rem; height: 0.625rem;"></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small fw-semibold text-lux-blue mb-0">{{ $admin->first_name }} {{ $admin->last_name }}</h6>
                                        <p class="small text-lux-gold mb-0">Concierge Privé</p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-lux-greyBlue"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    @if(isset($clients) && $clients->count() > 0)
                        <div>
                            <p class="small fw-medium text-lux-blue mb-2">Clients</p>
                            @foreach($clients as $client)
                                <a href="{{ route('admin.messages', ['conversation_id' => 'user_' . $client->id]) }}" class="d-flex align-items-center gap-3 p-3 rounded text-decoration-none mb-2" style="transition: all 0.3s; background-color: var(--lux-beige);" onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.1)'" onmouseout="this.style.backgroundColor='var(--lux-beige)'">
                                    <div class="flex-shrink-0">
                                        @if($client->photo_url)
                                            <img src="{{ asset('storage/' . $client->photo_url) }}" class="rounded-circle" style="width: 2.5rem; height: 2.5rem; object-fit: cover;" alt="{{ $client->first_name }}">
                                        @else
                                            <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; font-size: 0.75rem;">
                                                {{ strtoupper(substr($client->first_name ?? '', 0, 1) . substr($client->last_name ?? '', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small fw-semibold text-lux-blue mb-0">{{ $client->first_name }} {{ $client->last_name }}</h6>
                                        <p class="small text-lux-greyBlue mb-0">Client</p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-lux-greyBlue"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    @if((!isset($admins) || $admins->count() == 0) && (!isset($otherClients) || $otherClients->count() == 0))
                        <div class="text-center py-4">
                            <i class="fa-regular fa-user fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                            <p class="small text-lux-gray mb-0">Aucun contact disponible</p>
                        </div>
                    @endif
                </div>
            @endif
            
            @forelse($conversations as $conversation)
                @php
                    $lastMessage = $conversation['last_message'];
                    $isActive = $selectedConversation && $selectedConversation['id'] === $conversation['id'];
                    $otherUser = $conversation['other_user'];
                    $reservation = $conversation['reservation'];
                @endphp
                <a href="{{ route('admin.messages', ['conversation_id' => $conversation['id']]) }}" 
                   class="d-block p-4 border-bottom text-decoration-none conversation-item {{ $isActive ? 'bg-lux-gold bg-opacity-5 border-start border-lux-gold' : '' }}" 
                   style="border-color: rgba(138, 150, 166, 0.1) !important; border-left-width: {{ $isActive ? '4px' : '0' }}; transition: background-color 0.3s;" 
                   onmouseover="if(!this.classList.contains('bg-lux-gold')) this.style.backgroundColor='var(--lux-beige)'" 
                   onmouseout="if(!this.classList.contains('bg-lux-gold')) this.style.backgroundColor='transparent'">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div class="position-relative flex-shrink-0">
                                @if($otherUser && $otherUser->photo_url)
                                    <img src="{{ asset('storage/' . $otherUser->photo_url) }}" class="rounded-circle" style="width: 2.5rem; height: 2.5rem; object-fit: cover;" alt="{{ $otherUser->first_name }}">
                                @else
                                    <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; font-size: 0.75rem;">
                                        {{ $otherUser ? strtoupper(substr($otherUser->first_name ?? '', 0, 1) . substr($otherUser->last_name ?? '', 0, 1)) : 'SYS' }}
                                    </div>
                                @endif
                                @if($otherUser && $otherUser->is_admin)
                                    <span class="position-absolute bottom-0 end-0 rounded-circle bg-success border border-var(--lux-blue)" style="width: 0.625rem; height: 0.625rem;"></span>
                                @endif
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="fw-semibold small text-lux-blue mb-0 text-truncate">
                                    @if($otherUser)
                                        {{ $otherUser->first_name }} {{ $otherUser->last_name }}
                                        @if($otherUser->is_admin)
                                            <span class="text-lux-gold"> - Conciergerie</span>
                                        @endif
                                    @else
                                        Support LUXÎLES
                                    @endif
                                </h3>
                                @if($reservation)
                                    <p class="small text-lux-gold fw-medium mb-0 text-truncate">{{ $reservation->villa->name }} • {{ $reservation->villa->island->name ?? '' }}</p>
                                @else
                                    <p class="small text-white mb-0">
                                        @if($otherUser && $otherUser->is_admin)
                                            Conciergerie
                                        @elseif($otherUser)
                                            Client
                                        @else
                                            Assistance
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                        <span class="small text-white flex-shrink-0 ms-2" style="white-space: nowrap;">{{ $lastMessage->short_time_ago }}</span>
                    </div>
                    <p class="small text-lux-blue mt-2 mb-0 text-truncate" style="font-weight: {{ $conversation['unread_count'] > 0 ? '500' : '400' }};">
                        {{ \Illuminate\Support\Str::limit($lastMessage->subject ?? $lastMessage->body, 60) }}
                    </p>
                    @if($conversation['unread_count'] > 0)
                        <span class="badge bg-lux-gold text-lux-blue rounded-pill mt-1 small">{{ $conversation['unread_count'] }}</span>
                    @endif
                </a>
            @empty
                <div class="p-5 text-center">
                    <i class="fa-regular fa-envelope fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                    <p class="text-lux-gray mb-0">Aucune conversation</p>
                </div>
            @endforelse
        </div>
    </aside>

    <!-- Chat Area -->
    <section class="chat-window">
        @if($selectedConversation)
            <!-- Mobile Toggle Button -->
            <button onclick="document.getElementById('conversation-list').classList.toggle('show')" class="d-md-none position-absolute top-0 start-0 m-4 z-3 rounded-circle bg-lux-blue shadow-md d-flex align-items-center justify-content-center text-white border-0" style="width: 2.5rem; height: 2.5rem; z-index: 30;">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            
            <!-- Chat Header -->
            <div class="h-auto bg-white border-bottom d-flex align-items-center justify-content-between px-4 px-md-6 py-3 flex-shrink-0 shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; min-height: 80px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative">
                        @if(isset($selectedConversation['other_user']) && $selectedConversation['other_user'] && $selectedConversation['other_user']->photo_url)
                            <img src="{{ asset('storage/' . $selectedConversation['other_user']->photo_url) }}" class="rounded-circle shadow-sm" style="width: 2.5rem; height: 2.5rem; object-fit: cover;" alt="{{ $selectedConversation['other_user']->first_name }}">
                        @else
                            <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 2.5rem; height: 2.5rem; font-size: 0.75rem;">
                                {{ isset($selectedConversation['other_user']) && $selectedConversation['other_user'] ? strtoupper(substr($selectedConversation['other_user']->first_name ?? '', 0, 1) . substr($selectedConversation['other_user']->last_name ?? '', 0, 1)) : 'SYS' }}
                            </div>
                        @endif
                        @if(isset($selectedConversation['other_user']) && $selectedConversation['other_user'] && $selectedConversation['other_user']->is_admin)
                            <span class="position-absolute bottom-0 end-0 rounded-circle bg-success border border-var(--lux-blue)" style="width: 0.75rem; height: 0.75rem;"></span>
                        @endif
                    </div>
                    <div>
                        <h2 class="font-serif h5 text-lux-blue fw-medium mb-0" style="font-family: 'Playfair Display', serif;">
                            @if(isset($selectedConversation['other_user']) && $selectedConversation['other_user'])
                                {{ $selectedConversation['other_user']->first_name }} {{ $selectedConversation['other_user']->last_name }}
                                @if($selectedConversation['other_user']->is_admin)
                                    <span class="text-lux-gold"> - Conciergerie</span>
                                @endif
                            @else
                                Support LUXÎLES
                            @endif
                        </h2>
                        <div class="d-flex align-items-center gap-2 small text-lux-greyBlue">
                            @if(isset($selectedConversation['other_user']) && $selectedConversation['other_user'] && $selectedConversation['other_user']->is_admin)
                                <span class="text-success fw-medium">En ligne</span>
                                <span class="rounded-circle bg-lux-blue" style="width: 0.25rem; height: 0.25rem; opacity: 0.4;"></span>
                                <span>Réponse habituelle &lt; 1h</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <a href="tel:+33766334198" class="btn btn-link text-lux-greyBlue p-2 rounded-circle border-0 text-decoration-none" style="width: 2.25rem; height: 2.25rem;" title="Appeler" onmouseover="this.style.backgroundColor='var(--lux-white)'; this.style.color='var(--lux-blue)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-greyBlue)'">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                    @if($activeReservation)
                        <button class="btn btn-link text-lux-greyBlue p-2 rounded-circle border-0" style="width: 2.25rem; height: 2.25rem;" title="Détails de la réservation" onmouseover="this.style.backgroundColor='var(--lux-white)'; this.style.color='var(--lux-blue)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-greyBlue)'">
                            <i class="fa-solid fa-circle-info"></i>
                        </button>
                    @endif
                    <div class="d-none d-lg-block h-100 border-start mx-2" style="width: 1px; border-color: rgba(138, 150, 166, 0.2) !important;"></div>
                    @if($activeReservation)
                        <div class="d-none d-lg-flex flex-column align-items-end">
                            <span class="small fw-bold text-lux-blue text-uppercase" style="letter-spacing: 0.1em;">{{ $activeReservation->villa->name }}</span>
                            <span class="small text-lux-gold">{{ $activeReservation->check_in_date->format('d') }} - {{ $activeReservation->check_out_date->format('d M') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="messages-area flex-grow-1 p-4 p-md-6" id="messages-container">
                @if($selectedMessages->count() > 0)
                    @php
                        $currentDate = null;
                    @endphp
                    @foreach($selectedMessages as $message)
                    @php
                        $messageDate = $message->created_at->format('Y-m-d');
                        $isFromUser = $message->sender_id === auth()->id();
                        $otherUser = $isFromUser ? $message->recipient : $message->sender;
                        
                        if ($currentDate !== $messageDate) {
                            $currentDate = $messageDate;
                            $dateLabel = $message->created_at->isToday() ? 'Aujourd\'hui' : ($message->created_at->isYesterday() ? 'Hier' : $message->created_at->format('d M Y'));
                        }
                    @endphp
                    
                    @if($currentDate === $messageDate && ($loop->first || $selectedMessages[$loop->index - 1]->created_at->format('Y-m-d') !== $messageDate))
                        <div class="d-flex justify-content-center mb-4">
                            <span class="bg-white bg-opacity-80 px-4 py-1 rounded-pill small text-lux-greyBlue shadow-sm border border-white">{{ $dateLabel }}</span>
                        </div>
                    @endif

                    <!-- Message -->
                    <div class="d-flex align-items-end gap-3 mb-4 {{ $isFromUser ? 'flex-row-reverse' : '' }}" style="max-width: 85%; {{ $isFromUser ? 'margin-left: auto;' : '' }}">
                        @if(!$isFromUser)
                            <div class="flex-shrink-0">
                                @if($otherUser && $otherUser->photo_url)
                                    <img src="{{ asset('storage/' . $otherUser->photo_url) }}" class="rounded-circle shadow-sm mb-1" style="width: 2rem; height: 2rem; object-fit: cover;" alt="Avatar">
                                @else
                                    <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center mb-1 shadow-sm" style="width: 2rem; height: 2rem; font-size: 0.625rem;">
                                        {{ $otherUser ? strtoupper(substr($otherUser->first_name ?? '', 0, 1) . substr($otherUser->last_name ?? '', 0, 1)) : 'SYS' }}
                                    </div>
                                @endif
                            </div>
                        @else
                            @if($loop->index > 0 && $selectedMessages[$loop->index - 1]->sender_id === auth()->id())
                                <div class="flex-shrink-0" style="width: 2rem;"></div>
                            @else
                                <div class="flex-shrink-0">
                                    @if(auth()->user()->photo_url)
                                        <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" class="rounded-circle shadow-sm mb-1" style="width: 2rem; height: 2rem; object-fit: cover;" alt="Me">
                                    @else
                                        <div class="rounded-circle bg-lux-gold text-lux-blue d-flex align-items-center justify-content-center mb-1 shadow-sm fw-bold" style="width: 2rem; height: 2rem; font-size: 0.625rem;">
                                            {{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1) . substr(auth()->user()->last_name ?? '', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                        
                        <div class="d-flex flex-column gap-1 {{ $isFromUser ? 'align-items-end' : '' }}">
                            <div class="p-3 rounded-3 shadow-sm position-relative {{ $isFromUser ? 'message-own' : 'message-other' }} border" style="background-color: {{ $isFromUser ? 'var(--lux-gold)' : 'white' }}; color: {{ $isFromUser ? 'white' : 'var(--lux-text)' }}; border-color: rgba(138, 150, 166, 0.05) !important; max-width: 100%; {{ $isFromUser ? 'border-bottom-right-radius: 0 !important;' : 'border-bottom-left-radius: 0 !important;' }}">
                                @if($message->attachments && $message->attachments->count() > 0)
                                    @foreach($message->attachments as $attachment)
                                        @if($attachment->file_type === 'image')
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="img-fluid rounded" style="max-width: 300px; max-height: 300px; cursor: pointer;" onclick="window.open('{{ asset('storage/' . $attachment->file_path) }}', '_blank')">
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-decoration-none d-inline-flex align-items-center gap-2 {{ $isFromUser ? 'text-white' : 'text-lux-text' }}">
                                                    <i class="fa-solid fa-file"></i> {{ $attachment->file_name }}
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                @if($message->body)
                                    <p class="mb-0" style="line-height: 1.75;">{{ nl2br(e($message->body)) }}</p>
                                @endif
                            </div>
                            <span class="small text-lux-greyBlue {{ $isFromUser ? 'text-end d-flex align-items-center gap-1' : '' }}" style="font-size: 0.625rem;">
                                {{ $message->created_at->format('H:i') }}
                                @if($isFromUser)
                                    <i class="fa-solid fa-check-double text-lux-gold"></i>
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <!-- Aucun message - Premier message -->
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fa-regular fa-comments fa-4x text-lux-greyBlue opacity-50 mb-4"></i>
                            <h4 class="h5 font-serif text-lux-blue mb-2">Aucun message</h4>
                            <p class="text-lux-gray mb-0">Écrivez votre premier message ci-dessous</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Input Area -->
            <div class="p-4 p-md-6 bg-white border-top flex-shrink-0" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                <form class="d-flex align-items-end gap-3" id="message-form" onsubmit="event.preventDefault(); sendMessage();">
                    <input type="file" id="attachment-input" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                    <button type="button" class="btn btn-link text-lux-greyBlue p-0 rounded-circle border-0 flex-shrink-0" style="width: 2.5rem; height: 2.5rem;" onclick="document.getElementById('attachment-input').click()" onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.1)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-greyBlue)'">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>
                    
                    <div class="flex-grow-1 bg-lux-white rounded-3 border border-transparent p-3" style="transition: all 0.3s;" id="input-container" onfocusin="this.style.borderColor='rgba(203, 174, 130, 0.3)'; this.style.backgroundColor='white'" onfocusout="this.style.borderColor='transparent'; this.style.backgroundColor='var(--lux-white)'">
                        <textarea rows="1" placeholder="Écrivez votre message..." class="form-control border-0 bg-transparent outline-0 text-lux-text resize-none" style="max-height: 8rem; min-height: 1.5rem;" id="message-input" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="border-color: rgba(0, 0, 0, 0.1) !important;">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-link text-lux-greyBlue p-0 border-0 small" id="emoji-btn" style="text-decoration: none; position: relative;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-greyBlue)'">
                                    <i class="fa-regular fa-face-smile"></i>
                                </button>
                                <input type="file" id="image-input" class="d-none" accept="image/*">
                                <button type="button" class="btn btn-link text-lux-greyBlue p-0 border-0 small" onclick="document.getElementById('image-input').click()" style="text-decoration: none;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-greyBlue)'">
                                    <i class="fa-regular fa-image"></i>
                                </button>
                            </div>
                            <span class="small text-lux-greyBlue" style="font-size: 0.625rem; opacity: 0.6;">Appuyez sur Entrée pour envoyer</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-lux-primary rounded-circle border-0 flex-shrink-0 shadow-lg" style="width: 3rem; height: 3rem;" onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 10px 15px rgba(203, 174, 130, 0.4)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 6px rgba(203, 174, 130, 0.2)'">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        @else
            <!-- Empty State -->
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <i class="fa-regular fa-comments fa-4x text-white opacity-50 mb-4"></i>
                    <h3 class="h4 font-serif text-lux-blue mb-2">Sélectionnez une conversation</h3>
                    <p class="text-lux-gray mb-0">Choisissez une conversation dans la liste pour commencer à échanger.</p>
                </div>
            </div>
        @endif
    </section>

    <!-- Right Sidebar: Details (Desktop Only) -->
    <aside class="right-sidebar d-none d-xl-flex">
        @if($selectedConversation)
            <div class="p-5 d-flex flex-column align-items-center border-bottom" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                <div class="rounded-circle p-1 border border-lux-gold mb-4 position-relative" style="width: 6rem; height: 6rem;">
                    @if(isset($selectedConversation['other_user']) && $selectedConversation['other_user'] && $selectedConversation['other_user']->photo_url)
                        <img src="{{ asset('storage/' . $selectedConversation['other_user']->photo_url) }}" class="w-100 h-100 rounded-circle object-fit-cover" alt="Profile">
                    @else
                        <div class="w-100 h-100 rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center">
                            {{ isset($selectedConversation['other_user']) && $selectedConversation['other_user'] ? strtoupper(substr($selectedConversation['other_user']->first_name ?? '', 0, 1) . substr($selectedConversation['other_user']->last_name ?? '', 0, 1)) : 'SYS' }}
                        </div>
                    @endif
                </div>
                <h3 class="font-serif h4 text-lux-blue mb-0" style="font-family: 'Playfair Display', serif;">
                    @if(isset($selectedConversation['other_user']) && $selectedConversation['other_user'])
                        {{ $selectedConversation['other_user']->first_name }} {{ $selectedConversation['other_user']->last_name }}
                    @else
                        Support LUXÎLES
                    @endif
                </h3>
                <p class="text-lux-gold small fw-medium text-uppercase mb-4" style="letter-spacing: 0.1em;">
                    @if(isset($selectedConversation['other_user']) && $selectedConversation['other_user'])
                        @if($selectedConversation['other_user']->is_admin)
                            Concierge Privé
                        @else
                            Client
                        @endif
                    @else
                        Assistance
                    @endif
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('contact.index') }}" class="btn btn-outline-dark rounded-circle border p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 2.5rem; height: 2.5rem; color: var(--lux-blue);" onmouseover="this.style.backgroundColor='var(--lux-blue)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-blue)'">
                        <i class="fa-regular fa-envelope"></i>
                    </a>
                    <a href="tel:+33766334198" class="btn btn-outline-dark rounded-circle border p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 2.5rem; height: 2.5rem; color: var(--lux-blue);" onmouseover="this.style.backgroundColor='var(--lux-blue)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-blue)'">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                </div>
            </div>
        @else
            <!-- État par défaut quand aucune conversation n'est sélectionnée -->
            <div class="p-5 d-flex flex-column align-items-center border-bottom" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                <div class="rounded-circle p-1 border border-lux-gold mb-4 position-relative" style="width: 6rem; height: 6rem;">
                    <div class="w-100 h-100 rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                </div>
                <h3 class="font-serif h4 text-lux-blue mb-0" style="font-family: 'Playfair Display', serif;">Support LUXÎLES</h3>
                <p class="text-lux-gold small fw-medium text-uppercase mb-4" style="letter-spacing: 0.1em;">Conciergerie</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('contact.index') }}" class="btn btn-outline-dark rounded-circle border p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 2.5rem; height: 2.5rem; color: var(--lux-blue);" onmouseover="this.style.backgroundColor='var(--lux-blue)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-blue)'">
                        <i class="fa-regular fa-envelope"></i>
                    </a>
                    <a href="tel:+33766334198" class="btn btn-outline-dark rounded-circle border p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 2.5rem; height: 2.5rem; color: var(--lux-blue);" onmouseover="this.style.backgroundColor='var(--lux-blue)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-blue)'">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                </div>
            </div>
        @endif

        @if($selectedConversation && $activeReservation)
            <div class="p-4 border-bottom" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                <h4 class="small text-uppercase fw-semibold text-lux-blue mb-4" style="letter-spacing: 0.1em; font-size: 0.75rem;">Réservation Active</h4>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-house-chimney text-lux-gold mt-1"></i>
                        <div>
                            <p class="small fw-medium text-white mb-0">{{ $activeReservation->villa->name }}</p>
                            <p class="small text-white mb-0">{{ $activeReservation->villa->island->name ?? '' }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa-regular fa-calendar text-lux-gold mt-1"></i>
                        <div>
                            <p class="small fw-medium text-white mb-0">{{ $activeReservation->check_in_date->format('d') }} - {{ $activeReservation->check_out_date->format('d M Y') }}</p>
                            <p class="small text-white mb-0">{{ $activeReservation->number_of_nights }} nuits</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-users text-lux-gold mt-1"></i>
                        <div>
                            <p class="small fw-medium text-white mb-0">{{ $activeReservation->number_of_guests }} personne{{ $activeReservation->number_of_guests > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.reservations.show', $activeReservation->id) }}" class="btn btn-outline-dark w-100 mt-4 py-2 border-lux-gold text-lux-gold small fw-medium text-decoration-none" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-gold)'; this.style.color='var(--lux-blue)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-gold)'">
                    Voir la réservation
                </a>
            </div>
        @endif

            <div class="p-4 border-bottom" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                <h4 class="small text-uppercase fw-semibold text-lux-blue mb-4" style="letter-spacing: 0.1em; font-size: 0.75rem;">Services Conciergerie</h4>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('contact.index', ['subject' => 'Chef à domicile']) }}" class="btn btn-link text-start p-3 rounded text-decoration-none d-flex align-items-center gap-3 w-100 border-0" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1)); transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-white)'; this.querySelector('.service-icon').style.backgroundColor='var(--lux-gold)'; this.querySelector('.service-icon i').style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.querySelector('.service-icon').style.backgroundColor='rgba(203, 174, 130, 0.1)'; this.querySelector('.service-icon i').style.color='var(--lux-gold)'">
                        <div class="service-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2rem; height: 2rem; background-color: rgba(203, 174, 130, 0.1); transition: all 0.3s;">
                            <i class="fa-solid fa-utensils" style="font-size: 0.875rem; color: #CBAE82 !important;"></i>
                        </div>
                        <span class="small fw-medium" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1));">Chef privé</span>
                    </a>
                    <a href="{{ route('contact.index', ['subject' => 'Transferts privés']) }}" class="btn btn-link text-start p-3 rounded text-decoration-none d-flex align-items-center gap-3 w-100 border-0" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1)); transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-white)'; this.querySelector('.service-icon').style.backgroundColor='var(--lux-gold)'; this.querySelector('.service-icon i').style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.querySelector('.service-icon').style.backgroundColor='rgba(203, 174, 130, 0.1)'; this.querySelector('.service-icon i').style.color='var(--lux-gold)'">
                        <div class="service-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2rem; height: 2rem; background-color: rgba(203, 174, 130, 0.1); transition: all 0.3s;">
                            <i class="fa-solid fa-car" style="font-size: 0.875rem; color: #CBAE82 !important;"></i>
                        </div>
                        <span class="small fw-medium" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1));">Transport VIP</span>
                    </a>
                    <a href="{{ route('contact.index', ['subject' => 'Conciergerie 24/7']) }}" class="btn btn-link text-start p-3 rounded text-decoration-none d-flex align-items-center gap-3 w-100 border-0" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1)); transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-white)'; this.querySelector('.service-icon').style.backgroundColor='var(--lux-gold)'; this.querySelector('.service-icon i').style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.querySelector('.service-icon').style.backgroundColor='rgba(203, 174, 130, 0.1)'; this.querySelector('.service-icon i').style.color='var(--lux-gold)'">
                        <div class="service-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2rem; height: 2rem; background-color: rgba(203, 174, 130, 0.1); transition: all 0.3s;">
                            <i class="fa-solid fa-spa" style="font-size: 0.875rem; color: #CBAE82 !important;"></i>
                        </div>
                        <span class="small fw-medium" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1));">Spa & Bien-être</span>
                    </a>
                    <a href="{{ route('contact.index', ['subject' => 'Activités exclusives']) }}" class="btn btn-link text-start p-3 rounded text-decoration-none d-flex align-items-center gap-3 w-100 border-0" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1)); transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-white)'; this.querySelector('.service-icon').style.backgroundColor='var(--lux-gold)'; this.querySelector('.service-icon i').style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.querySelector('.service-icon').style.backgroundColor='rgba(203, 174, 130, 0.1)'; this.querySelector('.service-icon i').style.color='var(--lux-gold)'">
                        <div class="service-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2rem; height: 2rem; background-color: rgba(203, 174, 130, 0.1); transition: all 0.3s;">
                            <i class="fa-solid fa-champagne-glasses" style="font-size: 0.875rem; color: #CBAE82 !important;"></i>
                        </div>
                        <span class="small fw-medium" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1));">Événements</span>
                    </a>
                </div>
            </div>

        <div class="p-4">
            <h4 class="small text-uppercase fw-semibold mb-4" style="letter-spacing: 0.1em; font-size: 0.75rem; color: rgb(10 26 47 / var(--tw-text-opacity, 1));">Fichiers Partagés</h4>
            @php
                $sharedFiles = ($selectedConversation && $activeReservation) ? $activeReservation->documents : collect();
            @endphp
            @if($sharedFiles->count() > 0)
                <div class="d-flex flex-column gap-2">
                    @foreach($sharedFiles as $file)
                        <div class="d-flex align-items-center gap-3 p-2 rounded cursor-pointer" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-white)'; this.querySelector('p').style.color='var(--lux-gold)'" onmouseout="this.style.backgroundColor='transparent'; this.querySelector('p').style.color='rgb(10 26 47 / var(--tw-text-opacity, 1))'">
                            <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0 {{ str_contains($file->file_path, '.pdf') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-primary bg-opacity-10 text-primary' }}" style="width: 2rem; height: 2rem;">
                                <i class="fa-solid fa-file-{{ str_contains($file->file_path, '.pdf') ? 'pdf' : 'lines' }} small"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="small fw-medium mb-0 text-truncate" style="transition: color 0.3s; color: rgb(10 26 47 / var(--tw-text-opacity, 1));">{{ basename($file->file_path) }}</p>
                                <span class="small" style="font-size: 0.625rem; color: rgb(10 26 47 / var(--tw-text-opacity, 1));">{{ $file->file_size ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="small mb-0" style="color: rgb(10 26 47 / var(--tw-text-opacity, 1));">Aucun fichier partagé</p>
            @endif
        </div>
    </aside>
    
    <!-- Modal pour démarrer une nouvelle conversation -->
    <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title font-serif" id="newConversationModalLabel" style="font-family: 'Playfair Display', serif;">Nouvelle conversation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if(isset($otherAdmins) && $otherAdmins->count() > 0)
                        <div class="p-3 border-bottom">
                            <h6 class="small text-uppercase fw-semibold text-lux-blue mb-3" style="letter-spacing: 0.1em; font-size: 0.75rem;">Autres administrateurs</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($otherAdmins as $admin)
                                    <a href="{{ route('admin.messages', ['conversation_id' => 'user_' . $admin->id]) }}" class="d-flex align-items-center gap-3 p-2 rounded text-decoration-none" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-beige)'" onmouseout="this.style.backgroundColor='transparent'">
                                        <div class="position-relative flex-shrink-0">
                                            @if($admin->photo_url)
                                                <img src="{{ asset('storage/' . $admin->photo_url) }}" class="rounded-circle" style="width: 2.5rem; height: 2.5rem; object-fit: cover;" alt="{{ $admin->first_name }}">
                                            @else
                                                <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($admin->first_name ?? '', 0, 1) . substr($admin->last_name ?? '', 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="position-absolute bottom-0 end-0 rounded-circle bg-success border border-var(--lux-blue)" style="width: 0.625rem; height: 0.625rem;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="small fw-semibold text-lux-blue mb-0">{{ $admin->first_name }} {{ $admin->last_name }}</h6>
                                            <p class="small text-lux-gold mb-0">Concierge Privé</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($clients) && $clients->count() > 0)
                        <div class="p-3">
                            <h6 class="small text-uppercase fw-semibold text-lux-blue mb-3" style="letter-spacing: 0.1em; font-size: 0.75rem;">Clients</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($clients as $client)
                                    <a href="{{ route('admin.messages', ['conversation_id' => 'user_' . $client->id]) }}" class="d-flex align-items-center gap-3 p-2 rounded text-decoration-none" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-beige)'" onmouseout="this.style.backgroundColor='transparent'">
                                        <div class="flex-shrink-0">
                                            @if($client->photo_url)
                                                <img src="{{ asset('storage/' . $client->photo_url) }}" class="rounded-circle" style="width: 2.5rem; height: 2.5rem; object-fit: cover;" alt="{{ $client->first_name }}">
                                            @else
                                                <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($client->first_name ?? '', 0, 1) . substr($client->last_name ?? '', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="small fw-semibold text-lux-blue mb-0">{{ $client->first_name }} {{ $client->last_name }}</h6>
                                            <p class="small text-white mb-0">Client</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if((!isset($admins) || $admins->count() == 0) && (!isset($otherClients) || $otherClients->count() == 0))
                        <div class="p-5 text-center">
                            <i class="fa-regular fa-user fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                            <p class="text-lux-gray mb-0">Aucun contact disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Variable globale pour stocker le fichier sélectionné
let selectedFile = null;
let selectedFileType = null; // 'image' ou 'attachment'

document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    window.sendMessage = function() {
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        
        // Vérifier qu'on a au moins un message ou un fichier
        if (!message && !selectedFile) {
            alert('Veuillez écrire un message ou sélectionner une pièce jointe');
            return;
        }
        
        // Récupérer les informations de la conversation
        const conversationId = '{{ $selectedConversation["id"] ?? "" }}';
        if (!conversationId) {
            alert('Veuillez sélectionner une conversation');
            return;
        }
        
        // Extraire le recipient_id depuis l'ID de conversation
        let recipientId = null;
        let reservationId = null;
        
        if (conversationId.startsWith('user_')) {
            recipientId = conversationId.replace('user_', '');
        } else if (conversationId.startsWith('reservation_')) {
            reservationId = conversationId.replace('reservation_', '');
            // Pour les conversations de réservation, on envoie au premier admin ou à l'autre utilisateur
            recipientId = '{{ isset($selectedConversation["other_user"]) && $selectedConversation["other_user"] ? $selectedConversation["other_user"]->id : "" }}';
        }
        
        if (!recipientId) {
            alert('Impossible de déterminer le destinataire');
            return;
        }
        
        // Désactiver le bouton et l'input pendant l'envoi
        const sendButton = document.querySelector('#message-form button[type="submit"]');
        const originalButtonHTML = sendButton.innerHTML;
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        input.disabled = true;
        
        // Préparer FormData pour l'envoi avec fichier
        const formData = new FormData();
        formData.append('recipient_id', recipientId);
        if (message) {
            formData.append('body', message);
        }
        if (reservationId) {
            formData.append('reservation_id', reservationId);
        }
        if (selectedFile) {
            if (selectedFileType === 'image') {
                formData.append('image', selectedFile);
            } else {
                formData.append('attachment', selectedFile);
            }
        }
        
        // Envoyer le message via AJAX
        fetch('{{ route("admin.messages.send") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    console.error('Erreur serveur:', data);
                    return Promise.reject(data);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Réponse serveur:', data);
            if (data.success) {
                // Vider l'input
                input.value = '';
                input.style.height = '';
                
                // Ajouter le message à l'affichage
                const messagesContainer = document.getElementById('messages-container');
                if (messagesContainer) {
                    // Vérifier s'il y a un message "Aucun message" à supprimer
                    const emptyState = messagesContainer.querySelector('.d-flex.align-items-center.justify-content-center.h-100');
                    if (emptyState) {
                        emptyState.remove();
                    }
                    
                    // Créer l'élément HTML du message
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'd-flex align-items-end gap-3 mb-4 flex-row-reverse';
                    messageDiv.style.cssText = 'max-width: 85%; margin-left: auto;';
                    
                    const messageTime = data.message.created_at || new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    const messageBody = message ? message.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>') : '';
                    
                    // Construire le HTML pour les pièces jointes
                    let attachmentsHTML = '';
                    if (data.message.attachments && data.message.attachments.length > 0) {
                        data.message.attachments.forEach(attachment => {
                            if (attachment.file_type === 'image') {
                                attachmentsHTML += `<div class="mb-2"><img src="/storage/${attachment.file_path}" alt="${attachment.file_name}" class="img-fluid rounded" style="max-width: 300px; max-height: 300px; cursor: pointer;" onclick="window.open('/storage/${attachment.file_path}', '_blank')"></div>`;
                            } else {
                                attachmentsHTML += `<div class="mb-2"><a href="/storage/${attachment.file_path}" target="_blank" class="text-white text-decoration-none d-inline-flex align-items-center gap-2"><i class="fa-solid fa-file"></i> ${attachment.file_name}</a></div>`;
                            }
                        });
                    }
                    
                    messageDiv.innerHTML = `
                        <div class="flex-shrink-0" style="width: 2rem;"></div>
                        <div class="d-flex flex-column gap-1 align-items-end">
                            <div class="p-3 rounded-3 shadow-sm position-relative message-own border" style="background-color: var(--lux-gold); color: var(--lux-blue); border-color: rgba(138, 150, 166, 0.05) !important; max-width: 100%; border-bottom-right-radius: 0 !important;">
                                ${attachmentsHTML}
                                ${messageBody ? `<p class="mb-0" style="line-height: 1.75;">${messageBody}</p>` : ''}
                            </div>
                            <span class="small text-white text-end d-flex align-items-center gap-1" style="font-size: 0.625rem;">
                                ${messageTime}
                                <i class="fa-solid fa-check-double text-lux-gold"></i>
                            </span>
                        </div>
                    `;
                    
                    messagesContainer.appendChild(messageDiv);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
                
                // Réinitialiser les variables de fichier
                selectedFile = null;
                selectedFileType = null;
                if (attachmentInput) attachmentInput.value = '';
                if (imageInput) imageInput.value = '';
                if (input) input.placeholder = 'Écrivez votre message...';
                
                // Recharger la page après 1.5 secondes pour mettre à jour la liste des conversations
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                alert(data.message || 'Erreur lors de l\'envoi du message');
            }
        })
        .catch(error => {
            console.error('Erreur complète:', error);
            const errorMessage = error.message || (error.errors ? JSON.stringify(error.errors) : 'Une erreur est survenue lors de l\'envoi du message');
            alert('Erreur: ' + errorMessage);
        })
        .finally(() => {
            // Réactiver le bouton et l'input
            sendButton.disabled = false;
            sendButton.innerHTML = originalButtonHTML;
            input.disabled = false;
            input.focus();
        });
    };
    
    const searchInput = document.getElementById('search-conversations');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.conversation-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => {
                b.classList.remove('bg-lux-blue', 'text-white');
                b.classList.add('bg-lux-beige', 'text-white');
            });
            this.classList.remove('bg-lux-beige', 'text-white');
            this.classList.add('bg-lux-blue', 'text-white');
        });
    });
    
    const messageInput = document.getElementById('message-input');
    if (messageInput) {
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    // Gestion de la pièce jointe
    const attachmentInput = document.getElementById('attachment-input');
    if (attachmentInput) {
        attachmentInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                selectedFile = file;
                selectedFileType = 'attachment';
                
                // Afficher un indicateur que le fichier est prêt
                const input = document.getElementById('message-input');
                if (input && !input.value.trim()) {
                    input.placeholder = 'Fichier sélectionné: ' + file.name + ' (optionnel: ajoutez un message)';
                }
            }
        });
    }
    
    // Gestion de l'image
    const imageInput = document.getElementById('image-input');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Vérifier que c'est bien une image
                if (!file.type.startsWith('image/')) {
                    alert('Veuillez sélectionner une image');
                    e.target.value = '';
                    return;
                }
                
                selectedFile = file;
                selectedFileType = 'image';
                
                // Afficher un aperçu ou un indicateur que l'image est prête
                const input = document.getElementById('message-input');
                if (input && !input.value.trim()) {
                    input.placeholder = 'Image sélectionnée: ' + file.name + ' (optionnel: ajoutez un message)';
                }
            }
        });
    }
    
    // Gestion des emojis
    const emojiBtn = document.getElementById('emoji-btn');
    if (emojiBtn) {
        emojiBtn.addEventListener('click', function() {
            // Liste d'emojis simples
            const emojis = ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥', '😶', '😐', '😑', '😬', '🙄', '😯', '😦', '😧', '😮', '😲', '🥱', '😴', '🤤', '😪', '😵', '🤐', '🥴', '🤢', '🤮', '🤧', '😷', '🤒', '🤕', '🤑', '🤠', '😈', '👿', '👹', '👺', '🤡', '💩', '👻', '💀', '☠️', '👽', '👾', '🤖', '🎃', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾'];
            
            // Créer un modal simple pour sélectionner un emoji
            const emojiModal = document.createElement('div');
            emojiModal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
            emojiModal.style.cssText = 'background-color: rgba(0,0,0,0.5); z-index: 9999;';
            emojiModal.onclick = function(e) {
                if (e.target === emojiModal) {
                    emojiModal.remove();
                }
            };
            
            const emojiContainer = document.createElement('div');
            emojiContainer.className = 'bg-lux-blue rounded p-4';
            emojiContainer.style.cssText = 'max-width: 400px; max-height: 400px; overflow-y: auto;';
            emojiContainer.onclick = function(e) {
                e.stopPropagation();
            };
            
            const emojiGrid = document.createElement('div');
            emojiGrid.className = 'd-flex flex-wrap gap-2';
            emojiGrid.style.cssText = 'gap: 0.5rem;';
            
            emojis.forEach(emoji => {
                const emojiBtn = document.createElement('button');
                emojiBtn.type = 'button';
                emojiBtn.className = 'btn btn-link p-2 border-0';
                emojiBtn.textContent = emoji;
                emojiBtn.style.cssText = 'font-size: 1.5rem; cursor: pointer;';
                emojiBtn.onclick = function() {
                    const input = document.getElementById('message-input');
                    if (input) {
                        input.value += emoji;
                        input.style.height = '';
                        input.style.height = input.scrollHeight + 'px';
                        input.focus();
                    }
                    emojiModal.remove();
                };
                emojiBtn.onmouseover = function() {
                    this.style.backgroundColor = 'var(--lux-beige)';
                };
                emojiBtn.onmouseout = function() {
                    this.style.backgroundColor = 'transparent';
                };
                emojiGrid.appendChild(emojiBtn);
            });
            
            emojiContainer.appendChild(emojiGrid);
            emojiModal.appendChild(emojiContainer);
            document.body.appendChild(emojiModal);
        });
    }

    // Écouter les messages en temps réel avec Laravel Echo
    if (typeof window.Echo !== 'undefined' && window.Echo) {
        const userId = {{ auth()->id() }};
        
        // Écouter les nouveaux messages
        window.Echo.private('user.' + userId)
            .listen('.message.sent', (data) => {
                if (data.message) {
                    const message = data.message;
                    const currentConversationId = '{{ $selectedConversation["id"] ?? "" }}';
                    
                    // Vérifier si le message appartient à la conversation actuelle
                    let messageBelongsToCurrentConversation = false;
                    
                    if (currentConversationId.startsWith('user_')) {
                        const otherUserId = currentConversationId.replace('user_', '');
                        messageBelongsToCurrentConversation = 
                            (message.sender_id == userId && message.recipient_id == otherUserId) ||
                            (message.sender_id == otherUserId && message.recipient_id == userId);
                    } else if (currentConversationId.startsWith('reservation_')) {
                        const reservationId = currentConversationId.replace('reservation_', '');
                        messageBelongsToCurrentConversation = message.reservation_id == reservationId;
                    }
                    
                    if (messageBelongsToCurrentConversation) {
                        // Ajouter le message à l'interface
                        addMessageToDOM(message);
                        
                        // Faire défiler vers le bas
                        const messagesContainer = document.getElementById('messages-container');
                        if (messagesContainer) {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
                    }
                    
                    // Recharger la liste des conversations pour mettre à jour le dernier message
                    // (vous pouvez optimiser cela en mettant à jour seulement l'élément de la liste)
                }
            });
    }

    // Fonction pour ajouter un message au DOM
    function addMessageToDOM(message) {
        const messagesContainer = document.getElementById('messages-container');
        if (!messagesContainer) return;
        
        const isFromUser = message.sender_id == {{ auth()->id() }};
        const messageDate = new Date(message.created_at_full).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
        const messageTime = new Date(message.created_at_full).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        
        const messageHTML = `
            <div class="d-flex align-items-end gap-3 mb-4 ${isFromUser ? 'flex-row-reverse' : ''}" style="max-width: 85%; ${isFromUser ? 'margin-left: auto;' : ''}">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-lux-blue text-white d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; font-size: 0.75rem;">
                        ${message.sender_name ? message.sender_name.substring(0, 2).toUpperCase() : 'U'}
                    </div>
                </div>
                <div class="d-flex flex-column gap-1 ${isFromUser ? 'align-items-end' : ''}">
                    <div class="p-3 rounded-3 shadow-sm position-relative ${isFromUser ? 'message-own' : 'message-other'} border" style="background-color: ${isFromUser ? 'var(--lux-gold)' : 'white'}; color: ${isFromUser ? 'white' : 'var(--lux-text)'}; border-color: rgba(138, 150, 166, 0.05) !important; max-width: 100%; ${isFromUser ? 'border-bottom-right-radius: 0 !important;' : 'border-bottom-left-radius: 0 !important;'}">
                        ${message.body ? '<p class="mb-0" style="line-height: 1.75;">' + message.body.replace(/\n/g, '<br>') + '</p>' : ''}
                    </div>
                    <span class="small text-lux-greyBlue ${isFromUser ? 'text-end d-flex align-items-center gap-1' : ''}" style="font-size: 0.625rem;">
                        ${messageTime}
                        ${isFromUser ? '<i class="fa-solid fa-check-double text-lux-gold"></i>' : ''}
                    </span>
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
    }
});
</script>
@endpush
