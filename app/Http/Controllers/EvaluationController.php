<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EvaluationResource;

class EvaluationController extends Controller
{
     // Liste des évaluations
    public function index()
    {
        return EvaluationResource::collection(
            Evaluation::with('stage.etudiant','stage.entreprise')->paginate(10)
        );
    }

    // Créer une évaluation (par un tuteur)
    public function store(Request $request)
    {
        $data = $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'note' => 'required|numeric|min:0|max:20',
            'commentaire' => 'nullable|string',
        ]);

        $stage = Stage::findOrFail($data['stage_id']);

        // Vérifier que le user connecté est bien le tuteur du stage
        if (auth()->user()->role !== 'tuteur' || $stage->tuteur_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $evaluation = Evaluation::create($data);

        return new EvaluationResource($evaluation->load('stage.etudiant','stage.entreprise'));
    }

    // Voir une évaluation
    public function show(Evaluation $evaluation)
    {
        return new EvaluationResource($evaluation->load('stage.etudiant','stage.entreprise'));
    }

    // Mettre à jour une évaluation
    public function update(Request $request, Evaluation $evaluation)
    {
        $data = $request->validate([
            'note' => 'sometimes|numeric|min:0|max:20',
            'commentaire' => 'nullable|string',
        ]);

        // Vérifier que seul le tuteur qui a fait le stage peut modifier
        if (auth()->user()->role !== 'tuteur' || $evaluation->stage->tuteur_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $evaluation->update($data);

        return new EvaluationResource($evaluation->load('stage.etudiant','stage.entreprise'));
    }

    // Supprimer une évaluation
    public function destroy(Evaluation $evaluation)
    {
        if (auth()->user()->role !== 'tuteur' || $evaluation->stage->tuteur_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $evaluation->delete();

        return response()->json(['message' => 'Évaluation supprimée avec succès']);
    }
}
