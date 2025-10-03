<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     */

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();


         // Vérifier clé secrète
        if ($validated['invitation_key'] !== env('DEPARTMENT_REGISTER_KEY')) {
            return response()->json([
                'success' => false,
                'message' => 'Clé d’invitation invalide.'
            ], 403);
        }


         // Vérifier qu’un chef existe déjà pour ce département
        if (User::where('role', User::ROLE_CHEF_DEPARTEMENT)
                // ->where('departement_id', $validated['departement_id'])
                ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce département a déjà un chef.'
            ], 403);
        }

        if ($validated['role'] !== User::ROLE_CHEF_DEPARTEMENT) {
            return response()->json([
                'success' => false,
                'message' => 'Seul un chef de département peut s’inscrire directement.'
            ], 403);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // 'role' => $validated['role'],
            'role' => User::ROLE_CHEF_DEPARTEMENT,
            // 'departement_id' => $validated['departement_id'] ?? null,
            'must_change_password' => false,

        ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
        ], 201);
    }



    /**
     * Connexion d'un utilisateur existant
     */

        public function login(LoginRequest $request)
        {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants invalides'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'must_change_password' => $user->must_change_password,
                ]
            ]);
        }


    /**
     * Déconnexion de l'utilisateur authentifié
     */
    // public function logout(Request $request)
    // {
    //     $request->user()->tokens()->delete();

    //     return response()->json([
    //         'message' => 'Déconnexion réussie'
    //     ]);
    // }

     public function logout()
        {
            auth()->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ]);
        }



    /**
     * Changer de mot de passe
     */
    public function changePassword(ChangePasswordRequest $request)
        {
            $user = auth()->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe actuel incorrect'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'must_change_password' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ]);
        }
    }

    /**
     * Récupération des informations de l'utilisateur authentifié
     */
    // public function me(Request $request)
    // {
    //     return response()->json([
    //         'user' => $request->user()
    //     ]);
    // }


       /**
     * Envoi d'un email de réinitialisation de mot de passe
     */
    // public function forgotPassword(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //     ]);

    //     $user = User::where('email', $request->email)->first();
    //     $token = Str::random(60);

    //     // Stocker le token en base (hashé)
    //     DB::table('password_reset_tokens')->updateOrInsert(
    //         ['email' => $request->email],
    //         [
    //             'token' => Hash::make($token),
    //             'created_at' => now(),
    //         ]
    //     );

    //     // Créer le lien de réinitialisation
    //     $resetUrl = "http://192.168.1.14:8081/reset-password?token={$token}&email=" . urlencode($request->email);

    //     try {
    //         // ENVOYER L'EMAIL
    //         Mail::send([], [], function ($message) use ($request, $resetUrl, $user) {
    //             $message->to($request->email)
    //                     ->subject('Réinitialisation de votre mot de passe')
    //                     ->html("
    //                         <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
    //                             <div style='background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>

    //                                 <!-- Header -->
    //                                 <div style='background-color: #198754; padding: 30px; text-align: center;'>
    //                                     <h1 style='color: white; margin: 0; font-size: 28px;'>Plateforme de Gestion</h1>
    //                                     <p style='color: #e8f5e8; margin: 10px 0 0 0;'>Suivi des Stagiaires</p>
    //                                 </div>

    //                                 <!-- Body -->
    //                                 <div style='padding: 40px 30px;'>
    //                                     <h2 style='color: #333; margin: 0 0 20px 0;'>Réinitialisation de votre mot de passe</h2>

    //                                     <p style='color: #555; line-height: 1.6; margin: 0 0 15px 0;'>
    //                                         Bonjour <strong style='color: #198754;'>{$user->name}</strong>,
    //                                     </p>

    //                                     <p style='color: #555; line-height: 1.6; margin: 0 0 25px 0;'>
    //                                         Vous avez demandé la réinitialisation de votre mot de passe pour votre compte sur la plateforme de gestion des stagiaires.
    //                                     </p>

    //                                     <div style='text-align: center; margin: 35px 0;'>
    //                                         <a href='{$resetUrl}' style='
    //                                             background-color: #198754;
    //                                             color: white;
    //                                             padding: 15px 30px;
    //                                             text-decoration: none;
    //                                             border-radius: 8px;
    //                                             font-weight: bold;
    //                                             font-size: 16px;
    //                                             display: inline-block;
    //                                             box-shadow: 0 2px 5px rgba(25,135,84,0.3);
    //                                         '>
    //                                             🔒 Réinitialiser mon mot de passe
    //                                         </a>
    //                                     </div>

    //                                     <!-- Warning box -->
    //                                     <div style='
    //                                         background-color: #fff3cd;
    //                                         border: 1px solid #ffeaa7;
    //                                         color: #856404;
    //                                         padding: 20px;
    //                                         border-radius: 8px;
    //                                         margin: 25px 0;
    //                                         border-left: 4px solid #ffc107;
    //                                     '>
    //                                         <strong>⚠️ Important :</strong> Ce lien est valide pendant <strong>60 minutes</strong> seulement.
    //                                     </div>

    //                                     <p style='color: #666; line-height: 1.6; margin: 20px 0 0 0; font-size: 14px;'>
    //                                         Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email en toute sécurité. Votre mot de passe restera inchangé.
    //                                     </p>

    //                                     <p style='color: #666; line-height: 1.6; margin: 10px 0 0 0; font-size: 14px;'>
    //                                         Pour votre sécurité, ne partagez jamais ce lien avec personne.
    //                                     </p>
    //                                 </div>

    //                                 <!-- Footer -->
    //                                 <div style='background-color: #f8f9fa; padding: 25px 30px; border-top: 1px solid #dee2e6;'>
    //                                     <p style='color: #6c757d; margin: 0 0 10px 0; font-size: 14px; text-align: center;'>
    //                                         Cordialement,<br>
    //                                         <strong>L'équipe de la Plateforme de Gestion</strong>
    //                                     </p>

    //                                     <div style='margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;'>
    //                                         <p style='color: #6c757d; font-size: 12px; text-align: center; margin: 0;'>
    //                                             Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
    //                                         </p>
    //                                         <p style='color: #007bff; font-size: 11px; text-align: center; margin: 5px 0 0 0; word-break: break-all;'>
    //                                             {$resetUrl}
    //                                         </p>
    //                                     </div>
    //                                 </div>

    //                             </div>
    //                         </div>
    //                     ");
    //         });

    //         \Log::info(' Email de reset password envoyé avec succès à: ' . $request->email);

    //         return response()->json([
    //             'message' => 'Un email de réinitialisation a été envoyé à votre adresse',
    //             'success' => true
    //         ], 200);

    //     } catch (\Exception $e) {
    //         \Log::error('❌ Erreur envoi email forgot password: ' . $e->getMessage());

    //         return response()->json([
    //             'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage(),
    //             'success' => false,
    //             'debug_token' => $token, // Pour debug seulement
    //         ], 500);
    //     }
    // }


       /**
     * Réinitialisation du mot de passe
     */
    // public function resetPassword(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //         'token' => 'required',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     // Récupérer tous les tokens pour cet email
    //     $resets = DB::table('password_reset_tokens')
    //         ->where('email', $request->email)
    //         ->get();

    //     $validReset = null;
    //     foreach ($resets as $reset) {
    //         if (Hash::check($request->token, $reset->token)) {
    //             $validReset = $reset;
    //             break;
    //         }
    //     }

    //     if (!$validReset) {
    //         return response()->json(['message' => 'Token invalide ou expiré'], 400);
    //     }

    //     // Vérifier si le token n'est pas trop ancien
    //     $tokenAge = now()->diffInMinutes($validReset->created_at);
    //     if ($tokenAge > 60) { // 60 minutes
    //         DB::table('password_reset_tokens')->where('email', $request->email)->delete();
    //         return response()->json(['message' => 'Token expiré'], 400);
    //     }

    //     // Mettre à jour le mot de passe
    //     $user = User::where('email', $request->email)->first();
    //     $user->update(['password' => Hash::make($request->password)]);

    //     // Supprimer tous les tokens de reset pour cet email
    //     DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    //     return response()->json(['message' => 'Mot de passe réinitialisé avec succès'], 200);
    // }








