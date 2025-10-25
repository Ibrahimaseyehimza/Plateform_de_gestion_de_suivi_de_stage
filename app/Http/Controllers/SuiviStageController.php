<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SuiviStageController extends Controller
{
    //  public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     // Cas 1️⃣ : Chef de département
    //     if ($user->role === 'chef_departement') {
    //         $query = User::with(['metier', 'livrables', 'appreciations'])
    //             ->whereHas('metier', function ($q) use ($user, $request) {
    //                 $q->where('departement_id', $user->departement_id);
    //                 // Filtre optionnel par métier
    //                 if ($request->has('metier_id')) {
    //                     $q->where('id', $request->metier_id);
    //                 }
    //             });

    //         $apprenants = $query->get();

    //         return response()->json([
    //             'success' => true,
    //             'data' => $apprenants
    //         ]);
    //     }

    //     // Cas 2️⃣ : Chef de métier
    //     if ($user->role === 'chef_metier') {
    //         $apprenants = User::with(['metier', 'livrables', 'appreciations'])
    //             ->where('metier_id', $user->metier_id)
    //             ->get();

    //         return response()->json([
    //             'success' => true,
    //             'data' => $apprenants
    //         ]);
    //     }

    //     return response()->json(['message' => 'Accès non autorisé'], 403);
    // }

    // public function index(Request $request)
    // {

    //     $user = auth()->user();

    //     // Charger les apprenants avec leurs livrables + tache associée
    //     $query = User::with(['livrables.tache']);

    //     // Exemple de filtrage selon le rôle
    //     if ($user->role === 'chef_de_metier') {
    //         $query->where('metier_id', $user->metier_id);
    //     } elseif ($user->role === 'chef_de_departement' && $request->has('metier_id')) {
    //         $query->where('metier_id', $request->metier_id);
    //     } else {
    //         // par défaut, on montre seulement les apprenants
    //         $query->where('role', 'apprenant');
    //     }

    //     $apprenants = User::with('livrables')->where('role', 'apprenant')->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $apprenants
    //     ]);
    // }


//     public function index(Request $request)
// {
//     $user = auth()->user();

//     // Charger les apprenants avec leurs livrables + tache + entreprise
//     $query = User::with(['livrables.tache', 'livrables.apprenant', 'entreprise'])
//                  ->where('role', 'apprenant');

//     // Filtrage selon le rôle
//     if ($user->role === 'chef_de_metier') {
//         $query->where('metier_id', $user->metier_id);
//     } elseif ($user->role === 'chef_de_departement' && $request->has('metier_id')) {
//         $query->where('metier_id', $request->metier_id);
//     }

//     $apprenants = $query->get();

//     return response()->json([
//         'success' => true,
//         'data' => $apprenants
//     ]);
// }



public function index(Request $request)
    {
        $user = auth()->user();

        // Base query pour les apprenants
        $query = User::with([
            'livrables.tache.entreprise', // Livrables avec info tâche + entreprise
            'livrables.tache.stage',      // Si besoin
            'entreprise'                  // Entreprise de l'étudiant
        ])->where('role', 'apprenant');

        // Filtrage selon rôle du user
        if ($user->role === 'chef_de_metier') {
            $query->where('metier_id', $user->metier_id);
        } elseif ($user->role === 'chef_de_departement' && $request->has('metier_id')) {
            $query->where('metier_id', $request->metier_id);
        }

        $apprenants = $query->get()->map(function($apprenant) {
            return [
                'id' => $apprenant->id,
                'name' => $apprenant->name,
                'prenom' => $apprenant->prenom,
                'email' => $apprenant->email,
                'matricule' => $apprenant->matricule,
                'role' => $apprenant->role,
                'metier_id' => $apprenant->metier_id,
                'entreprise' => $apprenant->entreprise ? [
                    'id' => $apprenant->entreprise->id,
                    'nom' => $apprenant->entreprise->nom
                ] : null,
                'livrables' => $apprenant->livrables->map(function($livrable) {
                    return [
                        'id' => $livrable->id,
                        'titre' => $livrable->titre,
                        'description' => $livrable->description,
                        'fichier' => $livrable->fichier,
                        'fichier_url' => $livrable->fichier_url,
                        'statut' => $livrable->statut, // ✅ Statut donné par maître de stage
                        'note' => $livrable->note,
                        'commentaire' => $livrable->commentaire,
                        'tache' => $livrable->tache ? [
                            'id' => $livrable->tache->id,
                            'titre' => $livrable->tache->titre,
                            'statut' => $livrable->tache->statut,
                            'date_echeance' => $livrable->tache->date_echeance,
                            'entreprise' => $livrable->tache->entreprise ? [
                                'id' => $livrable->tache->entreprise->id,
                                'nom' => $livrable->tache->entreprise->nom
                            ] : null
                        ] : null
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $apprenants
        ]);
    }

}
