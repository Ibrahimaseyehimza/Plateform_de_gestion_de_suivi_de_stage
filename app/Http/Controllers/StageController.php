<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StageResource;

class StageController extends Controller
{
     // Liste des stages
    public function index()
    {
        return StageResource::collection(Stage::with(['etudiant','entreprise','tuteur'])->paginate(10));
    }

    // Créer un stage (affecter un étudiant à une entreprise)
    public function store(Request $request)
    {
        $data = $request->validate([
            'etudiant_id'   => 'required|exists:users,id',
            'entreprise_id' => 'required|exists:entreprises,id',
            'tuteur_id'     => 'nullable|exists:users,id',
            'dateDebut'     => 'required|date',
            'dateFin'       => 'required|date|after:dateDebut',
        ]);

        $stage = Stage::create($data);

        return new StageResource($stage->load(['etudiant','entreprise','tuteur']));
    }

    // Détails d’un stage
    public function show(Stage $stage)
    {
        return new StageResource($stage->load(['etudiant','entreprise','tuteur']));
    }

    // Mettre à jour un stage
    public function update(Request $request, Stage $stage)
    {
        $data = $request->validate([
            'tuteur_id' => 'nullable|exists:users,id',
            'dateDebut' => 'nullable|date',
            'dateFin'   => 'nullable|date|after:dateDebut',
        ]);

        $stage->update($data);

        return new StageResource($stage->load(['etudiant','entreprise','tuteur']));
    }

    // Supprimer un stage
    public function destroy(Stage $stage)
    {
        $stage->delete();

        return response()->json(['message' => 'Stage supprimé avec succès']);
    }
}
