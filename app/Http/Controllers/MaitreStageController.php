<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MaitreStageController extends Controller
{
    public function index()
    {
        $maitres = User::where('role', 'maitre_stage')
            ->with('entreprise')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $maitres
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'entreprise_id' => 'required|exists:entreprises,id',
        ]);

        $maitre = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'maitre_stage',
            'entreprise_id' => $request->entreprise_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Maître de stage créé avec succès',
            'data' => $maitre
        ], 201);
    }

    public function update(Request $request, User $maitre_stage)
    {
        $request->validate([
            'name' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $maitre_stage->id,
        ]);

        $maitre_stage->update($request->only(['name', 'prenom', 'email']));

        return response()->json([
            'success' => true,
            'message' => 'Maître de stage mis à jour',
            'data' => $maitre_stage
        ]);
    }

    public function destroy(User $maitre_stage)
    {
        $maitre_stage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maître de stage supprimé'
        ]);
    }
}

