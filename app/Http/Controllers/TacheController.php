<?php

// namespace App\Http\Controllers;

// use App\Models\Tache;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use App\Http\Resources\TacheResource;

// class TacheController extends Controller
// {
//     // 🔍 Récupérer les tâches du maître connecté
//     public function index(Request $request)
//     {
//         $maitre = $request->user();

//         $taches = Tache::with('etudiant')
//             ->where('maitre_stage_id', $maitre->id)
//             ->orderBy('created_at', 'desc')
//             ->get();

//         return response()->json([
//             'success' => true,
//             'taches' => $taches
//         ]);
//     }

//      public function store(Request $request)
//     {
//         try {
//             $validated = $request->validate([
//                 // 'stage_id' => 'required|exists:stages,id',
//                 'stage_id' => 'nullable|exists:stages,id',
//                 'etudiant_id' => 'required|exists:users,id',
//                 'titre' => 'required|string|max:255',
//                 'description' => 'nullable|string',
//                 'date_echeance' => 'required|date|after_or_equal:today',
//             ]);

//             $maitre = $request->user();

//             if ($maitre->role !== 'maitre_stage') {
//                 return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
//             }

//             $tache = Tache::create([
//                 'stage_id' => $validated['stage_id'],
//                 'maitre_stage_id' => $maitre->id,
//                 'etudiant_id' => $validated['etudiant_id'],
//                 'titre' => $validated['titre'],
//                 'description' => $validated['description'] ?? '',
//                 'date_echeance' => $validated['date_echeance'],
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Tâche créée avec succès ✅',
//                 'tache' => $tache
//             ]);

//             } catch (\Illuminate\Validation\ValidationException $e) {
//             // 🔍 Erreur de validation
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Erreur de validation',
//                 'errors' => $e->errors()
//             ], 422);

//         } catch (\Illuminate\Database\QueryException $e) {
//             // 💾 Erreur SQL (clé étrangère, etc.)
//             \Log::error('Erreur SQL lors de la création de la tâche : '.$e->getMessage());

//             return response()->json([
//                 'success' => false,
//                 'message' => 'Erreur lors de la création dans la base de données',
//                 'error' => $e->getMessage()
//             ], 500);

//         } catch (\Throwable $e) {
//             // 🚨 Autre erreur imprévue
//             \Log::error('Erreur dans TacheController@store : '.$e->getMessage(), [
//                 'trace' => $e->getTraceAsString()
//             ]);

//             return response()->json([
//                 'success' => false,
//                 'message' => 'Erreur interne du serveur',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     // Voir une tâche
//     public function show(Tache $tache)
//     {
//         return new TacheResource($tache->load('livrables'));
//     }

//     // Mettre à jour une tâche
//     public function update(Request $request, Tache $tache)
//     {
//         $data = $request->validate([
//             'titre' => 'sometimes|string|max:255',
//             'description' => 'nullable|string',
//             'dateLimite' => 'nullable|date',
//         ]);

//         $tache->update($data);

//         return new TacheResource($tache->load('livrables'));
//     }

//     // Supprimer une tâche
//     public function destroy(Tache $tache)
//     {
//         $tache->delete();

//         return response()->json(['message' => 'Tâche supprimée avec succès']);
//     }
// }













namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TacheController extends Controller
{
    /**
     * Récupérer toutes les tâches avec filtres et recherche
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        if (!$entrepriseId) {
            return response()->json([
                'success' => false,
                'message' => "Aucune entreprise associée à ce maître de stage."
            ], 404);
        }

        // Paramètres de recherche et filtre
        $search = $request->get('search', '');
        $statut = $request->get('statut', 'all');

        // Query de base avec relations
        $query = Tache::with(['apprenant', 'entreprise'])
            ->where('entreprise_id', $entrepriseId);

        // Filtre par recherche
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('apprenant', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('prenom', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par statut
        if ($statut && $statut !== 'all') {
            $query->where('statut', $statut);
        }

        $taches = $query->orderBy('created_at', 'desc')->get();

        // Calculer les statistiques
        $statsQuery = Tache::where('entreprise_id', $entrepriseId);

        $stats = [
            'total' => $statsQuery->count(),
            'en_attente' => (clone $statsQuery)->where('statut', 'en_attente')->count(),
            'en_cours' => (clone $statsQuery)->where('statut', 'en_cours')->count(),
            'terminees' => (clone $statsQuery)->where('statut', 'terminee')->count(),
        ];

        return response()->json([
            'success' => true,
            'taches' => $taches,
            'stats' => $stats
        ], 200);
    }

    /**
     * Afficher une tâche spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $tache = Tache::with(['apprenant', 'maitreStage', 'entreprise'])
            ->where('entreprise_id', $user->entreprise_id)
            ->find($id);

        if (!$tache) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'tache' => $tache
        ], 200);
    }

    /**
     * Créer une nouvelle tâche
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->entreprise_id) {
            return response()->json([
                'success' => false,
                'message' => "Vous devez être rattaché à une entreprise pour créer une tâche."
            ], 403);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'apprenant_id' => 'required|exists:users,id',
            // 'priorite' => 'required|in:basse,moyenne,haute',
            'date_echeance' => 'required|date|after_or_equal:today',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'apprenant_id.required' => 'Vous devez sélectionner un apprenant',
            'apprenant_id.exists' => 'Cet apprenant n\'existe pas',
            // 'priorite.in' => 'Priorité invalide',
            'date_echeance.required' => 'La date d\'échéance est obligatoire',
            'date_echeance.after_or_equal' => 'La date d\'échéance ne peut pas être dans le passé',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier que l'apprenant est bien rattaché à la même entreprise
        $apprenant = \App\Models\User::find($request->apprenant_id);

        if ($apprenant->entreprise_id != $user->entreprise_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cet apprenant n\'est pas rattaché à votre entreprise'
            ], 403);
        }

        // Créer la tâche
        $tache = Tache::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'apprenant_id' => $request->apprenant_id,
            'entreprise_id' => $user->entreprise_id,
            'maitre_stage_id' => $user->id,
            // 'priorite' => $request->priorite,
            'date_echeance' => $request->date_echeance,
            'statut' => 'en_cours',
        ]);

        $tache->load(['apprenant', 'entreprise']);

        return response()->json([
            'success' => true,
            'message' => 'Tâche créée avec succès',
            'tache' => $tache
        ], 201);
    }

    /**
     * Mettre à jour une tâche
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $tache = Tache::where('entreprise_id', $user->entreprise_id)
            ->where('maitre_stage_id', $user->id)
            ->find($id);

        if (!$tache) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche introuvable ou vous n\'avez pas les droits'
            ], 404);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:5000',
            // 'priorite' => 'sometimes|in:basse,moyenne,haute',
            'statut' => 'sometimes|in:en_attente,en_cours,terminee',
            'date_echeance' => 'sometimes|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $tache->update($request->only([
            'titre',
            'description',
            // 'priorite',
            'statut',
            'date_echeance'
        ]));

        $tache->load(['apprenant', 'entreprise']);

        return response()->json([
            'success' => true,
            'message' => 'Tâche mise à jour avec succès',
            'tache' => $tache
        ], 200);
    }

    /**
     * Supprimer une tâche
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $tache = Tache::where('entreprise_id', $user->entreprise_id)
            ->where('maitre_stage_id', $user->id)
            ->find($id);

        if (!$tache) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche introuvable ou vous n\'avez pas les droits'
            ], 404);
        }

        $tache->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tâche supprimée avec succès'
        ], 200);
    }

    /**
     * Marquer une tâche comme terminée
     */
    public function marquerTerminee(Request $request, $id)
    {
        $user = $request->user();

        $tache = Tache::where('entreprise_id', $user->entreprise_id)
            ->find($id);

        if (!$tache) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche introuvable'
            ], 404);
        }

        $tache->update(['statut' => 'terminee']);
        $tache->load(['apprenant', 'entreprise']);

        return response()->json([
            'success' => true,
            'message' => 'Tâche marquée comme terminée',
            'tache' => $tache
        ], 200);
    }
}
