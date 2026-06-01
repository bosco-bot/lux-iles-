<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsHistory extends Model
{
    use HasFactory;

    protected $table = 'settings_history';

    protected $fillable = [
        'setting_key',
        'old_value',
        'new_value',
        'changed_by',
        'change_type',
        'notes',
    ];

    /**
     * Relation avec l'utilisateur qui a effectué la modification
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Obtenir le nom du paramètre formaté
     */
    public function getSettingNameAttribute()
    {
        return match($this->setting_key) {
            'global_tax_rate' => 'TVA Globale',
            'tourist_tax_per_night' => 'Taxe de Séjour (Par nuit/pers)',
            'tourist_tax_enabled' => 'Taxe de Séjour (Activation)',
            'service_fee_percentage' => 'Frais de Service (%)',
            'deposit_percentage_min' => 'Acompte Minimum (%)',
            'deposit_percentage_max' => 'Acompte Maximum (%)',
            'balance_due_days_before_checkin' => 'Jours avant arrivée - Paiement du solde',
            'deposit_guarantee_days_before_checkin' => 'Jours avant arrivée - Dépôt de garantie',
            'cancellation_policy_days' => 'Délai d\'annulation (jours)',
            default => $this->setting_key,
        };
    }

    /**
     * Obtenir la valeur formatée pour l'affichage
     */
    public function getFormattedOldValueAttribute()
    {
        if ($this->old_value === null) {
            return 'N/A';
        }

        if ($this->setting_key === 'tourist_tax_enabled') {
            return $this->old_value ? 'Activée' : 'Désactivée';
        }

        if (in_array($this->setting_key, ['global_tax_rate', 'service_fee_percentage', 'deposit_percentage_min', 'deposit_percentage_max'])) {
            return number_format((float)$this->old_value, 2, ',', ' ') . ' %';
        }

        if ($this->setting_key === 'tourist_tax_per_night') {
            return number_format((float)$this->old_value, 2, ',', ' ') . ' €';
        }

        if (in_array($this->setting_key, ['balance_due_days_before_checkin', 'deposit_guarantee_days_before_checkin', 'cancellation_policy_days'])) {
            return (int)$this->old_value . ' jour' . ((int)$this->old_value > 1 ? 's' : '');
        }

        return $this->old_value;
    }

    /**
     * Obtenir la nouvelle valeur formatée pour l'affichage
     */
    public function getFormattedNewValueAttribute()
    {
        if ($this->new_value === null) {
            return 'N/A';
        }

        if ($this->setting_key === 'tourist_tax_enabled') {
            return $this->new_value ? 'Activée' : 'Désactivée';
        }

        if (in_array($this->setting_key, ['global_tax_rate', 'service_fee_percentage', 'deposit_percentage_min', 'deposit_percentage_max'])) {
            return number_format((float)$this->new_value, 2, ',', ' ') . ' %';
        }

        if ($this->setting_key === 'tourist_tax_per_night') {
            return number_format((float)$this->new_value, 2, ',', ' ') . ' €';
        }

        if (in_array($this->setting_key, ['balance_due_days_before_checkin', 'deposit_guarantee_days_before_checkin', 'cancellation_policy_days'])) {
            return (int)$this->new_value . ' jour' . ((int)$this->new_value > 1 ? 's' : '');
        }

        return $this->new_value;
    }
}
