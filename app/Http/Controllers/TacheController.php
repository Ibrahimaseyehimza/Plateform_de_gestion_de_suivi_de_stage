<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TacheResource;

class TacheController extends Controller
{
     // Liste des tâches (Admin/Tuteur peut voir toutes, Étudiant voit les siennes)
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'etudiant') {
            $taches = Tache::whereHas('stage', function($q) use ($user) {
                $q->where('etudiant_id', $user->id);
            })->with('livrables')->paginate(10);
        } else {
            $taches = Tache::with(['stage','livrables'])->paginate(10);
        }

        return TacheResource::collection($taches);
    }

    // Créer une tâche
    public function store(Request $request)
    {
        $data = $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dateLimite' => 'nullable|date',
        ]);

        $tache = Tache::create($data);

        return new TacheResource($tache->load('livrables'));
    }

    // Voir une tâche
    public function show(Tache $tache)
    {
        return new TacheResource($tache->load('livrables'));
    }

    // Mettre à jour une tâche
    public function update(Request $request, Tache $tache)
    {
        $data = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'dateLimite' => 'nullable|date',
        ]);

        $tache->update($data);

        return new TacheResource($tache->load('livrables'));
    }

    // Supprimer une tâche
    public function destroy(Tache $tache)
    {
        $tache->delete();

        return response()->json(['message' => 'Tâche supprimée avec succès']);
    }
}
