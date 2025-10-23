<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stage;
use App\Models\DemandeDeStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class MaitreStageController extends Controller
{
    /**
     * Liste des maîtres de stage (pour RH).
     */
    public function index(Request $request)
    {
        try {
            // Optionnel : filtrer / paginer selon $request
            $maitres = User::where('role', 'maitre_stage')
                ->select('id', 'name', 'prenom', 'email', 'telephone', 'entreprise_id', 'created_at')
                ->with('entreprise:id,nom') // si relation entreprise existe
                ->get();

            return response()->json([
                'success' => true,
                'data' => $maitres
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des maîtres de stage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function notificationsAffectations(Request $request)
    {
        $maitreStage = $request->user();

        // Exemple : récupérer les affectations des apprenants liés à ce maître
        $affectations = Affectation::with('apprenant', 'campagne')
            ->where('maitre_stage_id', $maitreStage->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $affectations
        ]);
    }

    /**
     * Créer un maître de stage (store) — utilisé par RH.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:30',
            'entreprise_id' => 'nullable|integer|exists:entreprises,id',
            'password' => 'nullable|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $pwd = $data['password'] ?? bcrypt(str()->random(10)); // mot de passe temporaire si non fourni
            $user = User::create([
                'name' => $data['name'],
                'prenom' => $data['prenom'] ?? null,
                'email' => $data['email'],
                'telephone' => $data['telephone'] ?? null,
                'entreprise_id' => $data['entreprise_id'] ?? null,
                'role' => 'maitre_stage',
                'password' => Hash::make($pwd),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Maître de stage créé avec succès',
                'data' => $user,
                // 'plain_password' => $pwd // n'envoyer que si nécessaire / sécurisé
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un maître de stage (show)
     */
    public function show($id)
    {
        try {
            $user = User::where('role', 'maitre_stage')->with('entreprise:id,nom')->findOrFail($id);
            return response()->json(['success' => true, 'data' => $user]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maître de stage introuvable',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mettre à jour un maître de stage (update)
     */
    public function update(Request $request, $id)
    {
        try {
            $maitre = User::where('role', 'maitre_stage')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'prenom' => 'nullable|string|max:255',
                'email' => ['sometimes','required','email', Rule::unique('users')->ignore($maitre->id)],
                'telephone' => 'nullable|string|max:30',
                'entreprise_id' => 'nullable|integer|exists:entreprises,id',
                'password' => 'nullable|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $maitre->update($data);

            return response()->json(['success' => true, 'message' => 'Mis à jour effectuée', 'data' => $maitre]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur mise à jour', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un maître de stage (destroy)
     */
    public function destroy($id)
    {
        try {
            $maitre = User::where('role', 'maitre_stage')->findOrFail($id);
            $maitre->delete();

            return response()->json(['success' => true, 'message' => 'Maître de stage supprimé']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur suppression', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retourne les stages supervisés par le maître de stage connecté.
     */
    public function getStages(Request $request)
    {
        try {
            $user = $request->user();

            $stages = Stage::where('maitre_stage_id', $user->id)
                ->with(['etudiant:id,name,prenom', 'entreprise:id,nom', 'campagne:id,nom'])
                ->get();

            return response()->json(['success' => true, 'data' => $stages]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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

            return response()->json(['success' => true, 'message' => 'Note enregistrée']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Liste des étudiants affectés à l'entreprise du maître (déjà existante).
     */
    public function etudiantsAffectes(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'maitre_stage') {
            return response()->json(['success' => false, 'message' => 'Accès refusé. Vous devez être maître de stage.'], 403);
        }

        if (!$user->entreprise_id) {
            return response()->json(['success' => false, 'message' => 'Aucune entreprise associée à ce maître de stage.'], 400);
        }

        $demandes = DemandeDeStage::with(['etudiant:id,name,prenom,email,telephone', 'campagne:id,nom'])
            ->where('entreprise_id', $user->entreprise_id)
            ->where('statut', 'acceptee')
            ->get();

        $etudiants = $demandes->map(function ($demande) {
            return [
                'id' => $demande->etudiant->id ?? null,
                'nom' => $demande->etudiant->name ?? null,
                'prenom' => $demande->etudiant->prenom ?? null,
                'email' => $demande->etudiant->email ?? null,
                'telephone' => $demande->etudiant->telephone ?? null,
                'campagne' => $demande->campagne->nom ?? 'N/A',
                'adresse_1' => $demande->adresse_1 ?? null,
                'adresse_2' => $demande->adresse_2 ?? null,
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
