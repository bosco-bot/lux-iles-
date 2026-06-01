<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'sender_id',
        'recipient_id',
        'subject',
        'body',
        'is_read',
        'read_at',
        'is_admin_message',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_admin_message' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relation avec l'expéditeur
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relation avec le destinataire
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Relation avec la réservation
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Relation avec les pièces jointes
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    /**
     * Marquer le message comme lu
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Formater la date de manière courte (ex: "7 min" au lieu de "il y a 7 minutes")
     */
    public function getShortTimeAgoAttribute()
    {
        if (!$this->created_at) {
            return '';
        }
        
        $now = now();
        
        // Si la date est dans le futur (ne devrait pas arriver normalement)
        if ($this->created_at->isFuture()) {
            return 'Maintenant';
        }
        
        // Moins d'une minute
        if ($this->created_at->diffInSeconds($now) < 60) {
            return 'À l\'instant';
        }
        
        // Moins d'une heure - utiliser diffInMinutes et arrondir
        if ($this->created_at->diffInHours($now) < 1) {
            $minutes = floor($this->created_at->diffInMinutes($now));
            return $minutes . ' min';
        }
        
        // Moins d'un jour
        if ($this->created_at->diffInDays($now) < 1) {
            $hours = floor($this->created_at->diffInHours($now));
            return $hours . 'h';
        }
        
        // Moins d'une semaine
        if ($this->created_at->diffInWeeks($now) < 1) {
            $days = floor($this->created_at->diffInDays($now));
            return $days . 'j';
        }
        
        // Moins d'un mois
        if ($this->created_at->diffInMonths($now) < 1) {
            $weeks = floor($this->created_at->diffInWeeks($now));
            return $weeks . ' sem';
        }
        
        // Moins d'un an
        if ($this->created_at->diffInYears($now) < 1) {
            $months = floor($this->created_at->diffInMonths($now));
            return $months . ' mois';
        }
        
        // Un an ou plus
        $years = floor($this->created_at->diffInYears($now));
        return $years . ' an' . ($years > 1 ? 's' : '');
    }
}

