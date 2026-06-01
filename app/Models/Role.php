<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Relation avec les utilisateurs (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /**
     * Obtenir le badge color selon le rôle
     */
    public function getBadgeColorAttribute()
    {
        return match($this->name) {
            'admin' => 'danger',
            'manager' => 'primary',
            'accountant' => 'warning',
            'support' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Obtenir le nom d'affichage formaté
     */
    public function getFormattedNameAttribute()
    {
        return match($this->name) {
            'admin' => 'Super Admin',
            'manager' => 'Gestionnaire',
            'accountant' => 'Comptable',
            'support' => 'Support Client',
            default => $this->display_name,
        };
    }
}
