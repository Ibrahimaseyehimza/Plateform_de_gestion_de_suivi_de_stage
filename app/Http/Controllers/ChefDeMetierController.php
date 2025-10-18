<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stage;
use App\Models\Metier;
use App\Models\Entreprise;
use App\Models\ChefDeMetier;
use Illuminate\Http\Request;
use App\Models\CampagneDeStage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ChefDeMetierController extends Controller
{
    // Récupérer tous les chefs de métier
    public function index()
    {
        $chefs = ChefDeMetier::with('metier')->get();

        return response()->json([
            'success' => true,
            'data' => $chefs
        ]);
    }

    // public function index()
    // {
    //     $metiers = Metier::all();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $metiers
    //     ]);
    // }


    // Créer un chef de métier
    // public function store(Request $request)
    // {
    //     // Validation
    //     $validator = Validator::make($request->all(), [
    //         'nom' => 'required|string|max:255',
    //         'prenom' => 'required|string|max:255',
    //         'email' => 'required|email|unique:chef_de_metiers,email',
    //         'password' => 'required|string|min:8|confirmed',
    //         'metier_id' => 'required|exists:metiers,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         // Créer le chef de métier
    //         $chef = ChefDeMetier::create([
    //             'nom' => $request->nom,
    //             'prenom' => $request->prenom,
    //             'email' => $request->email,
    //             'metier_id' => $request->metier_id
    //         ]);

    //         // Créer l'utilisateur associé dans la table users
    //         User::create([
    //             'name' => $request->prenom . ' ' . $request->nom,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //             'role' => 'chef_metier'
    //         ]);

    //         // Recharger avec la relation
    //         $chef->load('metier');

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Chef de métier créé avec succès',
    //             'data' => $chef
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de la création',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


//     public function store(Request $request)
// {
//     // Validation
//     $validator = Validator::make($request->all(), [
//         'nom' => 'required|string|max:255',
//         'prenom' => 'required|string|max:255',
//         'email' => 'required|email|unique:chef_de_metiers,email|unique:users,email',
//         'password' => 'required|string|min:8|confirmed',
//         'metier_id' => 'required|exists:metiers,id'
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     try {
//         \DB::beginTransaction();

//         // Créer l'utilisateur d'abord
//         $user = User::create([
//             'name' => $request->prenom . ' ' . $request->nom,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'role' => 'chef_metier'
//         ]);

//         // Créer le chef de métier AVEC le password
//         $chef = ChefDeMetier::create([
//             'nom' => $request->nom,
//             'prenom' => $request->prenom,
//             'email' => $request->email,
//             'password' => Hash::make($request->password), // ✅ AJOUTER CETTE LIGNE
//             'metier_id' => $request->metier_id
//         ]);

//         \DB::commit();

//         // Recharger avec la relation
//         $chef->load('metier');

//         return response()->json([
//             'success' => true,
//             'message' => 'Chef de métier créé avec succès',
//             'data' => $chef
//         ], 201);

//     } catch (\Illuminate\Database\QueryException $e) {
//         \DB::rollBack();

//         if ($e->getCode() == 23000) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Cet email existe déjà',
//                 'error' => 'Email déjà utilisé'
//             ], 409);
//         }

//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de la création',
//             'error' => $e->getMessage()
//         ], 500);

//     } catch (\Exception $e) {
//         \DB::rollBack();

//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de la création',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }


public function store(Request $request)
{
    // ✅ Vérification manuelle au début
    if (User::where('email', $request->email)->exists() ||
        ChefDeMetier::where('email', $request->email)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Cet email existe déjà',
            'error' => 'Email déjà utilisé'
        ], 409);
    }

    // Validation sans unique:users
    $validator = Validator::make($request->all(), [
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|email',  // ✅ Pas de unique ici
        'password' => 'required|string|min:8|confirmed',
        'metier_id' => 'required|exists:metiers,id'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        \DB::beginTransaction();

        $user = User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'chef_metier',
            'metier_id' => $request->metier_id  // ✅ Ajoutez ceci
        ]);

        $chef = ChefDeMetier::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'metier_id' => $request->metier_id
        ]);

        \DB::commit();

        $chef->load('metier');

        return response()->json([
            'success' => true,
            'message' => 'Chef de métier créé avec succès',
            'data' => $chef
        ], 201);

    } catch (\Exception $e) {
        \DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Supprimer un chef de métier
    public function destroy($id)
    {
        try {
            $chef = ChefDeMetier::findOrFail($id);

            // Supprimer aussi l'utilisateur associé
            User::where('email', $chef->email)->delete();

            $chef->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chef de métier supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Récupérer la liste des métiers
    public function getMetiers()
    {
        $metiers = Metier::all();

        return response()->json([
            'success' => true,
            'data' => $metiers
        ]);
    }

    // Récupérer les chefs d'un métier spécifique
    public function getByMetier($metier_id)
    {
        $chefs = ChefDeMetier::where('metier_id', $metier_id)
                             ->with('metier')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => $chefs
        ]);
    }



    public function campagnes()
    {
        $user = auth()->user();

        if ($user->role !== 'chef_metier') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $campagnes = CampagneDeStage::with('entreprises')
            ->where('metier_id', $user->metier_id)
            ->get();

        return response()->json(['success' => true, 'data' => $campagnes]);
    }

    public function entreprises()
    {
        $user = auth()->user();

        $entreprises = Entreprise::where('metier_id', $user->metier_id)->get();

        return response()->json(['success' => true, 'data' => $entreprises]);
    }

    public function stages()
    {
        $user = auth()->user();

        $stages = Stage::with(['etudiant', 'entreprise', 'campagne'])
            ->whereHas('campagne', function ($q) use ($user) {
                $q->where('metier_id', $user->metier_id);
            })
            ->get();

        return response()->json(['success' => true, 'data' => $stages]);
    }
}
