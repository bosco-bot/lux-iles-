<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Afficher la messagerie admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer l'ID de la conversation sélectionnée (depuis query param)
        $selectedConversationId = $request->query('conversation_id');
        
        // Récupérer la liste de TOUS les clients
        $clients = User::where('is_admin', false)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        
        // Récupérer la liste de TOUS les autres admins
        $otherAdmins = User::where('is_admin', true)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        
        // Utilisateur par défaut pour la sidebar (premier client disponible)
        $defaultUser = $clients->first();
        
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
                    'other_user' => $otherUser,
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
                
                $activeReservation = Reservation::with(['villa.photos', 'villa.island', 'user'])
                    ->where('id', $reservationId)
                    ->first();
                
                // Déterminer l'autre utilisateur avec qui on converse
                $otherUser = null;
                if ($selectedMessages->count() > 0) {
                    $firstMessage = $selectedMessages->first();
                    if ($firstMessage->sender_id == $user->id) {
                        $otherUser = $firstMessage->recipient;
                    } else {
                        $otherUser = $firstMessage->sender;
                    }
                } else {
                    // Si pas de messages, prendre le client de la réservation
                    $otherUser = $activeReservation ? $activeReservation->user : null;
                }
                
                if ($activeReservation) {
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => $activeReservation,
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
                
                $otherUser = User::find($otherUserId);
                if ($otherUser) {
                    $selectedConversation = [
                        'id' => $selectedConversationId,
                        'reservation' => null,
                        'other_user' => $otherUser,
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
                    
                    $activeReservation = Reservation::with(['villa.photos', 'villa.island', 'user'])
                        ->where('id', $reservationId)
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
                // Si aucune conversation n'existe, créer une conversation par défaut avec le premier client
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
        
        return view('pages.admin.messages', compact(
            'conversations',
            'selectedMessages',
            'selectedConversation',
            'activeReservation',
            'clients',
            'otherAdmins',
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
        
        // Vérifier que le destinataire existe
        $recipient = User::find($validated['recipient_id']);
        if (!$recipient) {
            return response()->json([
                'success' => false,
                'message' => 'Destinataire introuvable'
            ], 404);
        }
        
        // Vérifier que la réservation existe si elle est fournie
        if (isset($validated['reservation_id']) && $validated['reservation_id']) {
            $reservation = Reservation::find($validated['reservation_id']);
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation introuvable'
                ], 404);
            }
        }
        
        // Créer le message
        $message = Message::create([
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
        $message->load(['sender', 'recipient', 'reservation', 'attachments']);
        
        // Envoyer une notification au destinataire si c'est un admin ou si l'expéditeur n'est pas un admin
        try {
            if ($recipient && ($recipient->is_admin || !$user->is_admin)) {
                $recipient->notify(new \App\Notifications\MessageReceivedNotification($message));
            }
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas le processus
            \Log::error('Erreur lors de l\'envoi de la notification de message: ' . $e->getMessage());
        }
        
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
}



