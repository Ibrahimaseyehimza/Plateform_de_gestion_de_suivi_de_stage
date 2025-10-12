<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CampagneDeStage;
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

        // Lier les entreprises
        $campagne->entreprises()->attach($validated['entreprise_ids']);

        // 🚀 Envoi de mail aux apprenants du métier
        $apprenants = User::where('role', 'apprenant')
                          ->where('metier_id', $validated['metier_id'])
                          ->get();

        foreach ($apprenants as $apprenant) {
            Mail::to($apprenant->email)->send(new CampagneCreeeMail($campagne));
        }

        return response()->json([
            'success' => true,
            'message' => 'Campagne créée avec succès et notifications envoyées',
            'data' => $campagne->load('metier', 'entreprises')
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
}
