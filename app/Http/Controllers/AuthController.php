<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // $user = User::create([
        //     'name' => $validated['name'],
        //     'email' => $validated['email'],
        //     'password' => Hash::make($validated['password']),
        //     'role' => $validated['role'] ?? 'etudiant',
        // ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Connexion d'un utilisateur existant
     */
    public function login(LoginRequest $request)
    {
        // $validated = $request->validated();

        // $user = User::where('email', $validated['email'])->first();

        // if (!$user || !Hash::check($validated['password'], $user->password)) {
        //     throw ValidationException::withMessages([
        //         'email' => ['Les informations d\'identification sont incorrectes.'],
        //     ]);
        // }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Déconnexion de l'utilisateur authentifié
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Récupération des informations de l'utilisateur authentifié
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}







// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use App\Http\Requests\LoginRequest;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Requests\RegisterRequest;
// use Illuminate\Validation\ValidationException;

// class AuthController extends Controller
// {
//     /**
//      * Inscription d'un nouvel utilisateur
//      */
//     // public function register(RegisterRequest $request)
//     // {
//     //     $validated = $request->validated();

//     //     $user = User::create([
//     //         'name' => $validated['name'],
//     //         'email' => $validated['email'],
//     //         'password' => Hash::make($validated['password']),
//     //         'role' => $validated['role'] ?? 'etudiant',
//     //     ]);

//     //     $token = $user->createToken('auth_token')->plainTextToken;

//     //     return response()->json([
//     //         'success' => true,
//     //         'message' => 'Inscription réussie',
//     //         'data' => [
//     //             'user' => $user,
//     //             'token' => $token
//     //         ]
//     //     ], 201); // ✅ 201 pour création
//     // }

//     // Régister avec debug
//     public function register(RegisterRequest $request)
// {
//     \Log::info('=== DÉBUT INSCRIPTION ===');
//     \Log::info('Données reçues:', $request->all());
//     \Log::info('Headers:', $request->headers->all());

//     try {
//         $validated = $request->validated();
//         \Log::info('Validation réussie:', $validated);

//         $user = User::create([
//             'name' => $validated['name'],
//             'email' => $validated['email'],
//             'password' => Hash::make($validated['password']),
//             'role' => $validated['role'] ?? 'etudiant',
//         ]);

//         \Log::info('Utilisateur créé:', $user->toArray());

//         $token = $user->createToken('auth_token')->plainTextToken;
//         \Log::info('Token créé avec succès');

//         return response()->json([
//             'success' => true,
//             'message' => 'Inscription réussie',
//             'data' => [
//                 'user' => $user,
//                 'token' => $token
//             ]
//         ], 201);

//     } catch (\Exception $e) {
//         \Log::error('Erreur inscription:', [
//             'message' => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine()
//         ]);

//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de l\'inscription',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }

//     /**
//      * Connexion d'un utilisateur existant
//      */
//     public function login(LoginRequest $request)
//     {
//         $validated = $request->validated();

//         $user = User::where('email', $validated['email'])->first();

//         if (!$user || !Hash::check($validated['password'], $user->password)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Les informations d\'identification sont incorrectes',
//                 'errors' => [
//                     'email' => ['Identifiants invalides']
//                 ]
//             ], 401); // ✅ 401 pour authentification échouée
//         }

//         // Révoquer les anciens tokens (optionnel)
//         $user->tokens()->delete();

//         $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'Connexion réussie',
//             'data' => [
//                 'user' => $user,
//                 'token' => $token
//             ]
//         ], 200); // ✅ 200 pour authentification réussie
//     }

//     /**
//      * Déconnexion de l'utilisateur authentifié
//      */
//     public function logout(Request $request)
//     {
//         $request->user()->currentAccessToken()->delete();

//         return response()->json([
//             'success' => true,
//             'message' => 'Déconnexion réussie'
//         ], 200); // ✅ 200 pour action réussie
//     }

//     /**
//      * Récupération des informations de l'utilisateur authentifié
//      */
//     public function me(Request $request)
//     {
//         return response()->json([
//             'success' => true,
//             'data' => [
//                 'user' => $request->user()
//             ]
//         ], 200); // ✅ 200 pour récupération de données
//     }

//     /**
//      * Rafraîchir le token
//      */
//     public function refresh(Request $request)
//     {
//         $user = $request->user();

//         // Supprimer le token actuel
//         $request->user()->currentAccessToken()->delete();

//         // Créer un nouveau token
//         $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'Token rafraîchi',
//             'data' => [
//                 'token' => $token
//             ]
//         ], 200);
//     }
// }





