<?php

namespace App\Http\Controllers;

use App\Models\RH;
use App\Models\User;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Models\CampagneDeStage;
use App\Http\Requests\RhRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateRHRequest;

class RhController extends Controller
{
    /**
     * Afficher tous les RH
     */
    public function index()
    {
        // $rhs = User::where('role', 'rh')
        //            ->with(['entreprise'])
        //            ->paginate(10);
         $rhs = User::with('entreprise')
            ->where('role', 'rh')
            ->get();

        //  $rhs = RH::with(['entreprise'])->get();

        return response()->json([
            'success' => true,
            'data' => $rhs
        ], 200);
    }


    /**
     * Créer un nouveau RH
     */
    public function store(RhRequest $request)
    {
        try {
            $rh = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'rh', // Fixé automatiquement
                'entreprise_id' => $request->entreprise_id,
            ]);

            // Charger la relation entreprise pour la réponse
            $rh->load('entreprise');

            return response()->json([
                'success' => true,
                'message' => 'RH créé avec succès',
                'data' => $rh
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du RH',
                'error' => $e->getMessage()
            ], 500);
        }
    }


      /**
     * Afficher un RH spécifique
     */
    public function show($id)
    {
        try {
            $rh = User::where('role', 'rh')
                      ->where('id', $id)
                      ->with('entreprise')
                      ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $rh
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'RH non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }


     /**
     * Modifier un RH existant
     */
    public function update(UpdateRHRequest $request, $id)
    {
        try {
            $rh = User::where('role', 'rh')
                      ->where('id', $id)
                      ->firstOrFail();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'entreprise_id' => $request->entreprise_id,
            ];

            // Mettre à jour le mot de passe seulement si fourni
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $rh->update($updateData);

            // Recharger les relations
            $rh->load('entreprise');

            return response()->json([
                'success' => true,
                'message' => 'RH modifié avec succès',
                'data' => $rh
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du RH',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un RH
     */
    public function destroy($id): JsonResponse
    {
        try {
            $rh = User::where('role', 'rh')
                      ->where('id', $id)
                      ->firstOrFail();

            $rh->delete();

            return response()->json([
                'success' => true,
                'message' => 'RH supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du RH',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les entreprises disponibles pour les listes déroulantes
     */
    public function getEntreprises(): JsonResponse
    {
        $entreprises = Entreprise::select('id', 'nom', 'adresse')
                                 ->orderBy('nom')
                                 ->get();

        return response()->json([
            'success' => true,
            'data' => $entreprises
        ], 200);
    }

    /**
     * Obtenir les RH par entreprise
     */
    public function getByEntreprise($entreprise_id): JsonResponse
    {
        try {
            $rhs = User::where('role', 'rh')
                       ->where('entreprise_id', $entreprise_id)
                       ->with('entreprise')
                       ->get();

            return response()->json([
                'success' => true,
                'data' => $rhs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des RH',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques sur les RH
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_rhs' => User::where('role', 'rh')->count(),
                'rhs_par_entreprise' => User::where('role', 'rh')
                                           ->with('entreprise:id,nom')
                                           ->get()
                                           ->groupBy('entreprise.nom')
                                           ->map->count(),
                'entreprises_sans_rh' => Entreprise::whereDoesntHave('users', function($query) {
                                            $query->where('role', 'rh');
                                         })->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechercher des RH
     */
    public function search($term): JsonResponse
    {
        try {
            $rhs = User::where('role', 'rh')
                       ->where(function($query) use ($term) {
                           $query->where('name', 'like', "%$term%")
                                 ->orWhere('email', 'like', "%$term%");
                       })
                       ->with('entreprise')
                       ->get();

            return response()->json([
                'success' => true,
                'data' => $rhs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexForRh(Request $request)
    {
        $user = $request->user();

        $campagnes = CampagneDeStage::whereHas('entreprises', function ($q) use ($user) {
                $q->where('entreprises.id', $user->entreprise_id);
            })
            ->with(['metier'])
            ->get();

        return response()->json(['success' => true, 'data' => $campagnes]);
    }


    public function validerCampagne(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:accepter,refuser']);
        $user = $request->user();

        DB::table('campagne_stage_entreprise')
            ->where('campagne_de_stage_id', $id)
            ->where('entreprise_id', $user->entreprise_id)
            ->update([
                'statut' => $request->action === 'accepter' ? 'acceptee' : 'refusee',
                'validated_by' => $user->id,
                'validated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => "Campagne {$request->action}e avec succès"
        ]);
    }

}
