<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TacheResource;
use Illuminate\Support\Facades\Log;

class TacheController extends Controller
{
    // 🔍 Récupérer les tâches du maître connecté
    public function index(Request $request)
    {
        try {
            $maitre = $request->user();

            $taches = Tache::with('etudiant:id,name,prenom,email')
                ->where('maitre_stage_id', $maitre->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $taches,
                'taches' => $taches // Pour compatibilité
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur index tâches: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des tâches',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ➕ Créer une tâche
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'etudiant_id' => 'required|exists:users,id',
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_echeance' => 'required|date|after_or_equal:today',
                'priorite' => 'required|in:basse,moyenne,haute',
            ]);

            $maitre = $request->user();

            if ($maitre->role !== 'maitre_stage') {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $tache = Tache::create([
                'maitre_stage_id' => $maitre->id,
                'etudiant_id' => $validated['etudiant_id'],
                'titre' => $validated['titre'],
                'description' => $validated['description'] ?? '',
                'date_echeance' => $validated['date_echeance'],
                'priorite' => $validated['priorite'],
                'statut' => 'en_attente',
            ]);

            $tache->load('etudiant:id,name,prenom');

            return response()->json([
                'success' => true,
                'message' => 'Tâche créée avec succès ✅',
                'data' => $tache,
                'tache' => $tache
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erreur store tâche: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ✏️ Mettre à jour une tâche
    public function update(Request $request, Tache $tache)
    {
        try {
            // Vérifier que c'est bien le maître qui a créé cette tâche
            if ($tache->maitre_stage_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $validated = $request->validate([
                'etudiant_id' => 'sometimes|exists:users,id',
                'titre' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'date_echeance' => 'sometimes|date',
                'priorite' => 'sometimes|in:basse,moyenne,haute',
            ]);

            $tache->update($validated);
            $tache->load('etudiant:id,name,prenom');

            return response()->json([
                'success' => true,
                'message' => 'Tâche mise à jour avec succès ✅',
                'data' => $tache,
                'tache' => $tache
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur update tâche: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // 🗑️ Supprimer une tâche
    public function destroy(Request $request, Tache $tache)
    {
        try {
            // Vérifier que c'est bien le maître qui a créé cette tâche
            if ($tache->maitre_stage_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $tache->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tâche supprimée avec succès ✅'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur destroy tâche: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // 👥 Récupérer les étudiants affectés au maître
    public function getEtudiantsAffectes(Request $request)
    {
        try {
            $maitre = $request->user();

            // Option 1 : Si les étudiants ont un champ maitre_stage_id
            $etudiants = User::where('maitre_stage_id', $maitre->id)
                ->where('role', 'etudiant')
                ->select('id', 'name', 'prenom', 'email')
                ->get();

            // Option 2 : Si c'est via une relation stages
            // $etudiants = User::whereHas('stage', function($q) use ($maitre) {
            //     $q->where('maitre_stage_id', $maitre->id);
            // })
            // ->where('role', 'etudiant')
            // ->select('id', 'name', 'prenom', 'email')
            // ->get();

            return response()->json([
                'success' => true,
                'data' => $etudiants,
                'etudiants' => $etudiants
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getEtudiantsAffectes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des étudiants',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // 📋 Voir une tâche
    public function show(Tache $tache)
    {
        try {
            $tache->load('etudiant:id,name,prenom,email');
            
            return response()->json([
                'success' => true,
                'data' => $tache,
                'tache' => $tache
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche non trouvée'
            ], 404);
        }
    }
}