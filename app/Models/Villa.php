<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Villa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'island_id',
        'address',
        'latitude',
        'longitude',
        'description',
        'short_description',
        'bedrooms',
        'bathrooms',
        'max_capacity',
        'surface_area',
        'base_price_per_night',
        'currency',
        'cleaning_fee',
        'service_fee_percentage',
        'deposit_amount',
        'check_in_time',
        'check_out_time',
        'minimum_stay_nights',
        'is_active',
        'is_featured',
        'sort_order',
        'airbnb_listing_id',
        'booking_listing_id',
        'abritel_listing_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'base_price_per_night' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'service_fee_percentage' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'max_capacity' => 'integer',
        'surface_area' => 'integer',
        'minimum_stay_nights' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Générer automatiquement le slug à partir du nom
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($villa) {
            if (empty($villa->slug)) {
                $villa->slug = Str::slug($villa->name);
            }
        });

        static::updating(function ($villa) {
            if ($villa->isDirty('name') && empty($villa->slug)) {
                $villa->slug = Str::slug($villa->name);
            }
        });
    }

    /**
     * Relation avec l'île
     */
    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    /**
     * Relation avec les photos
     */
    public function photos()
    {
        return $this->hasMany(VillaPhoto::class);
    }

    /**
     * Relation avec les équipements (many-to-many)
     */
    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'villa_equipments');
    }

    /**
     * Relation avec les périodes bloquées
     */
    public function availabilityBlocks()
    {
        return $this->hasMany(VillaAvailabilityBlock::class);
    }

    /**
     * Avis voyageurs (§3.4 CDC).
     */
    public function reviews()
    {
        return $this->hasMany(VillaReview::class);
    }

    public function publishedReviews()
    {
        return $this->reviews()->published()->orderByDesc('published_at');
    }

    public function averageRating(): ?float
    {
        $avg = $this->reviews()->published()->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function publishedReviewsCount(): int
    {
        return $this->reviews()->published()->count();
    }

    /**
     * Obtenir la photo principale
     */
    public function getPrimaryPhotoAttribute()
    {
        return $this->photos()->where('is_primary', true)->first();
    }

    /**
     * Relation avec les configurations iCal
     */
    public function icalConfigs()
    {
        return $this->hasMany(VillaIcalConfig::class);
    }

    /**
     * Relation avec les tarifs saisonniers
     */
    public function seasonalPrices()
    {
        return $this->hasMany(VillaSeasonalPrice::class);
    }

    /**
     * Obtenir le prix par nuit pour une date donnée
     * Prend en compte les tarifs saisonniers si disponibles
     * 
     * @param \DateTime|string $date Date pour laquelle obtenir le prix
     * @return float Prix par nuit pour cette date
     */
    public function getPriceForDate($date): float
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $seasonalPrices = $this->relationLoaded('seasonalPrices')
            ? $this->seasonalPrices
            : $this->seasonalPrices()->with('season')->get();

        $matchingPrices = [];

        foreach ($seasonalPrices as $seasonalPrice) {
            $season = $seasonalPrice->season;

            if ($season && $this->isDateInSeason($date, $season)) {
                $matchingPrices[] = (float) $seasonalPrice->price_per_night;
            }
        }

        if ($matchingPrices === []) {
            return (float) $this->base_price_per_night;
        }

        // CDC §3.3 : en cas de chevauchement, appliquer le tarif le plus élevé
        return max($matchingPrices);
    }

    /**
     * Vérifier si une date tombe dans une saison donnée
     * 
     * @param \DateTime $date
     * @param \App\Models\Season $season
     * @return bool
     */
    public function isDateInSeason(\DateTime $date, $season): bool
    {
        $month = (int) $date->format('n'); // 1-12
        $day = (int) $date->format('j'); // 1-31
        
        $startMonth = $season->start_month;
        $startDay = $season->start_day;
        $endMonth = $season->end_month;
        $endDay = $season->end_day;
        
        // Convertir la date en nombre pour faciliter la comparaison (mois * 100 + jour)
        $dateValue = $month * 100 + $day;
        $startValue = $startMonth * 100 + $startDay;
        $endValue = $endMonth * 100 + $endDay;
        
        // Si la saison commence et finit dans la même année (ex: Mai à Août)
        if ($startValue <= $endValue) {
            return $dateValue >= $startValue && $dateValue <= $endValue;
        } else {
            // Saison qui chevauche deux années (ex: Décembre à Avril)
            // La date est dans la saison si elle est >= début OU <= fin
            return $dateValue >= $startValue || $dateValue <= $endValue;
        }
    }

    /**
     * Calculer le prix total pour une période donnée
     * Prend en compte les tarifs saisonniers pour chaque nuit
     * 
     * @param \DateTime|string $checkIn Date d'arrivée
     * @param \DateTime|string $checkOut Date de départ
     * @return float Prix total pour la période
     */
    public function calculatePriceForPeriod($checkIn, $checkOut): float
    {
        if (is_string($checkIn)) {
            $checkIn = new \DateTime($checkIn);
        }
        if (is_string($checkOut)) {
            $checkOut = new \DateTime($checkOut);
        }
        
        $total = 0;
        $currentDate = clone $checkIn;
        
        // Parcourir chaque nuit de la période
        while ($currentDate < $checkOut) {
            $total += $this->getPriceForDate($currentDate);
            $currentDate->modify('+1 day');
        }
        
        return $total;
    }

    /**
     * Relation avec les favoris
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Relation many-to-many avec les utilisateurs qui ont cette villa en favoris
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * Vérifier si cette villa est en favoris pour un utilisateur donné
     */
    public function isFavoritedBy($userId): bool
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}




