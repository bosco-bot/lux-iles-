<?php

namespace App\Http\Controllers;

use App\Models\ClientDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientDocumentController extends Controller
{
    /**
     * Télécharger un document du dossier personnel (espace client — §3.10 CDC).
     */
    public function download(ClientDocument $clientDocument)
    {
        $user = Auth::user();

        if (! $user || $clientDocument->user_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à ce document.');
        }

        if (! Storage::disk('public')->exists($clientDocument->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $clientDocument->file_path,
            $clientDocument->file_name
        );
    }
}
