<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRoleIsChef
{
    /**
     * Vérifie que l'utilisateur est un chef de département
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'chef') {
            return response()->json([
                'success' => false,
                'message' => 'Accès réservé aux chefs de département.'
            ], 403);
        }

        return $next($request);
    }
}
