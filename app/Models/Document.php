<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'type',
        'document_number',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_signed',
        'signed_at',
        'signed_by',
        'generated_at',
    ];

    protected $casts = [
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
        'generated_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Relation avec la réservation
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Relation avec l'utilisateur ayant signé
     */
    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    /**
     * Formater la taille du fichier
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
}




