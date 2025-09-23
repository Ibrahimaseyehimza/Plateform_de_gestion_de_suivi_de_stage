<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\Request;

class EntrepriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Entreprise::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'localisation' => 'nullable|string',
            'secteur' => 'nullable|string',
        ]);

        return Entreprise::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Entreprise::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, $id)
    {
        $entreprise = Entreprise::findOrFail($id);
        $entreprise->update($request->all());
        return $entreprise;
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy($id)
    {
        Entreprise::destroy($id);
        return response()->json(['message' => 'Entreprise supprimée']);
    }
}
