<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMustChangePassword
{
    /**
     * Intercepte les requêtes si l'utilisateur doit changer son mot de passe
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez changer votre mot de passe avant d’accéder à cette ressource.',
                'must_change_password' => true,

            ], 403);
        }

        return $next($request);
    }
}
