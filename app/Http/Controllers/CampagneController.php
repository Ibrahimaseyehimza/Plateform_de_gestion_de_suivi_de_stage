<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CampagneDeStage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\CampagneResource;

class CampagneController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     // return CampagneResource::collection(
    //     //     CampagneDeStage::latest()->paginate(10)
    //     // );

    //      $campagnes = Campagne::with(['metier', 'entreprises'])->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $campagnes
    //     ]);
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'titre'         => 'required|string|max:255',
    //         'dateLancement' => 'required|date',
    //         'dateCloture'   => 'required|date|after_or_equal:dateLancement',
    //     ]);

    //     $campagne = CampagneDeStage::create($request->all());

    //     return new CampagneResource($campagne);
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show($id)
    // {
    //     $campagne = CampagneDeStage::findOrFail($id);
    //     return new CampagneResource($campagne);
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, $id)
    // {
    //     $campagne = CampagneDeStage::findOrFail($id);

    //     $request->validate([
    //         'titre'         => 'sometimes|string|max:255',
    //         'dateLancement' => 'sometimes|date',
    //         'dateCloture'   => 'sometimes|date|after_or_equal:dateLancement',
    //     ]);

    //     $campagne->update($request->all());

    //     return new CampagneResource($campagne);
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy($id)
    // {
    //     $campagne = CampagneDeStage::findOrFail($id);
    //     $campagne->delete();

    //     return response()->json(['message' => 'Campagne supprimée avec succès']);
    // }

















}
