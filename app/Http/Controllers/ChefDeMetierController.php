<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stage;
use App\Models\Metier;
use App\Models\Entreprise;
use App\Models\ChefDeMetier;
use Illuminate\Http\Request;
use App\Models\DemandeDeStage;
use App\Models\CampagneDeStage;
use Illuminate\Support\Facades\DBx;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AffectesNotification;
use Illuminate\Support\Facades\Notification;

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

    /**
     * 📊 Statistiques globales du Chef de Métier
     */
    public function statistiques(Request $request)
    {
        $user = $request->user();
        $metierId = $user->metier_id;

        $totalApprenants = User::where('role', 'apprenant')
            ->where('metier_id', $metierId)
            ->count();

        $totalDemandes = DemandeDeStage::whereHas('campagne', function ($q) use ($metierId) {
            $q->where('metier_id', $metierId);
        })->count();

        $stagesValides = DemandeDeStage::whereHas('campagne', function ($q) use ($metierId) {
            $q->where('metier_id', $metierId);
        })->where('statut', 'valide')->count();

        $entreprisesSaturees = DB::table('campagne_stage_entreprise')
            ->join('campagne_de_stages', 'campagne_de_stages.id', '=', 'campagne_stage_entreprise.campagne_de_stage_id')
            ->where('campagne_de_stages.metier_id', $metierId)
            ->whereColumn('places_occupees', '>=', 'capacite_max')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalApprenants' => $totalApprenants,
                'totalDemandes' => $totalDemandes,
                'stagesValides' => $stagesValides,
                'entreprisesSaturees' => $entreprisesSaturees
            ]
        ]);
    }

    /**
     * 📋 Liste des demandes de stage liées au métier
     */
    public function demandes(Request $request)
    {
        $user = $request->user();
        $metierId = $user->metier_id;

        $demandes = DemandeDeStage::with(['etudiant', 'campagne', 'entreprise'])
            ->whereHas('campagne', function ($q) use ($metierId) {
                $q->where('metier_id', $metierId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $demandes]);
    }

    /**
     * 🏢 Entreprises disponibles (ayant encore des places)
     */
    public function entreprisesDisponibles(Request $request)
    {
        $user = $request->user();
        $metierId = $user->metier_id;

        $entreprises = DB::table('campagne_stage_entreprise')
            ->join('campagne_de_stages', 'campagne_de_stages.id', '=', 'campagne_stage_entreprise.campagne_de_stage_id')
            ->join('entreprises', 'entreprises.id', '=', 'campagne_stage_entreprise.entreprise_id')
            ->where('campagne_de_stages.metier_id', $metierId)
            ->where('campagne_stage_entreprise.statut', 'acceptée')
            ->whereColumn('places_occupees', '<', 'capacite_max')
            ->select(
                'entreprises.id',
                'entreprises.nom',
                DB::raw('capacite_max - places_occupees as places_restantes')
            )
            ->get();

        return response()->json(['success' => true, 'data' => $entreprises]);
    }

    // ChefDeMetierController.php

    public function accepterEtAffecterEtudiant(Request $request, $demandeId)
    {
        $validated = $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id'
        ]);

        DB::beginTransaction();

        try {
            $demande = DemandeDeStage::findOrFail($demandeId);

            // 1️⃣ Vérifier que l'entreprise a accepté la campagne
            $pivot = DB::table('campagne_stage_entreprise')
                ->where('campagne_de_stage_id', $demande->campagne_id)
                ->where('entreprise_id', $validated['entreprise_id'])
                ->where('statut', 'acceptée')
                ->first();

            if (!$pivot) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cette entreprise n\'a pas accepté la campagne'
                ], 400);
            }

            // 2️⃣ Vérifier la disponibilité des places
            if ($pivot->places_occupees >= $pivot->capacite_max) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Capacité maximale atteinte pour cette entreprise',
                    'details' => [
                        'entreprise' => $pivot->entreprise_id,
                        'capacite_max' => $pivot->capacite_max,
                        'places_occupees' => $pivot->places_occupees
                    ]
                ], 400);
            }

            // 3️⃣ Mettre à jour la demande
            $demande->update([
                'entreprise_id' => $validated['entreprise_id'],
                'statut' => 'acceptee'
            ]);

            // 4️⃣ Incrémenter places_occupees
            DB::table('campagne_stage_entreprise')
                ->where('campagne_de_stage_id', $demande->campagne_id)
                ->where('entreprise_id', $validated['entreprise_id'])
                ->increment('places_occupees');

            DB::commit();

            // 5️⃣ (Optionnel) Envoyer une notification au RH
            // $rh = User::where('entreprise_id', $validated['entreprise_id'])
            //           ->where('role', 'rh')
            //           ->first();
            // Mail::to($rh->email)->send(new NouvelEtudiantAffecte($demande));

            return response()->json([
                'success' => true,
                'message' => '✅ Étudiant accepté et affecté avec succès',
                'data' => [
                    'demande' => $demande,
                    'places_restantes' => $pivot->capacite_max - ($pivot->places_occupees + 1)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorienterEtudiant(Request $request, $demandeId)
    {
        $validated = $request->validate([
            'nouvelle_entreprise_id' => 'required|exists:entreprises,id'
        ]);

        DB::beginTransaction();

        try {
            $demande = DemandeDeStage::findOrFail($demandeId);
            $ancienneEntrepriseId = $demande->entreprise_id;

            // Si l'étudiant était déjà accepté, libérer la place
            if ($demande->statut === 'acceptee') {
                DB::table('campagne_stage_entreprise')
                    ->where('campagne_de_stage_id', $demande->campagne_id)
                    ->where('entreprise_id', $ancienneEntrepriseId)
                    ->decrement('places_occupees');
            }

            // Vérifier la disponibilité dans la nouvelle entreprise
            $pivot = DB::table('campagne_stage_entreprise')
                ->where('campagne_de_stage_id', $demande->campagne_id)
                ->where('entreprise_id', $validated['nouvelle_entreprise_id'])
                ->where('statut', 'acceptée')
                ->first();

            if (!$pivot || $pivot->places_occupees >= $pivot->capacite_max) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => '❌ Pas de place disponible dans cette entreprise'
                ], 400);
            }

            // Réorienter
            $demande->update([
                'entreprise_id' => $validated['nouvelle_entreprise_id'],
                'statut' => 'reorientee'
            ]);

            // Occuper une place dans la nouvelle entreprise
            DB::table('campagne_stage_entreprise')
                ->where('campagne_de_stage_id', $demande->campagne_id)
                ->where('entreprise_id', $validated['nouvelle_entreprise_id'])
                ->increment('places_occupees');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Étudiant réorienté avec succès'
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
     * ⚙️ Affecter un apprenant à une entreprise
     */
    public function affecter(Request $request, $id)
    {
        $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id'
        ]);

        $demande = DemandeDeStage::findOrFail($id);
        $demande->entreprise_id = $request->entreprise_id;
        $demande->statut = 'valide';
        $demande->save();

        // Mise à jour du compteur d’occupation
        DB::table('campagne_stage_entreprise')
            ->where('campagne_de_stage_id', $demande->campagne_id)
            ->where('entreprise_id', $request->entreprise_id)
            ->increment('places_occupees');

        return response()->json(['success' => true, 'message' => 'Apprenant affecté avec succès.']);
    }

    public function entreprisesAvecEtudiants()
    {
        $entreprises = \App\Models\Entreprise::with([
            'etudiantsAffectes.etudiant',
            'etudiantsAffectes.campagne'
        ])->get();

        $data = $entreprises->map(function ($e) {
            return [
                'id' => $e->id,
                'nom' => $e->nom,
                'email_rh' => optional($e->rh)->email,
                'etudiants' => $e->etudiantsAffectes->map(function ($d) {
                    return [
                        'id' => $d->etudiant->id,
                        'nom' => $d->etudiant->name,
                        'prenom' => $d->etudiant->prenom,
                        'filiere' => $d->etudiant->filiere ?? null,
                        'campagne' => $d->campagne->titre ?? null,
                        'statut' => $d->statut,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function envoyerListeEtudiantsAuRh($entrepriseId)
    {
        $entreprise = \App\Models\Entreprise::with(['etudiantsAffectes.etudiant', 'rh'])->findOrFail($entrepriseId);

        $rh = $entreprise->rh;

        if (!$rh) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun RH associé à cette entreprise.'
            ], 400);
        }

        $etudiants = $entreprise->etudiantsAffectes
            ->whereIn('statut', ['acceptee', 'reorientee'])
            ->map(fn($d) => $d->etudiant->name . ' ' . $d->etudiant->prenom)
            ->toArray();

        $message = "Voici la liste des étudiants affectés à votre entreprise :\n- " . implode("\n- ", $etudiants);

        // 🔔 Créer une notification unique pour le RH
        \App\Models\Notification::create([
            'user_id' => $rh->id,
            'type' => 'liste_etudiants',
            'titre' => '📋 Liste des étudiants affectés',
            'message' => $message,
            'entreprise_id' => $entrepriseId,
            'lue' => false,
        ]);

        // (Optionnel) 📧 Envoyer un email au RH
        // Mail::to($rh->email)->send(new ListeEtudiantsAffectesMail($entreprise, $etudiants));

        return response()->json([
            'success' => true,
            'message' => '✅ Liste envoyée au RH avec succès',
        ]);
    }

    public function affectations()
    {
        $affectations = DemandeDeStage::with(['etudiant', 'entreprise'])
            ->whereIn('statut', ['acceptee', 'reorientee'])
            ->get();

        $entreprises = Entreprise::withCount(['stages as total_apprenants' => function ($q) {
            $q->where('statut', 'acceptée');
        }])->get(['id', 'nom', 'total_apprenants']);

        return response()->json([
            'data' => $affectations,
            'entreprises' => $entreprises
        ]);
    }

     public function envoyerRh(Request $request)
    {
        // ✅ On récupère les étudiants affectés
        $affectations = DemandeDeStage::with(['etudiant', 'entreprise'])
            ->whereIn('statut', ['acceptee', 'reorientee'])
            ->get();

        if ($affectations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune affectation trouvée à envoyer.'
            ], 404);
        }

        // ✅ Construire le contenu de la notification
        $message = "📋 Liste des étudiants affectés :\n\n";
        foreach ($affectations as $demande) {
            $message .= "- {$demande->etudiant->prenom} {$demande->etudiant->nom} → {$demande->entreprise->nom}\n";
        }

        // ✅ Récupérer tous les RH (ou un seul selon ta logique)
        $rhs = User::where('role', 'rh')->get();

        if ($rhs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur RH trouvé.'
            ], 404);
        }

        // ✅ Envoyer la notification
        Notification::send($rhs, new AffectesNotification($message));

        return response()->json([
            'success' => true,
            'message' => 'Liste envoyée avec succès au RH 🎉'
        ]);
    }




}
