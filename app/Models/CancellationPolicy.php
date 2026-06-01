<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CancellationPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'refund_rules',
        'is_default',
        'is_active',
        'icon',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'refund_rules' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Générer un slug automatiquement si non fourni
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($policy) {
            if (empty($policy->slug)) {
                $policy->slug = Str::slug($policy->name);
            }
        });
    }

    /**
     * Calculer le pourcentage de remboursement selon les jours avant l'arrivée
     */
    public function calculateRefundPercentage($daysBeforeCheckIn)
    {
        if (!$this->refund_rules || !is_array($this->refund_rules)) {
            return 0;
        }

        // Trier les règles par jours décroissants
        $rules = collect($this->refund_rules)->sortByDesc('days_before');

        foreach ($rules as $rule) {
            if ($daysBeforeCheckIn >= ($rule['days_before'] ?? 0)) {
                return $rule['refund_percentage'] ?? 0;
            }
        }

        return 0;
    }

    /**
     * Formater les règles pour l'affichage
     */
    public function getFormattedRulesAttribute()
    {
        if (!$this->refund_rules || !is_array($this->refund_rules)) {
            return [];
        }

        $rules = collect($this->refund_rules)->sortByDesc('days_before');
        
        return $rules->map(function ($rule) {
            return [
                'days' => $rule['days_before'] ?? 0,
                'percentage' => $rule['refund_percentage'] ?? 0,
                'label' => $this->formatRuleLabel($rule['days_before'] ?? 0, $rule['refund_percentage'] ?? 0),
            ];
        })->toArray();
    }

    /**
     * Formater le label d'une règle
     */
    private function formatRuleLabel($days, $percentage)
    {
        if ($days == 0) {
            return $percentage . '% remboursé immédiatement';
        }
        
        if ($days == 1) {
            return $percentage . '% remboursé < J-1';
        }
        
        return $percentage . '% remboursé < J-' . $days;
    }
}
