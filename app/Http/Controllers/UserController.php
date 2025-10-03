<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;

class UserController extends Controller
{
     public function store(RegisterRequest $request)
    {
        $validated = $request->validated();
        $currentUser = auth()->user();



    //Vérification du rôle
    if ($validated['role'] === User::ROLE_CHEF_METIER) {
        // Vérifier que le metier n'a pas déjà un chef
        if (User::where('role', User::ROLE_CHEF_METIER)
                ->where('metier_id', $validated['metier_id'])
                ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce métier a déjà un chef.'
            ], 403);
        }
    }

    if ($validated['role'] === User::ROLE_CHEF_DEPARTEMENT) {
        return response()->json([
            'success' => false,
            'message' => 'Impossible de créer un autre chef de département.'
        ], 403);
    }


        // Générer un mot de passe temporaire
        $temporaryPassword = Str::random(10);

        // Créer l’utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            // 'departement_id' => $validated['departement_id'] ?? null,
            'metier_id' => $validated['metier_id'] ?? null,
            'entreprise_id' => $validated['entreprise_id'] ?? null,
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);

        // Envoyer l’email avec les identifiants
        Mail::to($user->email)->queue(new InvitationMail($user, $temporaryPassword));

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès. Un email a été envoyé avec ses identifiants.',
            'data' => $user
        ], 201);
    }
}
