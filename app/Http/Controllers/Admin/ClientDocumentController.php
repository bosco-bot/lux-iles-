<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientDocumentController extends Controller
{
    private const MAX_FILE_SIZE_KB = 15360; // 15 Mo

    private const ALLOWED_MIMES = 'pdf,docx';

    /**
     * Téléverser un document dans le dossier client (§3.10 CDC).
     */
    public function store(Request $request, User $client)
    {
        if ($client->is_admin) {
            return redirect()->route('admin.clients')
                ->with('error', 'Impossible d\'ajouter un document à un administrateur.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:'.self::ALLOWED_MIMES, 'max:'.self::MAX_FILE_SIZE_KB],
        ]);

        try {
            $file = $validated['file'];
            $storedPath = $this->storeUploadedFile($file, $client->id);

            ClientDocument::create([
                'user_id' => $client->id,
                'title' => $validated['title'],
                'file_path' => $storedPath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('success', 'Document téléversé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur téléversement document client: '.$e->getMessage());

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('error', 'Le document n\'a pas pu être enregistré.');
        }
    }

    /**
     * Remplacer un document (fichier et/ou titre).
     */
    public function update(Request $request, User $client, ClientDocument $document)
    {
        $this->ensureDocumentBelongsToClient($client, $document);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:'.self::ALLOWED_MIMES, 'max:'.self::MAX_FILE_SIZE_KB],
        ]);

        try {
            if ($request->hasFile('file')) {
                $this->deleteStoredFile($document->file_path);
                $file = $validated['file'];
                $storedPath = $this->storeUploadedFile($file, $client->id);

                $document->fill([
                    'file_path' => $storedPath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => auth()->id(),
                ]);
            }

            $document->title = $validated['title'];
            $document->save();

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('success', 'Document mis à jour avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour document client: '.$e->getMessage());

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('error', 'Le document n\'a pas pu être mis à jour.');
        }
    }

    /**
     * Supprimer un document du dossier client.
     */
    public function destroy(User $client, ClientDocument $document)
    {
        $this->ensureDocumentBelongsToClient($client, $document);

        try {
            $this->deleteStoredFile($document->file_path);
            $document->delete();

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('success', 'Document supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur suppression document client: '.$e->getMessage());

            return redirect()
                ->route('admin.clients.show', $client)
                ->with('error', 'Le document n\'a pas pu être supprimé.');
        }
    }

    /**
     * Télécharger un document (admin).
     */
    public function download(User $client, ClientDocument $document)
    {
        $this->ensureDocumentBelongsToClient($client, $document);

        return $this->fileDownloadResponse($document);
    }

    private function ensureDocumentBelongsToClient(User $client, ClientDocument $document): void
    {
        if ($document->user_id !== $client->id) {
            abort(404);
        }
    }

    private function storeUploadedFile($file, int $userId): string
    {
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid().'_'.($safeName ?: 'document').'.'.$extension;

        return $file->storeAs('client-documents/'.$userId, $filename, 'public');
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function fileDownloadResponse(ClientDocument $document)
    {
        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
