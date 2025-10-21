<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stage;
use Illuminate\Http\Request;
use App\Models\DemandeDeStage;

class MaitreStageController extends Controller
{
    public function getStages(Request $request)
    {
        try {
            $user = $request->user();

            // Version simple sans relations pour tester
            $stages = Stage::where('maitre_stage_id', $user->id)->get();

            return response()->json([
                'success' => true,
                'data' => $stages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function downloadRapport($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Fonctionnalité en développement'
        ], 501);
    }

    public function updateNote(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'note' => 'required|numeric|min:0|max:20'
            ]);

            $stage = Stage::findOrFail($id);
            $stage->note = $validated['note'];
            $stage->save();

            return response()->json([
                'success' => true,
                'message' => 'Note enregistrée'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retourne la liste des étudiants affectés à l’entreprise du maître de stage connecté.
     */
    // public function etudiantsAffectes(Request $request)
    // {
    //     $user = $request->user();

    //     // Vérifier que c’est bien un maître de stage
    //     if ($user->role !== 'maitre_stage') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Accès refusé. Vous devez être maître de stage.'
    //         ], 403);
    //     }

    //     // Vérifier qu’il a une entreprise associée
    //     if (!$user->entreprise_id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Aucune entreprise associée à ce maître de stage.'
    //         ], 400);
    //     }

    //     // 🔍 Récupérer les étudiants affectés à cette entreprise
    //     $etudiants = User::where('role', 'apprenant')
    //         ->where('entreprise_id', $user->entreprise_id)
    //         ->select('id', 'name', 'prenom', 'email', 'telephone', 'departement_id')
    //         ->with('departement:id,nom') // optionnel si tu veux afficher le nom du département
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'entreprise' => $user->entreprise->nom,
    //         'nombre_etudiants' => $etudiants->count(),
    //         'etudiants' => $etudiants
    //     ]);
    // }

    public function etudiantsAffectes(Request $request)
    {
        $user = $request->user();

        // Vérifier le rôle
        if ($user->role !== 'maitre_stage') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Vous devez être maître de stage.'
            ], 403);
        }

        // Vérifier qu’il a une entreprise associée
        if (!$user->entreprise_id) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune entreprise associée à ce maître de stage.'
            ], 400);
        }

        // 🔍 Récupérer les demandes de stage acceptées pour cette entreprise
        $demandes = DemandeDeStage::with(['etudiant:id,name,prenom,email,telephone', 'campagne:id,nom'])
            ->where('entreprise_id', $user->entreprise_id)
            ->where('statut', 'acceptee')
            ->get();

        // Transformer les données pour le frontend
        $etudiants = $demandes->map(function ($demande) {
            return [
                'id' => $demande->etudiant->id,
                'nom' => $demande->etudiant->name,
                'prenom' => $demande->etudiant->prenom,
                'email' => $demande->etudiant->email,
                'telephone' => $demande->etudiant->telephone,
                'campagne' => $demande->campagne->nom ?? 'N/A',
                'adresse_1' => $demande->adresse_1,
                'adresse_2' => $demande->adresse_2,
                'statut' => $demande->statut,
            ];
        });

        return response()->json([
            'success' => true,
            'entreprise' => $user->entreprise->nom ?? 'N/A',
            'nombre_etudiants' => $etudiants->count(),
            'etudiants' => $etudiants
        ]);
    }

}
