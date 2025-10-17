<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Apprenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    //  public function handle(Request $request, Closure $next, ...$roles)
    // {
    //     $user = $request->user();

    //     if (! $user || ! in_array($user->role, $roles)) {
    //         return response()->json(['message' => 'Accès non autorisé'], 403);
    //     }

    //     return $next($request);
    // }

    //  public function handle(Request $request, Closure $next, ...$roles)
    // {
    //     $user = $request->user();

    //     if (!$user) {
    //         return response()->json(['message' => 'Non authentifié'], 401);
    //     }

    //     // ✅ Détecter si c'est un Apprenant
    //     $userRole = null;

    //     if ($user instanceof Apprenant) {
    //         $userRole = 'apprenant';
    //     } else {
    //         $userRole = $user->role ?? null;
    //     }

    //     \Log::info('RoleMiddleware:', [
    //         'user_class' => get_class($user),
    //         'user_id' => $user->id,
    //         'detected_role' => $userRole,
    //         'required_roles' => $roles
    //     ]);

    //     if (!$userRole || !in_array($userRole, $roles)) {
    //         return response()->json([
    //             'message' => 'Accès non autorisé',
    //             'your_role' => $userRole,
    //             'required_roles' => $roles
    //         ], 403);
    //     }

    //     return $next($request);
    // }


    // public function handle(Request $request, Closure $next, ...$roles)
    // {
    //     $user = $request->user();

    //     \Log::info('=== ROLE MIDDLEWARE ===', [
    //         'user_exists' => $user ? 'OUI' : 'NON',
    //         'user_class' => $user ? get_class($user) : 'NULL',
    //         'user_id' => $user?->id,
    //         'is_apprenant' => $user instanceof \App\Models\Apprenant,
    //         'required_roles' => $roles,
    //         'request_path' => $request->path(),
    //     ]);

    //     if (!$user) {
    //         \Log::error('Pas d\'utilisateur authentifié');
    //         return response()->json(['message' => 'Non authentifié'], 401);
    //     }

    //     $userRole = $user instanceof \App\Models\Apprenant ? 'apprenant' : ($user->role ?? null);

    //     \Log::info('Role détecté:', ['role' => $userRole]);

    //     if (!$userRole || !in_array($userRole, $roles)) {
    //         \Log::error('Accès refusé', [
    //             'user_role' => $userRole,
    //             'required' => $roles
    //         ]);
    //         return response()->json([
    //             'message' => 'Accès non autorisé',
    //             'your_role' => $userRole,
    //             'required_roles' => $roles
    //         ], 403);
    //     }

    //     \Log::info('Accès autorisé ✅');
    //     return $next($request);
    // }

    // public function handle(Request $request, Closure $next, ...$roles)
    // {
    //     $user = $request->user();

    //     \Log::info('=== ROLE MIDDLEWARE ===', [
    //         'user_exists' => $user ? 'OUI' : 'NON',
    //         'user_class' => $user ? get_class($user) : 'NULL',
    //         'user_id' => $user?->id,
    //         'user_role' => $user?->role, // ✅ AJOUT
    //         'required_roles' => $roles,
    //         'request_path' => $request->path(),
    //     ]);

    //     if (!$user) {
    //         \Log::error('Pas d\'utilisateur authentifié');
    //         return response()->json(['message' => 'Non authentifié'], 401);
    //     }

    //     // ✅ FIX : Toujours utiliser $user->role
    //     $userRole = $user->role;

    //     \Log::info('Role détecté:', ['role' => $userRole]);

    //     if (!$userRole || !in_array($userRole, $roles)) {
    //         \Log::error('Accès refusé', [
    //             'user_role' => $userRole,
    //             'required' => $roles
    //         ]);
    //         return response()->json([
    //             'message' => 'Accès refusé',
    //             'your_role' => $userRole,
    //             'required_roles' => $roles
    //         ], 403);
    //     }

    //     \Log::info('Accès autorisé ✅');
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, ...$roles)
{
    $user = $request->user();

    // 🔍 DEBUG COMPLET
    \Log::info('=== ROLE MIDDLEWARE DEBUG COMPLET ===', [
        'user_exists' => $user ? 'OUI' : 'NON',
        'user_class' => $user ? get_class($user) : 'NULL',
        'user_id' => $user?->id,
        'user_email' => $user?->email,
        'user_role' => $user?->role,
        'user_ALL_ATTRIBUTES' => $user ? $user->getAttributes() : 'NULL', // 🔍 TOUS les champs
        'user_toArray' => $user ? $user->toArray() : 'NULL', // 🔍 Version tableau
        'required_roles' => $roles,
        'request_path' => $request->path(),
        'token_from_header' => $request->bearerToken(), // 🔍 Le token reçu
    ]);

    if (!$user) {
        \Log::error('Pas d\'utilisateur authentifié');
        return response()->json(['message' => 'Non authentifié'], 401);
    }

    $userRole = $user->role;

    \Log::info('Role détecté:', ['role' => $userRole]);

    if (!$userRole || !in_array($userRole, $roles)) {
        \Log::error('Accès refusé', [
            'user_role' => $userRole,
            'required' => $roles
        ]);
        return response()->json([
            'message' => 'Accès refusé',
            'your_role' => $userRole,
            'required_roles' => $roles,
            'debug_all_user_data' => $user->getAttributes() // 🔍 Pour voir TOUT
        ], 403);
    }

    \Log::info('Accès autorisé ✅');
    return $next($request);
}
}
