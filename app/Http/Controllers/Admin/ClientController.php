<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendAccountInvitationEmailJob;
use App\Models\PromoCode;
use App\Models\PrivilegeClubNotification;
use App\Models\User;
use App\Services\EmailService;
use App\Services\PrivilegeClubService;
use App\Services\WhatsAppClickToChatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function __construct(
        protected WhatsAppClickToChatService $whatsAppService,
        protected EmailService $emailService
    ) {}
    /**
     * Afficher la liste des clients
     */
    public function index(Request $request)
    {
        try {
            $query = User::where('is_admin', false)
                ->withCount(['reservations', 'reservations as upcoming_reservations_count' => function ($q) {
                    $q->where('check_in_date', '>=', Carbon::now())
                        ->where('status', '!=', 'cancelled');
                }])
                ->with(['reservations' => function ($q) {
                    $q->orderBy('created_at', 'desc')->limit(1);
                }]);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true)->where('must_set_password', false);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($request->status === 'invitation') {
                    $query->where('must_set_password', true);
                }
            }

            if ($request->filled('reservations_filter')) {
                switch ($request->reservations_filter) {
                    case 'with_reservations':
                        $query->has('reservations');
                        break;
                    case 'without_reservations':
                        $query->doesntHave('reservations');
                        break;
                    case 'vip':
                        $query->has('reservations', '>=', 3);
                        break;
                }
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $clients = $query->orderBy($sortBy, $sortOrder)->paginate(15);

            return view('pages.admin.clients', compact('clients'));
        } catch (\Exception $e) {
            return view('pages.admin.clients', [
                'clients' => collect([]),
            ]);
        }
    }

    /**
     * Formulaire de création d'un client (§3.9 CDC).
     */
    public function create()
    {
        return view('pages.admin.clients.create');
    }

    /**
     * Enregistrer un client créé manuellement et envoyer l'invitation par email.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make(Str::random(64)),
                'country' => 'France',
                'is_admin' => false,
                'is_active' => true,
                'must_set_password' => true,
            ]);

            $token = $this->storePasswordSetupToken($user->email);

            try {
                SendAccountInvitationEmailJob::dispatch($user, $token);
            } catch (\Exception $e) {
                \Log::error('Erreur envoi invitation client: ' . $e->getMessage());

                return redirect()
                    ->route('admin.clients.show', $user)
                    ->with('error', 'Le client a été créé, mais l\'email d\'invitation n\'a pas pu être envoyé. Réessayez depuis la fiche client.');
            }

            return redirect()
                ->route('admin.clients.show', $user)
                ->with('success', 'Client créé avec succès. Un email d\'invitation a été envoyé à ' . $user->email . '.');
        } catch (\Exception $e) {
            \Log::error('Erreur création client admin: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du client.');
        }
    }

    /**
     * Afficher les détails d'un client
     */
    public function show(User $client)
    {
        if ($client->is_admin) {
            return redirect()->route('admin.clients')
                ->with('error', 'Cet utilisateur est un administrateur.');
        }

        $client->load([
            'reservations.villa.island',
            'reservations.payments',
            'reservations' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'clientDocuments.uploader',
        ]);

        $stats = [
            'total_reservations' => $client->reservations()->count(),
            'upcoming_reservations' => $client->reservations()
                ->where('check_in_date', '>=', Carbon::now())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'total_spent' => $client->reservations()
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'last_reservation' => $client->reservations()->latest()->first(),
        ];

        $clubService = app(PrivilegeClubService::class);
        $qualifyingStays = $clubService->countQualifyingStays($client);
        $earnedTier = $clubService->calculateTier($client);
        $tierDefinitions = $clubService->tierDefinitions();

        $pendingWhatsappNotifications = PrivilegeClubNotification::query()
            ->where('user_id', $client->id)
            ->pendingWhatsapp()
            ->orderByDesc('created_at')
            ->get();

        $recentWhatsappSentNotifications = PrivilegeClubNotification::query()
            ->where('user_id', $client->id)
            ->whereIn('type', [PrivilegeClubNotification::TYPE_TIER_UP, PrivilegeClubNotification::TYPE_TIER_DOWN])
            ->whereNotNull('whatsapp_sent_at')
            ->orderByDesc('whatsapp_sent_at')
            ->limit(5)
            ->get();

        $activePromoCodes = PromoCode::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        return view('pages.admin.client-show', compact(
            'client',
            'stats',
            'clubService',
            'qualifyingStays',
            'earnedTier',
            'tierDefinitions',
            'pendingWhatsappNotifications',
            'recentWhatsappSentNotifications',
            'activePromoCodes',
        ));
    }

    /**
     * Envoyer un code promo au client par email (§3.2 CDC — attribution ciblée).
     */
    public function sendPromoCode(User $client, Request $request)
    {
        if ($client->is_admin) {
            return back()->with('error', 'Action non applicable aux administrateurs.');
        }

        $validated = $request->validate([
            'promo_code_id' => ['required', 'exists:promo_codes,id'],
        ]);

        $promoCode = PromoCode::findOrFail($validated['promo_code_id']);

        if (! $promoCode->is_active) {
            return back()->with('error', 'Ce code promo est désactivé.');
        }

        try {
            $this->emailService->sendPromoCodeEmail($client, $promoCode);

            return back()->with('success', "Code promo {$promoCode->code} envoyé à {$client->email}.");
        } catch (\Exception $e) {
            \Log::error('Erreur envoi code promo client: ' . $e->getMessage());

            return back()->with('error', 'Impossible d\'envoyer le code promo par email.');
        }
    }

    /**
     * Ouvre WhatsApp avec un code promo prérempli pour le client (§3.2 CDC).
     */
    public function openPromoCodeWhatsapp(User $client, Request $request)
    {
        if ($client->is_admin) {
            return back()->with('error', 'Action non applicable aux administrateurs.');
        }

        $validated = $request->validate([
            'promo_code_id' => ['required', 'exists:promo_codes,id'],
        ]);

        if (! $client->phone) {
            return back()->with('error', 'Aucun numéro de téléphone renseigné pour ce client.');
        }

        $promoCode = PromoCode::findOrFail($validated['promo_code_id']);

        if (! $promoCode->is_active) {
            return back()->with('error', 'Ce code promo est désactivé.');
        }

        $valueLabel = $promoCode->type === 'percent'
            ? rtrim(rtrim(number_format((float) $promoCode->value, 2, ',', ''), '0'), ',') . ' %'
            : number_format((float) $promoCode->value, 2, ',', ' ') . ' €';

        $validUntil = $promoCode->valid_until?->format('d/m/Y');
        $message = $this->whatsAppService->buildPromoCodeMessage(
            $client->first_name ?? 'Client',
            $promoCode->code,
            $valueLabel,
            $validUntil
        );

        $link = $this->whatsAppService->buildLink($client->phone, $message);
        if (! $link) {
            return back()->with('error', 'Numéro de téléphone invalide pour WhatsApp.');
        }

        return redirect()->away($link);
    }

    /**
     * Tracer l'envoi manuel du message WhatsApp (CDC §3.1 — checklist admin).
     */
    public function markPrivilegeClubWhatsappSent(User $client, PrivilegeClubNotification $notification)
    {
        if ($client->is_admin) {
            return back()->with('error', 'Action non applicable aux administrateurs.');
        }

        if ($notification->user_id !== $client->id) {
            abort(404);
        }

        if (! $notification->requiresWhatsappFollowUp()) {
            return back()->with('error', 'Cette notification ne requiert pas de suivi WhatsApp.');
        }

        $notification->markWhatsappSent();

        return back()->with('success', 'Message WhatsApp marqué comme envoyé.');
    }

    /**
     * Ouvre WhatsApp avec un message prérempli pour une notification Privilege Club.
     */
    public function openPrivilegeClubWhatsapp(User $client, PrivilegeClubNotification $notification, PrivilegeClubService $clubService)
    {
        if ($client->is_admin) {
            return back()->with('error', 'Action non applicable aux administrateurs.');
        }

        if ($notification->user_id !== $client->id) {
            abort(404);
        }

        if (! $notification->requiresWhatsappFollowUp()) {
            return back()->with('error', 'Cette notification ne requiert pas de suivi WhatsApp.');
        }

        if (! $client->phone) {
            return back()->with('error', 'Aucun numéro de téléphone renseigné pour ce client.');
        }

        $message = $this->whatsAppService->buildPrivilegeClubMessage(
            $client->first_name ?? 'Client',
            $clubService->tierLabel($notification->new_tier)
        );

        $link = $this->whatsAppService->buildLink($client->phone, $message);
        if (! $link) {
            return back()->with('error', 'Numéro de téléphone invalide pour WhatsApp.');
        }

        return redirect()->away($link);
    }

    /**
     * Renvoyer l'email d'invitation (définir le mot de passe).
     */
    public function resendInvitation(User $client)
    {
        if ($client->is_admin) {
            return redirect()->route('admin.clients')
                ->with('error', 'Impossible d\'envoyer une invitation à un administrateur.');
        }

        if (! $client->must_set_password) {
            return redirect()
                ->route('admin.clients.show', $client)
                ->with('info', 'Ce client a déjà activé son compte.');
        }

        try {
            $token = $this->storePasswordSetupToken($client->email);
            SendAccountInvitationEmailJob::dispatch($client, $token);

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('success', 'Invitation renvoyée à ' . $client->email . '.');
        } catch (\Exception $e) {
            \Log::error('Erreur renvoi invitation client: ' . $e->getMessage());

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('error', 'Impossible d\'envoyer l\'email d\'invitation.');
        }
    }

    /**
     * Activer/Désactiver un client
     */
    public function toggleStatus(User $client)
    {
        if ($client->is_admin) {
            return redirect()->route('admin.clients')
                ->with('error', 'Impossible de modifier le statut d\'un administrateur.');
        }

        $client->update(['is_active' => ! $client->is_active]);

        $status = $client->is_active ? 'activé' : 'désactivé';

        return redirect()->route('admin.clients')
            ->with('success', "Client {$status} avec succès.");
    }

    /**
     * Ajuster le statut Privilege Club manuellement (§3.1 CDC).
     */
    public function updatePrivilegeClub(Request $request, User $client, PrivilegeClubService $clubService)
    {
        if ($client->is_admin) {
            return back()->with('error', 'Action non applicable aux administrateurs.');
        }

        $validated = $request->validate([
            'privilege_tier' => ['nullable', 'in:insider,signature,legend'],
            'privilege_tier_manual_override' => ['nullable', 'boolean'],
        ]);

        $tier = $validated['privilege_tier'] ?: null;
        $lock = $request->boolean('privilege_tier_manual_override', true);

        $clubService->setManualTier($client, $tier, $lock);

        return back()->with('success', 'Statut Privilege Club mis à jour.');
    }

    /**
     * Recalcul automatique du palier (déverrouille si demandé).
     */
    public function recalculatePrivilegeClub(Request $request, User $client, PrivilegeClubService $clubService)
    {
        if ($client->is_admin) {
            return back()->with('error', 'Action non applicable aux administrateurs.');
        }

        if ($request->boolean('unlock')) {
            $client->update(['privilege_tier_manual_override' => false]);
        }

        $clubService->updateTierIfChanged($client->fresh());

        return back()->with('success', 'Palier recalculé selon l\'historique des séjours.');
    }

    /**
     * Génère et enregistre un token de définition de mot de passe (24 h).
     */
    private function storePasswordSetupToken(string $email): string
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        return $token;
    }
}
