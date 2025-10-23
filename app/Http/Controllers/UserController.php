<?php

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
            'telephone' => 'required|string|max:255',
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
            'telephone' => $data['telephone'],
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
