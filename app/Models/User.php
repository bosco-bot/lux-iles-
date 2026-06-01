<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'birth_date',
        'nationality',
        'is_admin',
        'is_active',
        'must_set_password',
        'privilege_tier',
        'privilege_tier_manual_override',
        'privilege_tier_updated_at',
        'photo_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'must_set_password' => 'boolean',
            'privilege_tier_manual_override' => 'boolean',
            'privilege_tier_updated_at' => 'datetime',
        ];
    }

    public function privilegeClubNotifications()
    {
        return $this->hasMany(PrivilegeClubNotification::class)->orderByDesc('created_at');
    }

    public function privilegeClubTierLabel(): string
    {
        return app(\App\Services\PrivilegeClubService::class)->tierLabel($this->privilege_tier);
    }

    /**
     * Libellé du statut compte pour l'admin (§3.9 CDC).
     */
    public function getAccountStatusLabelAttribute(): string
    {
        if ($this->is_admin) {
            return 'Administrateur';
        }

        if ($this->must_set_password) {
            return 'Invitation envoyée';
        }

        return 'Actif';
    }

    /**
     * Relation avec les réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Documents du dossier client (contrats, devis — §3.10 CDC).
     */
    public function clientDocuments()
    {
        return $this->hasMany(ClientDocument::class)->orderByDesc('created_at');
    }

    /**
     * Relation avec les messages envoyés
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Relation avec les messages reçus
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Tous les messages (envoyés et reçus)
     */
    public function messages()
    {
        return Message::where('sender_id', $this->id)
            ->orWhere('recipient_id', $this->id);
    }

    /**
     * Relation avec les rôles (many-to-many)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Obtenir le rôle principal (premier rôle ou admin par défaut)
     */
    public function getPrimaryRoleAttribute()
    {
        $role = $this->roles()->first();
        if ($role) {
            return $role;
        }
        // Si pas de rôle mais is_admin, retourner le rôle admin
        if ($this->is_admin) {
            return Role::where('name', 'admin')->first();
        }
        return null;
    }

    /**
     * Relation avec les favoris
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Relation many-to-many avec les villas favorites
     */
    public function favoriteVillas()
    {
        return $this->belongsToMany(Villa::class, 'favorites')->withTimestamps();
    }

    /**
     * Vérifier si une villa est en favoris pour cet utilisateur
     */
    public function hasFavorite($villaId): bool
    {
        return $this->favorites()->where('villa_id', $villaId)->exists();
    }
}
