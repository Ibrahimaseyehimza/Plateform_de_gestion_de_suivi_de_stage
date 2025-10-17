<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Support\Str;
// use App\Mail\InvitationMail;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Mail;
// use App\Http\Requests\RegisterRequest;

// class UserController extends Controller
// {
//      public function store(RegisterRequest $request)
//     {
//         $validated = $request->validated();
//         $currentUser = auth()->user();
//     //Vérification du rôle
//     if ($validated['role'] === User::ROLE_CHEF_METIER) {
//         // Vérifier que le metier n'a pas déjà un chef
//         if (User::where('role', User::ROLE_CHEF_METIER)
//                 ->where('metier_id', $validated['metier_id'])
//                 ->exists()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Ce métier a déjà un chef.'
//             ], 403);
//         }
//     }

//     if ($validated['role'] === User::ROLE_CHEF_DEPARTEMENT) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Impossible de créer un autre chef de département.'
//         ], 403);
//     }


//         // Générer un mot de passe temporaire
//         $temporaryPassword = Str::random(10);

//         // Créer l’utilisateur
//         $user = User::create([
//             'nom' => $validated['nom'],
//             'email' => $validated['email'],
//             'role' => $validated['role'],
//             // 'departement_id' => $validated['departement_id'] ?? null,
//             'metier_id' => $validated['metier_id'] ?? null,
//             'entreprise_id' => $validated['entreprise_id'] ?? null,
//             'entreprise_id' => $validated['matricule'] ?? null,
//             //   'matricule' => $row['matricule'],
//             'password' => Hash::make($temporaryPassword),
//             'must_change_password' => true,
//         ]);

//         // Envoyer l’email avec les identifiants
//         Mail::to($user->email)->queue(new InvitationMail($user, $temporaryPassword));

//         return response()->json([
//             'success' => true,
//             'message' => 'Utilisateur créé avec succès. Un email a été envoyé avec ses identifiants.',
//             'data' => $user
//         ], 201);
//     }
// }












namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Apprenant;

use App\Models\Stage;
use App\Models\CampagneDeStage;
use App\Models\DemandeDeStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ApprenantsImport;

class UserController extends Controller
{
    /**
     * 🧍‍♂️ Liste des utilisateurs (optionnellement filtrés par rôle)
     */
    // public function index(Request $request)
    // {
    //     $role = $request->query('role');
    //     $query = User::with('metier');

    //     if ($role) {
    //         $query->where('role', $role);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $query->get()
    //     ]);
    // }

       // 📋 Récupérer tous les apprenants
        // public function index()
        // {
        //     $apprenants = Apprenant::with('metier', 'maitreDeStage')
        //         ->orderBy('created_at', 'desc')
        //         ->get();

        //     \Log::info('Nombre d\'apprenants trouvés: ' . $apprenants->count());

        //     return response()->json([
        //         'success' => true,
        //         'data' => $apprenants
        //     ], 200);
        // }

         /**
     * 📋 Récupérer tous les apprenants depuis la table users
     * CORRECTION: Au lieu de récupérer depuis la table Apprenant (qui était vide),
     * on récupère maintenant depuis la table User avec le filtre role = 'apprenant'
     * car c'est là que les données sont importées
     */
    public function index()
    {
        // CORRECTION LIGNE 1: Ajouter ->where('role', 'apprenant')
        // Cela récupère SEULEMENT les utilisateurs qui sont des apprenants
        // Avant: Apprenant::with('metier', 'maitreDeStage')
        // Après: User::where('role', 'apprenant')->with('metier')
        $apprenants = User::where('role', 'apprenant')
            ->with('metier')
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('Nombre d\'apprenants trouvés: ' . $apprenants->count());

        return response()->json([
            'success' => true,
            'data' => $apprenants
        ], 200);
    }


    /**
     * ➕ Créer un utilisateur (tous rôles confondus)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'matricule' => 'nullable|string|unique:users',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|in:apprenant,chef_metier,chef_departement,rh,maitre_stage',
            'metier_id' => 'nullable|exists:metiers,id'
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password'] ?? $validated['matricule']),
        ]);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
    }

    /**
     * 🧾 Importation d’apprenants depuis un fichier Excel
     */
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,csv|max:10240'
    //     ]);

    //     try {
    //         Excel::import(new ApprenantsImport, $request->file('file'));

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Importation réussie ✅'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de l\'importation',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function import(Request $request)
    {
        try {
            \Log::info('=== DEBUT IMPORT APPRENANTS ===');
            \Log::info('Has file: ' . ($request->hasFile('file') ? 'OUI' : 'NON'));

            $request->validate([
                'file' => 'required|file|mimes:xlsx,csv|max:10240'
            ]);

            $file = $request->file('file');

            \Log::info('File name: ' . $file->getClientOriginalName());
            \Log::info('File size: ' . $file->getSize());

            Excel::import(new ApprenantsImport, $file);

            $count = User::count();
            \Log::info("Nombre total d'apprenants après import: {$count}");

            return response()->json([
                'success' => true,
                'message' => 'Importation réussie',
                'total_apprenants' => $count
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Supprimer un utilisateur
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé'
        ]);
    }

    // ============================================================
    // 👨‍🎓 PARTIE APPRENANT
    // ============================================================

    /**
     * 🔹 Voir toutes les campagnes ouvertes pour le métier de l’apprenant
     */
    public function campagnesDisponibles(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'apprenant') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $campagnes = CampagneDeStage::where('metier_id', $user->metier_id)
            ->where('statut', 'ouvert')
            ->with('entreprises', 'metier')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $campagnes
        ]);
    }

    /**
     * 📮 Postuler à une entreprise pendant une campagne
     */
    public function postuler(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'campagne_id' => 'required|exists:campagne_de_stages,id',
            'entreprise_id' => 'required|exists:entreprises,id',
            'adresse_1' => 'required|string|max:255',
            'adresse_2' => 'nullable|string|max:255',
        ]);

        $campagne = CampagneDeStage::findOrFail($data['campagne_id']);

        if ($campagne->metier_id !== $user->metier_id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas postuler à une campagne hors de votre métier.'
            ], 403);
        }

        $demande = DemandeDeStage::create([
            'etudiant_id' => $user->id,
            'campagne_id' => $data['campagne_id'],
            'entreprise_id' => $data['entreprise_id'],
            'adresse_1' => $data['adresse_1'],
            'adresse_2' => $data['adresse_2'],
            'statut' => 'en_attente'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande envoyée avec succès 🎯',
            'data' => $demande
        ]);
    }

    /**
     * 📋 Voir mes demandes de stage
     */
    public function mesDemandes(Request $request)
    {
        $user = $request->user();

        $demandes = DemandeDeStage::with(['campagne', 'entreprise'])
            ->where('etudiant_id', $user->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $demandes
        ]);
    }

    /**
     * 🎓 Voir son stage actuel ou validé
     */
    public function monStage(Request $request)
    {
        $user = $request->user();

        $stage = Stage::with(['entreprise', 'tuteur'])
            ->where('etudiant_id', $user->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $stage
        ]);
    }
}
