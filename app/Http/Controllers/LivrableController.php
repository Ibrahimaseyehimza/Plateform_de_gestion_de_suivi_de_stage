<?php

namespace App\Http\Controllers;

use App\Models\Livrable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\LivrableResource;

class LivrableController extends Controller
{
     // Liste des livrables (un étudiant voit seulement les siens via ses tâches)
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'etudiant') {
            $livrables = Livrable::whereHas('tache.stage', function($q) use ($user) {
                $q->where('etudiant_id', $user->id);
            })->paginate(10);
        } else {
            $livrables = Livrable::with('tache')->paginate(10);
        }

        return LivrableResource::collection($livrables);
    }

    // Upload d’un livrable
    public function store(Request $request)
    {
        $data = $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'fichier' => 'required|file|mimes:pdf,docx,zip,png,jpg|max:2048',
            'commentaire' => 'nullable|string',
        ]);

        // Sauvegarder le fichier dans storage/app/public/livrables
        $path = $request->file('fichier')->store('livrables', 'public');

        $livrable = Livrable::create([
            'tache_id' => $data['tache_id'],
            'fichier' => $path,
            'commentaire' => $data['commentaire'] ?? null,
        ]);

        return new LivrableResource($livrable);
    }

    // Voir un livrable
    public function show(Livrable $livrable)
    {
        return new LivrableResource($livrable);
    }

    // Supprimer un livrable
    public function destroy(Livrable $livrable)
    {
        // Supprimer fichier du disque
        Storage::disk('public')->delete($livrable->fichier);

        $livrable->delete();

        return response()->json(['message' => 'Livrable supprimé avec succès']);
    }
}
