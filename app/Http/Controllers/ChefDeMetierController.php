<?php

namespace App\Http\Controllers;

use App\Models\Metier;
use App\Models\ChefDeMetier;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Validated;
use App\Http\Requests\ChefDeMetierRequest;

class ChefDeMetierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $chefsDeMetier = ChefDeMetier::with(['metier'])->get();

        return response()->json([
            'success' => true,
            'data' => $chefsDeMetier
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ChefDeMetierRequest $request)
    {
        try {
            $chefDeMetier = ChefDeMetier::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'metier_id' => $request->metier_id,
            ]);

            // Charger la relation métier pour la réponse
            $chefDeMetier->load(['metier']);

            return response()->json([
                'success' => true,
                'message' => 'Chef de métier créé avec succès',
                'data' => $chefDeMetier
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du chef de métier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ChefDeMetier $chefDeMetier)
    {
         // Charger les relations
        $chefDeMetier->load(['metier']);

        return response()->json([
            'success' => true,
            'data' => $chefDeMetier
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChefDeMetierRequest $request, ChefDeMetier $chefDeMetier)
    {
        try {
            $chefDeMetier->update([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'metier_id' => $request->metier_id,
            ]);

            // Recharger les relations
            $chefDeMetier->load(['metier']);

            return response()->json([
                'success' => true,
                'message' => 'Chef de métier modifié avec succès',
                'data' => $chefDeMetier
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du chef de métier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChefDeMetier $chefDeMetier)
    {
        try {
            $chefDeMetier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chef de métier supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du chef de métier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir tous les métiers pour les listes déroulantes
     */
    public function getMetiers()
    {
        $metiers = Metier::select('id', 'nom')->get();

        return response()->json([
            'success' => true,
            'data' => $metiers
        ], 200);
    }


     /**
     * Obtenir les chefs de métier par métier
     */
    public function getByMetier($metier_id)
    {
        $chefsDeMetier = ChefDeMetier::where('metier_id', $metier_id)
                                   ->with(['metier'])
                                   ->get();

        return response()->json([
            'success' => true,
            'data' => $chefsDeMetier
        ], 200);
    }


}
