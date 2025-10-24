<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\User;
use Illuminate\Http\Request;

class TacheController extends Controller
{
    /**
     * Liste des tâches créées par le maître de stage
     */
    public function index(Request $request)
    {
        try {
            $maitreStage = $request->user();

            // Filtres
            $search = $request->get('search', '');
            $statut = $request->get('statut', 'all');

            $query = Tache::where('maitre_stage_id', $maitreStage->id)
                ->with(['apprenant', 'entreprise']);

            // Recherche
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('titre', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('apprenant', function($sq) use ($search) {
                          $sq->where('prenom', 'like', "%{$search}%")
                             ->orWhere('nom', 'like', "%{$search}%");
                      });
                });
            }

            // Filtre statut
            if ($statut !== 'all') {
                $query->where('statut', $statut);
            }

            $taches = $query->orderBy('created_at', 'desc')->get();

            // Statistiques
            $stats = [
                'total' => Tache::where('maitre_stage_id', $maitreStage->id)->count(),
                'en_attente' => Tache::where('maitre_stage_id', $maitreStage->id)->where('statut', 'en_attente')->count(),
                'en_cours' => Tache::where('maitre_stage_id', $maitreStage->id)->where('statut', 'en_cours')->count(),
                'terminees' => Tache::where('maitre_stage_id', $maitreStage->id)->where('statut', 'terminee')->count(),
            ];

            return response()->json([
                'success' => true,
                'taches' => $taches,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des tâches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle tâche
     */
    public function store(Request $request)
    {
        try {
            $maitreStage = $request->user();

            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'apprenant_id' => 'required|exists:users,id',
                'priorite' => 'required|in:basse,moyenne,haute',
                'date_echeance' => 'required|date|after:today',
            ]);

            // ✅ VERIFICATION IMPORTANTE : L'apprenant doit être de la même entreprise
            $apprenant = User::findOrFail($validated['apprenant_id']);

            if ($apprenant->entreprise_id !== $maitreStage->entreprise_id) {
                return response()->json([
                    'success' => false,
                    'message' => "Cet apprenant n'est pas rattaché à votre entreprise",
                    'debug' => [
                        'apprenant_entreprise_id' => $apprenant->entreprise_id,
                        'maitre_entreprise_id' => $maitreStage->entreprise_id
                    ]
                ], 403);
            }

            // Créer la tâche
            $tache = Tache::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'apprenant_id' => $validated['apprenant_id'],
                'maitre_stage_id' => $maitreStage->id,
                'entreprise_id' => $maitreStage->entreprise_id,
                'priorite' => $validated['priorite'],
                'date_echeance' => $validated['date_echeance'],
                'statut' => 'en_cours',
            ]);

            $tache->load('apprenant');

            return response()->json([
                'success' => true,
                'message' => 'Tâche créée avec succès',
                'tache' => $tache
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la tâche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer une tâche comme terminée
     */
    public function terminer($id, Request $request)
    {
        try {
            $maitreStage = $request->user();

            $tache = Tache::where('id', $id)
                ->where('maitre_stage_id', $maitreStage->id)
                ->firstOrFail();

            $tache->update(['statut' => 'terminee']);

            return response()->json([
                'success' => true,
                'message' => 'Tâche marquée comme terminée',
                'tache' => $tache
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche introuvable',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Supprimer une tâche
     */
    public function destroy($id, Request $request)
    {
        try {
            $maitreStage = $request->user();

            $tache = Tache::where('id', $id)
                ->where('maitre_stage_id', $maitreStage->id)
                ->firstOrFail();

            $tache->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tâche supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche introuvable',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
