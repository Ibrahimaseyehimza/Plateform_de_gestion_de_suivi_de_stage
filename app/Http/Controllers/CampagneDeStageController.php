<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CampagneDeStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CampagneDeStageController extends Controller
{


     public function index()
    {
        $campagnes = CampagneDeStage::with(['metier', 'entreprises'])->get();

        return response()->json([
            'success' => true,
            'data' => $campagnes
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'titre' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'date_debut' => 'required|date',
    //         'date_fin' => 'required|date|after:date_debut',
    //         'metier_id' => 'required|exists:metiers,id',
    //         'entreprise_ids' => 'required|array',
    //         'entreprise_ids.*' => 'exists:entreprises,id',
    //     ]);

    //     $campagne = CampagneDeStage::create($validated);

    //     foreach ($validated['entreprises'] as $entrepriseId) {
    //         DB::table('campagne_stage_entreprise')->insert([
    //             'campagne_de_stage_id' => $campagne->id,
    //             'entreprise_id' => $entrepriseId,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         // 📨 Envoi du mail au RH de cette entreprise
    //         $rh = User::where('entreprise_id', $entrepriseId)
    //                 ->where('role', 'rh')
    //                 ->first();

    //         if ($rh) {
    //             Mail::to($rh->email)->queue(new CampagneNotificationRHMail($rh, $campagne));
    //         }
    //     }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Campagne créée avec succès et notifications envoyées aux RH.',
    //             'data' => $campagne
    //         ])->setStatusCode(201);

    //     // Lier les entreprises
    //     // $campagne->entreprises()->attach($validated['entreprise_ids']);

    //     // 🚀 Envoi de mail aux apprenants du métier
    //     // $apprenants = User::where('role', 'apprenant')
    //     //                   ->where('metier_id', $validated['metier_id'])
    //     //                   ->get();

    //     // foreach ($apprenants as $apprenant) {
    //     //     Mail::to($apprenant->email)->send(new CampagneCreeeMail($campagne));
    //     // }

    //     // return response()->json([
    //     //     'success' => true,
    //     //     'message' => 'Campagne créée avec succès et notifications envoyées',
    //     //     'data' => $campagne->load('metier', 'entreprises')
    //     // ], 201);
    // }

    public function store(Request $request)
{
    $validated = $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'nullable|string',
        'date_debut' => 'required|date',
        'date_fin' => 'required|date|after:date_debut',
        'metier_id' => 'required|exists:metiers,id',
        'entreprise_ids' => 'required|array',
        'entreprise_ids.*' => 'exists:entreprises,id',
    ]);

    $campagne = CampagneDeStage::create($validated);

    foreach ($validated['entreprise_ids'] as $entrepriseId) { // ✅ corrigé ici
        DB::table('campagne_stage_entreprise')->insert([
            'campagne_de_stage_id' => $campagne->id,
            'entreprise_id' => $entrepriseId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rh = User::where('entreprise_id', $entrepriseId)
                ->where('role', 'rh')
                ->first();

        if ($rh) {
            Mail::to($rh->email)->queue(new CampagneNotificationRHMail($rh, $campagne));
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Campagne créée avec succès et notifications envoyées aux RH.',
        'data' => $campagne
    ], 201);
}

    /**
     * Supprimer une campagne de stage c'est que j'ai ajouter
     */
    public function destroy($id)
    {
        try {
            $campagne = CampagneDeStage::findOrFail($id);
            $campagne->delete();

            return response()->json([
                'success' => true,
                'message' => 'Campagne supprimée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }


      /**
     * Afficher une campagne spécifique
     */
    public function show($id)
    {
        try {
            $campagne = \App\Models\CampagneDeStage::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $campagne
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Campagne non trouvée'
            ], 404);
        }
    }

    public function campagnesOuvertesPourApprenant()
    {
        try {
            \Log::info('🔍 DEBUT - Recherche campagnes ouvertes');

            // ✅ CORRECTION: utiliser 'entreprises' au pluriel
            $campagnes = CampagneDeStage::where('statut', 'ouverte')
                ->with(['entreprises', 'metier'])  // ← CORRIGÉ ICI
                ->get();

            \Log::info('📊 Campagnes ouvertes trouvées: ' . $campagnes->count());

            if ($campagnes->isEmpty()) {
                \Log::info('ℹ️ Aucune campagne ouverte trouvée');
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Aucune campagne ouverte trouvée'
                ], 200);
            }

            \Log::info('✅ SUCCES - Campagnes récupérées');
            return response()->json([
                'success' => true,
                'data' => $campagnes,
                'count' => $campagnes->count(),
                'message' => 'Campagnes récupérées avec succès'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('❌ ERREUR dans campagnesOuvertesPourApprenant: ' . $e->getMessage());
            \Log::error('📁 File: ' . $e->getFile());
            \Log::error('📍 Line: ' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }


    public function campagnesDisponibles(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'apprenant') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $campagnes = CampagneDeStage::where('metier_id', $user->metier_id)
            ->where('statut', 'ouverte')
            ->with('entreprises', 'metier')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $campagnes
        ]);
    }


    public function campagnesPourEntreprise(Request $request)
    {
        $user = $request->user();

        $campagnes = CampagneDeStage::whereHas('entreprises', function ($q) use ($user) {
            $q->where('entreprises.id', $user->entreprise_id);
        })
        ->with(['metier', 'chefDepartement'])
        ->orderBy('date_debut', 'desc')
        ->get();

        return response()->json(['success' => true, 'data' => $campagnes]);
    }

    public function accepterCampagne($id, Request $request)
    {
        $user = $request->user();
        DB::table('campagne_stage_entreprise')
            ->where('campagne_de_stage_id', $id)
            ->where('entreprise_id', $user->entreprise_id)
            ->update(['statut' => 'acceptée']);
        return response()->json(['success' => true, 'message' => 'Campagne acceptée ✅']);
    }

    public function refuserCampagne($id, Request $request)
    {
        $user = $request->user();
        DB::table('campagne_stage_entreprise')
            ->where('campagne_de_stage_id', $id)
            ->where('entreprise_id', $user->entreprise_id)
            ->update(['statut' => 'refusée']);
        return response()->json(['success' => true, 'message' => 'Campagne refusée ❌']);
    }




}
