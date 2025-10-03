<?php

namespace App\Http\Controllers;

use App\Models\Metier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MetierRequest;

class MetierController extends Controller
{
    // public function index()
    // {
    //     return response()->json(Metier::all());
    // }

     public function index()
    {
        // return Metier::where('departement_id', auth()->user()->departement_id)->get();
        // return response()->json(Metier::all());

         $metiers = Metier::all();
        return response()->json([
            'success' => true,
            'data' => $metiers
        ]);

        // $metiers = Metier::with(['chef_metier'])->get();
        // return response()->json($metiers);

    }

    public function store(MetierRequest $request)
    {

        $metier = Metier::create([
            'nom' => $request->nom,
            'description' => $request->description,
            // 'departement_id' => auth()->user()->departement_id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $metier
        ], 201);
    }

    public function show(Metier $metier)
    {
         return response()->json([
                'success' => true,
                'data' => $metier
            ], 200);

    }


    // public function update(MetierRequest $request, Metier $metier)
    // {
    //     try {
    //         // Mettre à jour le métier avec les nouvelles données
    //         $metier->update([
    //             'nom' => $request->nom,
    //             'description' => $request->description,
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Métier modifié avec succès',
    //             'data' => $metier
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de la modification du métier',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function destroy(Metier $metier)
    {
        // $metier = Metier::where('departement_id', auth()->user()->departement_id)->findOrFail($id);
        $metier->delete();

        return response()->json(['message' => 'Métier supprimé']);
    }
}
