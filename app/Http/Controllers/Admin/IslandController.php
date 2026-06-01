<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IslandController extends Controller
{
    /**
     * Afficher la liste des îles
     */
    public function index()
    {
        $islands = Island::orderBy('name', 'asc')->get();
        return view('pages.admin.islands.index', compact('islands'));
    }

    /**
     * Afficher le formulaire de création d'une île
     */
    public function create()
    {
        return view('pages.admin.islands.create');
    }

    /**
     * Enregistrer une nouvelle île
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:islands,name',
            'code' => 'required|string|max:10|unique:islands,code',
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'country' => $request->country ?? 'France',
            'description' => $request->description,
        ];

        // Gérer l'upload d'image (obligatoire)
        $image = $request->file('image');
        $path = $image->store('islands', 'public');
        $data['image_path'] = $path;

        Island::create($data);

        return redirect()->route('admin.islands')
            ->with('success', 'Destination créée avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'une île
     */
    public function edit($id)
    {
        $island = Island::findOrFail($id);
        return view('pages.admin.islands.edit', compact('island'));
    }

    /**
     * Mettre à jour une île
     */
    public function update(Request $request, $id)
    {
        $island = Island::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:islands,name,' . $id,
            'code' => 'required|string|max:10|unique:islands,code,' . $id,
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'country' => $request->country ?? 'France',
            'description' => $request->description,
        ];

        // Gérer l'upload d'image (optionnel en édition)
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($island->image_path && Storage::disk('public')->exists($island->image_path)) {
                Storage::disk('public')->delete($island->image_path);
            }
            
            $image = $request->file('image');
            $path = $image->store('islands', 'public');
            $data['image_path'] = $path;
        }

        $island->update($data);

        return redirect()->route('admin.islands')
            ->with('success', 'Île mise à jour avec succès');
    }
}
