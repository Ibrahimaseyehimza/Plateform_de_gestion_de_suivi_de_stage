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

            // if ($rh) {
            //     Mail::to($rh->email)->queue(new CampagneNotificationRHMail($rh, $campagne));
            // }
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

    /**
     * 🔹 Récupère les campagnes destinées à une entreprise (RH connecté)
     */
    public function campagnesPourEntreprise(Request $request)
    {
        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        if (!$entrepriseId) {
            return response()->json([
                'success' => false,
                'message' => "Aucune entreprise associée à cet utilisateur RH."
            ], 403);
        }

        // On récupère toutes les campagnes liées à cette entreprise
        $campagnes = CampagneDeStage::whereHas('entreprises', function ($q) use ($entrepriseId) {
                $q->where('entreprise_id', $entrepriseId);
            })
            ->with(['metier', 'entreprises' => function ($q) use ($entrepriseId) {
                $q->where('entreprises.id', $entrepriseId);
            }])
            ->orderByDesc('date_debut')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $campagnes
        ]);
    }

    /**
     * ✅ Le RH accepte une campagne
     */
    public function accepterCampagne(Request $request, $id)
    {
        $request->validate([
            'capacite_max' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        $campagne = CampagneDeStage::findOrFail($id);

        $campagne->entreprises()->updateExistingPivot($entrepriseId, [
            'statut' => 'acceptée',
            'capacite_max' => $request->capacite_max,
            'message_refus' => null,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Campagne acceptée avec succès ✅",
            'data' => [
                'campagne_id' => $id,
                'capacite_max' => $request->capacite_max,
                            // 'capacite_max' => $validated['capacite_max'], // ← CORRECTION ICI

                'statut' => 'acceptee'
            ]
        ]);
    }

    /**
     * ❌ Le RH refuse une campagne
     */
    public function refuserCampagne(Request $request, $id)
    {
        $request->validate([
            'message_refus' => 'required|string|max:500'
        ]);

        $user = $request->user();
        $entrepriseId = $user->entreprise_id;

        $campagne = CampagneDeStage::findOrFail($id);

        $campagne->entreprises()->updateExistingPivot($entrepriseId, [
            'statut' => 'refusee',
            'nb_places' => null,
            'message_refus' => $request->message_refus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Campagne refusée avec justification ❌",
            'data' => [
                'campagne_id' => $id,
                'message_refus' => $request->message_refus,
                'statut' => 'refusee'
            ]
        ]);
    }

    public function validerCampagne(Request $request, $id)
    {
        $request->validate([
            'capacite_max' => 'required|integer|min:1',
            'decision' => 'required|in:accepter,refuser',
            'motif_refus' => 'nullable|string|max:500',
        ]);

        $rh = $request->user();
        $entrepriseId = $rh->entreprise_id;

        $pivot = \DB::table('campagne_stage_entreprise')
            ->where('campagne_de_stage_id', $id)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Aucune campagne trouvée pour cette entreprise'], 404);
        }

        if ($request->decision === 'refuser') {
            \DB::table('campagne_stage_entreprise')
                ->where('id', $pivot->id)
                ->update(['capacite_max' => 0, 'updated_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Campagne refusée par le RH',
                'motif' => $request->motif_refus
            ]);
        }

        // ✅ Accepter la campagne
        \DB::table('campagne_stage_entreprise')
            ->where('id', $pivot->id)
            ->update(['capacite_max' => $request->capacite_max, 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Campagne validée avec succès',
            'capacite_max' => $request->capacite_max,
        ]);
    }





}
