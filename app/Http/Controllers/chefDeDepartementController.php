<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandeDeStage;
use Illuminate\Support\Facades\DB;

class chefDeDepartementController extends Controller
{

public function getEtudiantsAffectes(Request $request)
{
    $user = $request->user();

    if (!$user->entreprise_id) {
        return response()->json([
            'success' => false,
            'message' => "Aucune entreprise associée à ce maître de stage. Veuillez contacter l'administrateur.",
            'user_id' => $user->id,
            'user_name' => $user->name
        ], 404);
    }

    $etudiants = DB::table('demande_de_stages')
        ->join('users', 'users.id', '=', 'demande_de_stages.etudiant_id')
        ->where('demande_de_stages.entreprise_id', $user->entreprise_id)
        ->where('demande_de_stages.statut', 'acceptee')
        ->select(
            'users.id',
            'users.name as nom',
            'users.prenom',
            'users.email',
            'demande_de_stages.adresse_1',
            'demande_de_stages.adresse_2',
            'demande_de_stages.statut'
        )
        ->get();

    return response()->json([
        'success' => true,
        'entreprise' => $user->entreprise->nom ?? 'N/A',
        'nombre_etudiants' => $etudiants->count(),
        'etudiants' => $etudiants
    ]);
}


}
