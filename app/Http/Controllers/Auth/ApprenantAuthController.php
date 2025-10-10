<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApprenantAuthController extends Controller
{
    /**
     * Connexion d'un apprenant avec email et matricule
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'matricule' => 'required|string',
        ]);

        // Chercher l'apprenant par email
        $apprenant = Apprenant::where('email', $request->email)->first();

        // Vérifier si l'apprenant existe et si le matricule correspond
        if (!$apprenant || !Hash::check($request->matricule, $apprenant->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou matricule incorrect'
            ], 401);
        }

        // Créer un token d'authentification
        $token = $apprenant->createToken('apprenant-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $apprenant->nom,
                'prenom' => $apprenant->prenom,
                'email' => $apprenant->email,
                'matricule' => $apprenant->matricule,
                'metier' => $apprenant->metier,
            ]
        ], 200);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ], 200);
    }

    /**
     * Récupérer les informations de l'apprenant connecté
     */
    public function me(Request $request)
    {
        $apprenant = $request->user()->load('metier', 'maitreDeStage');

        return response()->json([
            'success' => true,
            'data' => $apprenant
        ], 200);
    }

    /**
     * Changer le mot de passe (matricule)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'ancien_matricule' => 'required|string',
            'nouveau_matricule' => 'required|string|min:4',
        ]);

        $apprenant = $request->user();

        // Vérifier l'ancien matricule
        if (!Hash::check($request->ancien_matricule, $apprenant->password)) {
            return response()->json([
                'success' => false,
                'message' => 'L\'ancien matricule est incorrect'
            ], 401);
        }

        // Mettre à jour avec le nouveau matricule
        $apprenant->update([
            'matricule' => $request->nouveau_matricule,
            'password' => Hash::make($request->nouveau_matricule),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Matricule modifié avec succès'
        ], 200);
    }
}
