<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TacheResource;

class TacheController extends Controller
{
     // Liste des tâches (Admin/Tuteur peut voir toutes, Étudiant voit les siennes)
    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user->role === 'etudiant') {
    //         $taches = Tache::whereHas('stage', function($q) use ($user) {
    //             $q->where('etudiant_id', $user->id);
    //         })->with('livrables')->paginate(10);
    //     } else {
    //         $taches = Tache::with(['stage','livrables'])->paginate(10);
    //     }

    //     return TacheResource::collection($taches);
    // }

    // 🔍 Récupérer les tâches du maître connecté
    public function index(Request $request)
    {
        $maitre = $request->user();

        $taches = Tache::with('etudiant')
            ->where('maitre_stage_id', $maitre->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'taches' => $taches
        ]);
    }

    // Créer une tâche
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'stage_id' => 'required|exists:stages,id',
    //         'titre' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'dateLimite' => 'nullable|date',
    //     ]);

    //     $tache = Tache::create($data);

    //     return new TacheResource($tache->load('livrables'));
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         // 'stage_id' => 'required|exists:stages,id',
    //         'stage_id' => 'nullable|,id',
    //         'etudiant_id' => 'required|exists:users,id',
    //         'titre' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'date_echeance' => 'required|date|after_or_equal:today',
    //     ]);

    //     $maitre = $request->user();

    //     if ($maitre->role !== 'maitre_stage') {
    //         return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
    //     }

    //     $tache = Tache::create([
    //         'stage_id' => $validated['stage_id'],
    //         'maitre_stage_id' => $maitre->id,
    //         'etudiant_id' => $validated['etudiant_id'],
    //         'titre' => $validated['titre'],
    //         'description' => $validated['description'] ?? '',
    //         'date_echeance' => $validated['date_echeance'],
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Tâche créée avec succès ✅',
    //         'tache' => $tache
    //     ]);
    // }


     public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                // 'stage_id' => 'required|exists:stages,id',
                'stage_id' => 'nullable|exists:stages,id',
                'etudiant_id' => 'required|exists:users,id',
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_echeance' => 'required|date|after_or_equal:today',
            ]);

            $maitre = $request->user();

            if ($maitre->role !== 'maitre_stage') {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            $tache = Tache::create([
                'stage_id' => $validated['stage_id'],
                'maitre_stage_id' => $maitre->id,
                'etudiant_id' => $validated['etudiant_id'],
                'titre' => $validated['titre'],
                'description' => $validated['description'] ?? '',
                'date_echeance' => $validated['date_echeance'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tâche créée avec succès ✅',
                'tache' => $tache
            ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
            // 🔍 Erreur de validation
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\QueryException $e) {
            // 💾 Erreur SQL (clé étrangère, etc.)
            \Log::error('Erreur SQL lors de la création de la tâche : '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création dans la base de données',
                'error' => $e->getMessage()
            ], 500);

        } catch (\Throwable $e) {
            // 🚨 Autre erreur imprévue
            \Log::error('Erreur dans TacheController@store : '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur',
                'error' => $e->getMessage()
            ], 500);
        }
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
