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
use Illuminate\Support\Facades\Log;
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
        $rhs = User::with('entreprise')
            ->where('role', 'rh')
            ->get();

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
                'role' => 'rh',
                'entreprise_id' => $request->entreprise_id,
            ]);

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

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $rh->update($updateData);
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
     * Obtenir les entreprises disponibles
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

    /**
     * 🔔 Récupérer les notifications RH
     */
    public function notifications()
    {
        $user = Auth::user();

        if ($user->role !== 'rh') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé'
            ], 403);
        }

        $notifications = $user->notifications()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * 🔔 Marquer une notification comme lue
     */
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
     * 👥 Récupérer la liste des étudiants affectés
     */
    public function etudiantsAffectes(Request $request)
    {
        try {
            $user = $request->user();
            $entrepriseId = $user->entreprise_id;

            Log::info('📥 Récupération étudiants affectés:', [
                'user_id' => $user->id,
                'entreprise_id' => $entrepriseId
            ]);

            if (!$entrepriseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas associé à une entreprise'
                ], 400);
            }

            // Récupérer les demandes avec TOUTES les infos
            $demandes = DemandeDeStage::with([
                'etudiant:id,name,prenom,email',
                'campagne:id,titre,date_debut,date_fin,metier_id',
                'campagne.metier:id,nom'
            ])
            ->where('entreprise_id', $entrepriseId)
            ->whereIn('statut', ['acceptee', 'reorientee'])
            ->orderBy('created_at', 'desc')
            ->get();

            Log::info('✅ Demandes récupérées:', [
                'count' => $demandes->count()
            ]);

            // Grouper par campagne
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
                            'statut' => $demande->statut,
                        ];
                    })
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $parCampagne,
                'total' => $demandes->count()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERREUR etudiantsAffectes:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📤 Soumettre la liste des étudiants au maître de stage (CORRIGÉ)
     */
    public function soumettreAuMaitreStage(Request $request)
    {
        try {
            Log::info('📥 Données reçues pour soumission:', $request->all());

            // Validation
            $validated = $request->validate([
                'maitre_stage_id' => 'required|exists:users,id',
                'campagne_id' => 'nullable|exists:campagne_de_stages,id',
                'etudiants_ids' => 'required|array|min:1',
                'etudiants_ids.*' => 'required|integer|exists:users,id',
                'message' => 'nullable|string|max:1000',
            ]);

            Log::info('✅ Validation réussie:', $validated);

            $user = $request->user();
            $entrepriseId = $user->entreprise_id;

            // Vérifier que le maître de stage a le bon rôle
            $maitreStage = User::find($validated['maitre_stage_id']);
            
            if (!$maitreStage) {
                Log::warning('⚠️ Maître de stage introuvable:', ['id' => $validated['maitre_stage_id']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Maître de stage introuvable'
                ], 404);
            }

            if ($maitreStage->role !== 'maitre_stage') {
                Log::warning('⚠️ Rôle incorrect:', [
                    'user_id' => $maitreStage->id,
                    'role' => $maitreStage->role
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'L\'utilisateur sélectionné n\'est pas un maître de stage'
                ], 400);
            }

            DB::beginTransaction();

            // Créer la soumission
            $soumission = SoumissionMaitreStage::create([
                'entreprise_id' => $entrepriseId,
                'rh_id' => $user->id,
                'maitre_stage_id' => $validated['maitre_stage_id'],
                'etudiants_ids' => json_encode($validated['etudiants_ids']), // Encoder en JSON
                'statut' => $validated['statut'],
            ]);

            Log::info('✅ Soumission créée:', ['id' => $soumission->id]);

            // 🔔 Créer une notification pour le maître de stage
            $maitreStage->notify(new \Illuminate\Notifications\Messages\DatabaseMessage([
                'titre' => '📋 Nouvelle liste d\'étudiants',
                'message' => sprintf(
                    'Le RH %s vous a soumis une liste de %d étudiant(s).',
                    $user->name,
                    count($validated['etudiants_ids'])
                ),
                'type' => 'nouvelle_soumission',
                'entreprise_id' => $entrepriseId
            ]));

            DB::commit();

            Log::info('✅ Soumission terminée avec succès');

            return response()->json([
                'success' => true,
                'message' => '✅ Liste soumise au maître de stage avec succès',
                'data' => $soumission
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Erreur de validation:', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ ERREUR SOUMISSION:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📋 Historique des soumissions
     */
    public function historiqueSoumissions(Request $request)
    {
        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        $soumissions = SoumissionMaitreStage::where('entreprise_id', $entrepriseId)
            ->with([
                'maitreStage:id,name,prenom,email',
                'rh:id,name,prenom'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ajouter les infos des étudiants
        $soumissions = $soumissions->map(function($soumission) {
            $etudiantsIds = is_array($soumission->etudiants_ids) 
                ? $soumission->etudiants_ids 
                : json_decode($soumission->etudiants_ids, true);
                
            $etudiants = User::whereIn('id', $etudiantsIds)
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

        $maitre = $entreprise->maitreStage;

        if (!$maitre) {
            return response()->json(['error' => 'Aucun maître de stage trouvé pour cette entreprise.'], 404);
        }

        $etudiants = $entreprise->etudiants;
        $maitre->notify(new AffectesMaitreNotification($etudiants, $entreprise));

        return response()->json(['success' => true, 'message' => 'Notification envoyée au maître de stage.']);
    }
}