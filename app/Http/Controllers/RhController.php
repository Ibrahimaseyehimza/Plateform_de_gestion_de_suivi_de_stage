<?php

namespace App\Http\Controllers;

use App\Models\RH;
use App\Models\User;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Models\DemandeDeStage;
use App\Models\CampagneDeStage;
use App\Http\Requests\RhRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\SoumissionMaitreStage;
use App\Http\Requests\UpdateRHRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AffectesMaitreNotification;

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


    // 🔹 Récupérer les notifications RH
    public function notifications()
    {
        $user = Auth::user();

        // Vérification du rôle RH
        if ($user->role !== 'rh') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé'
            ], 403);
        }

        // Notifications triées (les plus récentes d'abord)
        $notifications = $user->notifications()->latest()->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * 🔔 Récupérer toutes les notifications avec détails des étudiants
     */
    // public function notifications(Request $request)
    // {
    //     $user = $request->user();

    //     $notifications = Notification::where('user_id', $user->id)
    //         ->with([
    //             'etudiant:id,name,prenom,email,telephone,adresse', // Infos complètes
    //             'demande.campagne:id,titre,date_debut,date_fin',
    //             'entreprise:id,nom'
    //         ])
    //         ->orderBy('lue', 'asc') // Non lues en premier
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $notifications,
    //         'non_lues' => $notifications->where('lue', false)->count()
    //     ]);
    // }

//     public function notifications(Request $request)
// {
//     $user = Auth::user();

//     // ✅ 1. Vérification du rôle (seuls les RH ont accès ici, sinon adapte)
//     if ($user->role !== 'rh') {
//         return response()->json([
//             'success' => false,
//             'message' => 'Accès refusé — réservé aux RH'
//         ], 403);
//     }

//     // ✅ 2. Récupération des notifications liées à cet utilisateur
//     $notifications = Notification::where('user_id', $user->id)
//         ->with([
//             // Relations détaillées
//             'etudiant:id,name,prenom,email,telephone,adresse',
//             'demande.campagne:id,titre,date_debut,date_fin',
//             'entreprise:id,nom'
//         ])
//         ->orderBy('lue', 'asc') // Non lues d'abord
//         ->orderBy('created_at', 'desc') // Plus récentes ensuite
//         ->get();

//     // ✅ 3. Calcul des non lues
//     $nonLuesCount = $notifications->where('lue', false)->count();

//     // ✅ 4. Réponse unifiée
//     return response()->json([
//         'success' => true,
//         'message' => 'Notifications récupérées avec succès',
//         'data' => $notifications,
//         'non_lues' => $nonLuesCount
//     ]);
// }


    // 🔹 Marquer une notification comme lue
    public function marquerCommeLue($id)
    {
        $user = Auth::user();

        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification introuvable'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue.'
        ]);
    }

    /**
     * 👥 Récupérer la liste COMPLÈTE des étudiants affectés avec toutes leurs infos
     */
    public function etudiantsAffectes(Request $request)
    {
        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        if (!$entrepriseId) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas associé à une entreprise'
            ], 400);
        }

        // Récupérer toutes les demandes acceptées avec TOUTES les infos de l'étudiant
        $demandes = DemandeDeStage::with([
            'etudiant' => function($query) {
                $query->select('id', 'name', 'prenom', 'email', 'telephone', 'adresse', 'date_naissance', 'niveau_etude');
            },
            'campagne:id,titre,date_debut,date_fin,metier_id',
            'campagne.metier:id,nom'
        ])
        ->where('entreprise_id', $entrepriseId)
        ->whereIn('statut', ['acceptee', 'reorientee'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Grouper par campagne pour faciliter la soumission
        $parCampagne = $demandes->groupBy('campagne_id')->map(function($group) {
            return [
                'campagne' => $group->first()->campagne,
                'etudiants' => $group->map(function($demande) {
                    return [
                        'demande_id' => $demande->id,
                        'etudiant_id' => $demande->etudiant->id,
                        'nom' => $demande->etudiant->name,
                        'prenom' => $demande->etudiant->prenom,
                        'email' => $demande->etudiant->email,
                        'telephone' => $demande->etudiant->telephone,
                        'adresse' => $demande->etudiant->adresse,
                        'date_naissance' => $demande->etudiant->date_naissance,
                        'niveau_etude' => $demande->etudiant->niveau_etude,
                        'statut' => $demande->statut,
                        'adresse_stage_1' => $demande->adresse_1,
                        'adresse_stage_2' => $demande->adresse_2,
                    ];
                })
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $parCampagne,
            'total' => $demandes->count()
        ]);
    }

    /**
     * 📤 Soumettre la liste des étudiants au maître de stage
     */
    public function soumettreAuMaitreStage(Request $request)
    {
        $validated = $request->validate([
            'maitre_stage_id' => 'required|exists:users,id',
            'campagne_id' => 'required|exists:campagne_de_stages,id',
            'etudiants_ids' => 'required|array',
            'etudiants_ids.*' => 'exists:users,id',
            'message' => 'nullable|string|max:1000'
        ]);

        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        // Vérifier que le maître de stage existe et a le bon rôle
        $maitreStage = User::find($validated['maitre_stage_id']);
        if ($maitreStage->role !== 'maitre_stage') {
            return response()->json([
                'success' => false,
                'message' => 'L\'utilisateur sélectionné n\'est pas un maître de stage'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Créer la soumission
            $soumission = SoumissionMaitreStage::create([
                'entreprise_id' => $entrepriseId,
                'rh_id' => $user->id,
                'maitre_stage_id' => $validated['maitre_stage_id'],
                'campagne_id' => $validated['campagne_id'],
                'etudiants_ids' => $validated['etudiants_ids'],
                'message' => $validated['message'],
                'statut' => 'en_attente'
            ]);

            // 🔔 Créer une notification pour le maître de stage
            Notification::create([
                'user_id' => $validated['maitre_stage_id'],
                'type' => 'nouvelle_soumission',
                'titre' => '📋 Nouvelle liste d\'étudiants à superviser',
                'message' => sprintf(
                    'Le RH %s %s vous a soumis une liste de %d étudiant(s) pour la campagne.',
                    $user->name,
                    $user->prenom,
                    count($validated['etudiants_ids'])
                ),
                'entreprise_id' => $entrepriseId,
                'lue' => false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Liste soumise au maître de stage avec succès',
                'data' => $soumission
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📋 Voir l'historique des soumissions
     */
    public function historiqueSoumissions(Request $request)
    {
        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        $soumissions = SoumissionMaitreStage::where('entreprise_id', $entrepriseId)
            ->with([
                'maitreStage:id,name,prenom,email',
                'campagne:id,titre',
                'rh:id,name,prenom'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ajouter les infos des étudiants
        $soumissions = $soumissions->map(function($soumission) {
            $etudiants = User::whereIn('id', $soumission->etudiants_ids)
                ->select('id', 'name', 'prenom', 'email')
                ->get();

            $soumission->etudiants = $etudiants;
            return $soumission;
        });

        return response()->json([
            'success' => true,
            'data' => $soumissions
        ]);
    }

    /**
     * 📊 Liste des maîtres de stage disponibles
     */
    public function maitresStageDisponibles(Request $request)
    {
        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        // Récupérer les maîtres de stage de l'entreprise
        $maitresStage = User::where('entreprise_id', $entrepriseId)
            ->where('role', 'maitre_stage')
            ->select('id', 'name', 'prenom', 'email', 'telephone')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $maitresStage
        ]);
    }


    public function notifierMaitreStage($entrepriseId)
    {
        $entreprise = Entreprise::with(['maitreStage', 'etudiants'])->findOrFail($entrepriseId);

        // Le maitre de stage lié à cette entreprise
        $maitre = $entreprise->maitreStage; // Assure-toi d’avoir une relation "maitreStage()" dans le modèle Entreprise

        if (!$maitre) {
            return response()->json(['error' => 'Aucun maître de stage trouvé pour cette entreprise.'], 404);
        }

        $etudiants = $entreprise->etudiants; // Relation "etudiants()" à créer aussi

        $maitre->notify(new AffectesMaitreNotification($etudiants, $entreprise));

        return response()->json(['success' => true, 'message' => 'Notification envoyée au maître de stage.']);
    }

}
