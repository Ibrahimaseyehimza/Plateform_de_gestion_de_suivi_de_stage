<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Illuminate\Http\Request;

class ApprenantTacheController extends Controller
{
    // Liste des tâches assignées à l'apprenant connecté
    public function index(Request $request)
    {
        $apprenant = $request->user(); // Utilisateur connecté

        $taches = Tache::where('apprenant_id', $apprenant->id)
            ->with('maitre') // si tu veux afficher le maitre
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'taches' => $taches
        ]);
    }

    public function getTaches(Request $request)
{
    $user = $request->user(); // Utilisateur connecté

    if ($user->role === 'apprenant') {
        // Si c'est un apprenant, on récupère ses propres tâches
        $taches = Tache::where('apprenant_id', $user->id)
            ->with('maitre') // Inclure les infos du maître
            ->orderBy('created_at', 'desc')
            ->get();
    } elseif ($user->role === 'maitre_stage') {
        // Si c'est un maître de stage, on récupère les tâches de ses apprenants
        $taches = Tache::whereHas('apprenant', function($q) use ($user) {
            $q->where('maitre_id', $user->id);
        })
        ->with('apprenant') // Inclure les infos de l'apprenant
        ->orderBy('created_at', 'desc')
        ->get();
    } else {
        // Tout autre rôle n'a pas accès
        return response()->json([
            'success' => false,
            'message' => 'Accès refusé'
        ], 403);
    }

    return response()->json([
        'success' => true,
        'taches' => $taches
    ]);
}


    // Marquer une tâche comme terminée
    public function terminer($id, Request $request)
    {
        $apprenant = $request->user();
        $tache = Tache::where('id', $id)
            ->where('apprenant_id', $apprenant->id)
            ->first();

        if (!$tache) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche introuvable ou non autorisée'
            ], 404);
        }

        $tache->statut = 'terminee';
        $tache->save();

        return response()->json([
            'success' => true,
            'message' => 'Tâche marquée comme terminée',
            'tache' => $tache
        ]);
    }
}
