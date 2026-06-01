<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\VillaReviewService;
use App\Models\Document;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EspaceClientController extends Controller
{
    /**
     * Afficher le tableau de bord de l'espace client
     */
    public function index(Reservation $reservation = null)
    {
        $user = Auth::user();
        
        // Si une réservation spécifique est demandée
        if ($reservation && $reservation->user_id === $user->id) {
            $nextReservation = $reservation->load(['villa.photos', 'villa.island', 'payments']);
        } else {
            // Sinon, chercher la prochaine réservation (comportement par défaut)
            $nextReservation = Reservation::with(['villa.photos', 'villa.island', 'payments'])
                ->where('user_id', $user->id)
                ->whereIn('status', ['confirmed', 'deposit_paid', 'fully_paid'])
                ->where('check_out_date', '>=', now()->toDateString())
                ->orderBy('check_in_date', 'asc')
                ->first();
        }
        
        // Toutes les réservations
        $reservations = Reservation::with(['villa.photos', 'villa.island', 'payments'])
            ->where('user_id', $user->id)
            ->orderBy('check_in_date', 'desc')
            ->limit(10)
            ->get();
        
        // Documents récents
        $recentDocuments = Document::whereHas('reservation', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        $recentClientDocuments = \App\Models\ClientDocument::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        // Compter les nouveaux documents (créés dans les 7 derniers jours)
        $newDocumentsCount = Document::whereHas('reservation', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('created_at', '>=', now()->subDays(7))
        ->count()
        + \App\Models\ClientDocument::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // Messages non lus
        $unreadMessagesCount = Message::where('recipient_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        // Messages récents
        $recentMessages = Message::with(['sender', 'reservation'])
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calcul des paiements pour la prochaine réservation
        $paymentStatus = null;
        if ($nextReservation) {
            $totalPaid = $nextReservation->payments()
                ->where('status', 'completed')
                ->where('type', '!=', 'deposit_guarantee')
                ->sum('amount');
            
            $remainingAmount = $nextReservation->total_price - $totalPaid;
            $percentagePaid = $nextReservation->total_price > 0 
                ? ($totalPaid / $nextReservation->total_price) * 100 
                : 0;
            
            // Calculer la date d'échéance du solde selon les paramètres configurés
            $balanceDueDays = \App\Helpers\SettingsHelper::get('balance_due_days_before_checkin', 30);
            $balanceDueDate = $nextReservation->check_in_date->copy()->subDays($balanceDueDays);
            
            $guaranteePayment = $nextReservation->payments()
                ->where('type', 'deposit_guarantee')
                ->whereIn('status', ['pending', 'processing'])
                ->first();
            
            $paymentStatus = [
                'total_paid' => $totalPaid,
                'total_price' => $nextReservation->total_price,
                'remaining' => max(0, $remainingAmount),
                'percentage' => round($percentagePaid, 1),
                'balance_due_date' => $balanceDueDate,
                'guarantee_payment' => $guaranteePayment,
            ];
        }
        
        return view('pages.espace-client', compact(
            'nextReservation',
            'reservations',
            'recentDocuments',
            'recentClientDocuments',
            'newDocumentsCount',
            'unreadMessagesCount',
            'recentMessages',
            'paymentStatus'
        ));
    }

    /**
     * Afficher la liste des réservations
     */
    public function reservations()
    {
        $user = Auth::user();
        
        // Toutes les réservations de l'utilisateur avec leurs relations
        $reservations = Reservation::with(['villa.photos', 'villa.island', 'payments', 'promoCode', 'review'])
            ->where('user_id', $user->id)
            ->orderBy('check_in_date', 'desc')
            ->get();

        $reviewService = app(VillaReviewService::class);
        
        // Séparer par période et statut pour les filtres
        $upcomingReservations = $reservations->filter(function($reservation) {
            return $reservation->check_out_date >= now()->toDateString() 
                && !in_array($reservation->status, ['cancelled', 'completed']);
        });
        
        $pastReservations = $reservations->filter(function($reservation) {
            return $reservation->check_out_date < now()->toDateString() 
                || $reservation->status === 'completed';
        });
        
        $cancelledReservations = $reservations->filter(function($reservation) {
            return $reservation->status === 'cancelled';
        });
        
        return view('pages.reservations', compact(
            'reservations',
            'upcomingReservations',
            'pastReservations',
            'cancelledReservations',
            'reviewService'
        ));
    }

    /**
     * Afficher la messagerie
     */
    public function messages(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer l'ID de la conversation sélectionnée (depuis query param)
        $selectedConversationId = $request->query('conversation_id');
        
        // Récupérer la liste des administrateurs disponibles
        $admins = \App\Models\User::where('is_admin', true)
            ->where('is_active', true)
            ->get();
        
        // Récupérer la liste de TOUS les autres clients (pas seulement ceux avec qui on a déjà communiqué)
        // Pour permettre de démarrer une conversation avec n'importe quel client
        $otherClients = \App\Models\User::where('is_admin', false)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        
        // Utilisateur par défaut pour la sidebar (premier admin disponible)
        $defaultUser = $admins->first();
        
        // Récupérer toutes les conversations (groupées par réservation ou par utilisateur)
        $conversations = Message::with(['sender', 'recipient', 'reservation.villa', 'reservation.villa.island'])
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) use ($user) {
                // Grouper par réservation si disponible, sinon par l'autre utilisateur
                if ($message->reservation_id) {
                    return 'reservation_' . $message->reservation_id;
                } else {
                    $otherUserId = $message->sender_id == $user->id ? $message->recipient_id : $message->sender_id;
                    return 'user_' . $otherUserId;
                }
            })
            ->map(function($messages) use ($user) {
                $lastMessage = $messages->first();
                $unreadCount = $messages->where('recipient_id', $user->id)->where('is_read', false)->count();
                
                // Déterminer l'autre utilisateur avec qui on converse
                $otherUser = $lastMessage->sender_id == $user->id ? $lastMessage->recipient : $lastMessage->sender;
                
                // Créer l'ID de conversation : par réservation si disponible, sinon par utilisateur
                $conversationId = $lastMessage->reservation_id 
                    ? 'reservation_' . $lastMessage->reservation_id 
                    : 'user_' . ($lastMessage->sender_id == $user->id ? $lastMessage->recipient_id : $lastMessage->sender_id);
                
                return [
                    'id' => $conversationId,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'reservation' => $lastMessage->reservation,
                    'other_user' => $otherUser, // Peut être un client ou un admin
                ];
            })
            ->sortByDesc(function($conversation) {
                return $conversation['last_message']->created_at;
            })
            ->values();
        
        // Récupérer les messages de la conversation sélectionnée
        $selectedMessages = collect();
        $selectedConversation = null;
        $activeReservation = null;
        
        if ($selectedConversationId) {
            // Extraire le type et l'ID
            if (str_starts_with($selectedConversationId, 'reservation_')) {
                $reservationId = str_replace('reservation_', '', $selectedConversationId);
                $selectedMessages = Message::with(['sender', 'recipient', 'attachments'])
                    ->where('reservation_id', $reservationId)
                    ->where(function($query) use ($user) {
                        $query->where('sender_id', $user->id)
                              ->orWhere('recipient_id', $user->id);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $activeReservation = Reservation::with(['villa.photos', 'villa.island'])
                    ->where('id', $reservationId)
                    ->where('user_id', $user->id)
                    ->first();
                
                // Déterminer l'autre utilisateur avec qui on converse
                $otherUser = null;
                if ($selectedMessages->count() > 0) {
                    // Prendre le premier message pour déterminer l'autre utilisateur
                    $firstMessage = $selectedMessages->first();
                    if ($firstMessage->sender_id == $user->id) {
                        $otherUser = $firstMessage->recipient;
                    } else {
                        $otherUser = $firstMessage->sender;
                    }
                } else {
                    // Si pas de messages, chercher un admin par défaut pour les conversations de réservation
                    $otherUser = \App\Models\User::where('is_admin', true)->first();
                }
                
                if ($activeReservation) {
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => $activeReservation,
                        'other_user' => $otherUser,
                    ];
                } else {
                    // Même si la réservation n'existe pas, créer la conversation pour afficher la sidebar
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => null,
                        'other_user' => $otherUser,
                    ];
                }
            } elseif (str_starts_with($selectedConversationId, 'user_')) {
                $otherUserId = str_replace('user_', '', $selectedConversationId);
                $selectedMessages = Message::with(['sender', 'recipient', 'attachments'])
                    ->where(function($query) use ($user, $otherUserId) {
                        $query->where(function($q) use ($user, $otherUserId) {
                            $q->where('sender_id', $user->id)
                              ->where('recipient_id', $otherUserId);
                        })->orWhere(function($q) use ($user, $otherUserId) {
                            $q->where('sender_id', $otherUserId)
                              ->where('recipient_id', $user->id);
                        });
                    })
                    ->whereNull('reservation_id')
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $otherUser = \App\Models\User::find($otherUserId);
                if ($otherUser) {
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => null,
                        'other_user' => $otherUser,
                    ];
                } else {
                    // Si l'utilisateur n'existe pas, créer quand même la conversation pour afficher la sidebar
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => null,
                        'other_user' => null,
                    ];
                }
            }
            
            // Marquer les messages comme lus seulement s'il y a des messages
            if ($selectedMessages->count() > 0) {
                Message::where('recipient_id', $user->id)
                    ->where('is_read', false)
                    ->whereIn('id', $selectedMessages->pluck('id'))
                    ->update(['is_read' => true, 'read_at' => now()]);
            }
        }
        
        // Si aucune conversation n'est sélectionnée, sélectionner la première conversation de la liste ou créer une conversation par défaut
        if (!$selectedConversation) {
            // Si on a des conversations existantes, prendre la première
            if ($conversations->count() > 0) {
                $firstConversation = $conversations->first();
                $selectedConversationId = $firstConversation['id'];
                
                // Charger les messages de cette première conversation
                if (str_starts_with($selectedConversationId, 'reservation_')) {
                    $reservationId = str_replace('reservation_', '', $selectedConversationId);
                    $selectedMessages = Message::with(['sender', 'recipient', 'attachments'])
                        ->where('reservation_id', $reservationId)
                        ->where(function($query) use ($user) {
                            $query->where('sender_id', $user->id)
                                  ->orWhere('recipient_id', $user->id);
                        })
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    $activeReservation = Reservation::with(['villa.photos', 'villa.island'])
                        ->where('id', $reservationId)
                        ->where('user_id', $user->id)
                        ->first();
                    
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => $activeReservation,
                        'other_user' => $firstConversation['other_user'],
                    ];
                } elseif (str_starts_with($selectedConversationId, 'user_')) {
                    $otherUserId = str_replace('user_', '', $selectedConversationId);
                    $selectedMessages = Message::with(['sender', 'recipient', 'attachments'])
                        ->where(function($query) use ($user, $otherUserId) {
                            $query->where(function($q) use ($user, $otherUserId) {
                                $q->where('sender_id', $user->id)
                                  ->where('recipient_id', $otherUserId);
                            })->orWhere(function($q) use ($user, $otherUserId) {
                                $q->where('sender_id', $otherUserId)
                                  ->where('recipient_id', $user->id);
                            });
                        })
                        ->whereNull('reservation_id')
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => null,
                        'other_user' => $firstConversation['other_user'],
                    ];
                }
                
                // Marquer les messages comme lus
                if ($selectedMessages->count() > 0) {
                    Message::where('recipient_id', $user->id)
                        ->where('is_read', false)
                        ->whereIn('id', $selectedMessages->pluck('id'))
                        ->update(['is_read' => true, 'read_at' => now()]);
                }
            } elseif ($defaultUser) {
                // Si aucune conversation n'existe, créer une conversation par défaut avec le premier admin
                $selectedConversation = [
                    'id' => 'user_' . $defaultUser->id,
                    'reservation' => null,
                    'other_user' => $defaultUser,
                ];
                // Charger les messages de cette conversation par défaut (s'il y en a)
                $selectedMessages = Message::with(['sender', 'recipient', 'attachments'])
                    ->where(function($query) use ($user, $defaultUser) {
                        $query->where(function($q) use ($user, $defaultUser) {
                            $q->where('sender_id', $user->id)
                              ->where('recipient_id', $defaultUser->id);
                        })->orWhere(function($q) use ($user, $defaultUser) {
                            $q->where('sender_id', $defaultUser->id)
                              ->where('recipient_id', $user->id);
                        });
                    })
                    ->whereNull('reservation_id')
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                // Marquer les messages comme lus
                if ($selectedMessages->count() > 0) {
                    Message::where('recipient_id', $user->id)
                        ->where('is_read', false)
                        ->whereIn('id', $selectedMessages->pluck('id'))
                        ->update(['is_read' => true, 'read_at' => now()]);
                }
            }
        }
        
        return view('pages.messages', compact(
            'conversations',
            'selectedMessages',
            'selectedConversation',
            'activeReservation',
            'admins',
            'otherClients',
            'defaultUser'
        ));
    }

    /**
     * Envoyer un message
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'body' => 'nullable|string|max:5000',
            'reservation_id' => 'nullable|exists:reservations,id',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx',
            'image' => 'nullable|image|max:10240|mimes:jpg,jpeg,png,gif',
        ]);
        
        // Le body ou un fichier doit être présent
        if (empty($validated['body']) && !$request->hasFile('attachment') && !$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'Le message ou une pièce jointe est requis'
            ], 422);
        }
        
        // Vérifier que l'utilisateur peut envoyer un message à ce destinataire
        $recipient = \App\Models\User::find($validated['recipient_id']);
        if (!$recipient) {
            return response()->json([
                'success' => false,
                'message' => 'Destinataire introuvable'
            ], 404);
        }
        
        // Vérifier que la réservation appartient à l'utilisateur si elle est fournie
        if (isset($validated['reservation_id']) && $validated['reservation_id']) {
            $reservation = \App\Models\Reservation::find($validated['reservation_id']);
            if (!$reservation || $reservation->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non autorisée'
                ], 403);
            }
        }
        
        // Créer le message
        $message = \App\Models\Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $validated['recipient_id'],
            'reservation_id' => $validated['reservation_id'] ?? null,
            'body' => $validated['body'] ?? '',
            'is_read' => false,
            'is_admin_message' => $user->is_admin,
        ]);
        
        // Gérer les pièces jointes
        $file = $request->file('attachment') ?? $request->file('image');
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('message_attachments', $fileName, 'public');
            
            \App\Models\MessageAttachment::create([
                'message_id' => $message->id,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->isValid() && str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'document',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
        
        // Charger les relations pour la réponse
        $message->load(['sender', 'recipient', 'attachments']);
        
        // Diffuser l'événement MessageSent en temps réel
        try {
            event(new \App\Events\MessageSent($message));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la diffusion du message: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'created_at' => $message->created_at->format('H:i'),
                'created_at_full' => $message->created_at->toIso8601String(),
                'attachments' => $message->attachments->map(function($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_path' => $attachment->file_path,
                        'file_name' => $attachment->file_name,
                        'file_type' => $attachment->file_type,
                        'mime_type' => $attachment->mime_type,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Afficher la liste des documents
     */
    public function documents(Request $request)
    {
        $user = Auth::user();

        $clientDocumentsQuery = \App\Models\ClientDocument::where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $clientDocumentsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('period')) {
            switch ($request->period) {
                case '30days':
                    $clientDocumentsQuery->where('created_at', '>=', now()->subDays(30));
                    break;
                case 'year':
                    $year = $request->input('year', now()->year);
                    $clientDocumentsQuery->whereYear('created_at', $year);
                    break;
            }
        }

        $clientDocuments = $clientDocumentsQuery->orderByDesc('created_at')->get();
        $recentClientDocuments = \App\Models\ClientDocument::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
        
        // Récupérer tous les documents de l'utilisateur avec leurs relations
        $query = Document::with(['reservation.villa.island', 'reservation.villa.photos', 'signer'])
            ->whereHas('reservation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        
        // Filtre par type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filtre par période
        if ($request->has('period') && $request->period) {
            switch ($request->period) {
                case '30days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case 'year':
                    $year = $request->has('year') ? $request->year : now()->year;
                    $query->whereYear('created_at', $year);
                    break;
            }
        }
        
        // Recherche par nom de document ou numéro
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%");
            });
        }
        
        // Trier par date de création (plus récent en premier)
        $query->orderBy('created_at', 'desc');
        
        // Pagination
        $documents = $query->paginate(15)->withQueryString();
        
        // Documents récents (pour la vue cartes)
        $recentDocuments = Document::with(['reservation.villa.island', 'reservation.villa.photos'])
            ->whereHas('reservation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
        
        // Statistiques
        $totalDocuments = Document::whereHas('reservation', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count() + $clientDocuments->count();
        
        $documentsByType = Document::whereHas('reservation', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->selectRaw('type, COUNT(*) as count')
        ->groupBy('type')
        ->pluck('count', 'type')
        ->toArray();
        
        // Années disponibles pour le filtre
        $availableYears = Document::whereHas('reservation', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->toArray();
        
        // Types de documents disponibles
        $availableTypes = [
            'contract' => 'Contrat',
            'invoice' => 'Facture',
            'deposit_receipt' => 'Reçu d\'arrhes',
            'balance_receipt' => 'Reçu de solde',
            'guarantee_receipt' => 'Reçu de caution',
            'receipt' => 'Reçu',
            'cancellation' => 'Annulation',
        ];
        
        return view('pages.documents', compact(
            'documents',
            'recentDocuments',
            'clientDocuments',
            'recentClientDocuments',
            'totalDocuments',
            'documentsByType',
            'availableYears',
            'availableTypes'
        ));
    }

    /**
     * Afficher la liste des paiements
     */
    public function payments(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer tous les paiements de l'utilisateur avec leurs relations
        $query = Payment::with(['reservation.villa.island', 'reservation.villa.photos'])
            ->whereHas('reservation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        
        // Filtre par période
        if ($request->filled('period')) {
            $now = \Carbon\Carbon::now();
            switch ($request->period) {
                case 'this_month':
                    $query->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                    break;
                case 'last_month':
                    $query->whereBetween('created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()]);
                    break;
                case 'this_year':
                    $query->whereBetween('created_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                    break;
                case 'last_30_days':
                    $query->where('created_at', '>=', $now->copy()->subDays(30));
                    break;
            }
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Recherche par numéro de paiement ou réservation
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhereHas('reservation', function($q) use ($search) {
                      $q->where('reservation_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Trier par date de création (plus récent en premier)
        $query->orderBy('created_at', 'desc');
        
        // Pagination
        $payments = $query->paginate(15)->withQueryString();
        
        // Statistiques
        $totalPaid = Payment::whereHas('reservation', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('status', 'completed')
        ->sum('amount');
        
        $pendingAmount = Payment::whereHas('reservation', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('status', 'pending')
        ->sum('amount');
        
        $pendingCount = Payment::whereHas('reservation', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('status', 'pending')
        ->count();
        
        // Types de paiements disponibles
        $availableTypes = [
            'deposit' => 'Arrhes',
            'balance' => 'Solde',
            'deposit_guarantee' => 'Garantie',
            'refund' => 'Remboursement',
            'adjustment' => 'Ajustement',
        ];
        
        return view('pages.payments', compact(
            'payments',
            'totalPaid',
            'pendingAmount',
            'pendingCount',
            'availableTypes'
        ));
    }

    /**
     * Afficher la page de paiement du solde
     */
    public function payBalance(Request $request, Reservation $reservation)
    {
        $user = Auth::user();
        
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== $user->id) {
            return redirect()->route('espace-client.index')->with('error', 'Vous n\'avez pas accès à cette réservation.');
        }
        
        $balancePayment = $reservation->payments()
            ->where('type', 'balance')
            ->whereIn('status', ['pending', 'processing'])
            ->first();
        
        if (!$balancePayment) {
            return redirect()->route('espace-client.reservations')->with('error', 'Aucun solde en attente de paiement pour cette réservation.');
        }
        
        // Vérifier que le paiement de l'acompte a été complété
        $depositPayment = $reservation->payments()
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->first();
        
        if (!$depositPayment) {
            return redirect()->route('espace-client.reservations')->with('error', 'Veuillez d\'abord payer l\'acompte.');
        }
        
        // Charger les relations nécessaires
        $reservation->load(['villa.island', 'villa.photos', 'payments']);
        $villa = $reservation->villa;
        
        // Récupérer la photo principale
        $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
            ?? $villa->photos->first();
        
        // Calculer les dates
        $checkIn = $reservation->check_in_date->format('Y-m-d');
        $checkOut = $reservation->check_out_date->format('Y-m-d');
        $nights = $reservation->number_of_nights;
        
        // Récupérer les informations depuis la réservation
        $total = $reservation->total_price;
        $balanceAmount = $balancePayment->amount;
        $totalPaid = $reservation->payments()
            ->where('status', 'completed')
            ->where('type', '!=', 'deposit_guarantee')
            ->sum('amount');
        
        // Récupérer la clé publique Stripe
        $stripePublicKey = \App\Helpers\SettingsHelper::get('stripe_public_key');
        return view('pages.pay-balance', compact(
            'reservation',
            'villa',
            'primaryPhoto',
            'balancePayment',
            'depositPayment',
            'checkIn',
            'checkOut',
            'nights',
            'total',
            'balanceAmount',
            'totalPaid',
            'stripePublicKey'
        ));
    }

    /**
     * Afficher la page de paiement de l'acompte (arrhes)
     */
    public function payDeposit(Request $request, Reservation $reservation)
    {
        $user = Auth::user();
        
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== $user->id) {
            return redirect()->route('espace-client.index')->with('error', 'Vous n\'avez pas accès à cette réservation.');
        }
        
        $depositPayment = $reservation->payments()
            ->where('type', 'deposit')
            ->whereIn('status', ['pending', 'processing'])
            ->first();
        
        if (!$depositPayment) {
            return redirect()->route('espace-client.reservations')->with('error', 'Aucun acompte en attente de paiement pour cette réservation.');
        }
        
        // Charger les relations nécessaires
        $reservation->load(['villa.island', 'villa.photos', 'payments', 'documents']);
        $villa = $reservation->villa;
        
        // Récupérer la photo principale
        $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
            ?? $villa->photos->first();
        
        // Calculer les dates
        $checkIn = $reservation->check_in_date->format('Y-m-d');
        $checkOut = $reservation->check_out_date->format('Y-m-d');
        $nights = $reservation->number_of_nights;
        
        // Récupérer les informations depuis la réservation
        $depositAmount = $depositPayment->amount;
        $total = $reservation->total_price;
        
        // Récupérer la clé publique Stripe
        $stripePublicKey = \App\Helpers\SettingsHelper::get('stripe_public_key');
        
        return view('pages.pay-deposit', compact(
            'reservation',
            'villa',
            'primaryPhoto',
            'depositPayment',
            'checkIn',
            'checkOut',
            'nights',
            'depositAmount',
            'total',
            'stripePublicKey'
        ));
    }

    /**
     * Afficher la page de paiement de la caution
     */
    public function payDepositGuarantee(Request $request, Reservation $reservation)
    {
        $user = Auth::user();
        
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== $user->id) {
            return redirect()->route('espace-client.index')->with('error', 'Vous n\'avez pas accès à cette réservation.');
        }
        
        $guaranteePayment = $reservation->payments()
            ->where('type', 'deposit_guarantee')
            ->whereIn('status', ['pending', 'processing'])
            ->first();
        
        if (!$guaranteePayment) {
            return redirect()->route('espace-client.reservations')->with('error', 'Aucune caution en attente de paiement pour cette réservation.');
        }
        
        // Charger les relations nécessaires
        $reservation->load(['villa.island', 'villa.photos', 'payments']);
        $villa = $reservation->villa;
        
        // Récupérer la photo principale
        $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
            ?? $villa->photos->first();
        
        // Calculer les dates
        $checkIn = $reservation->check_in_date->format('Y-m-d');
        $checkOut = $reservation->check_out_date->format('Y-m-d');
        $nights = $reservation->number_of_nights;
        
        // Récupérer les informations depuis la réservation
        $guaranteeAmount = $guaranteePayment->amount;
        $dueDate = $guaranteePayment->due_date;
        
        // Récupérer la clé publique Stripe
        $stripePublicKey = \App\Helpers\SettingsHelper::get('stripe_public_key');
        
        return view('pages.pay-deposit-guarantee', compact(
            'reservation',
            'villa',
            'primaryPhoto',
            'guaranteePayment',
            'checkIn',
            'checkOut',
            'nights',
            'guaranteeAmount',
            'dueDate',
            'stripePublicKey'
        ));
    }
}

