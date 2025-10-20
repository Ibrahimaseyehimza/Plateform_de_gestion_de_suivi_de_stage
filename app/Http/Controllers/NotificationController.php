<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Liste des notifications de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            Log::info('🔔 Récupération des notifications', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            
            // Pour l'instant, retourner un tableau vide
            // Vous pourrez implémenter la logique de notifications plus tard
            $notifications = [];
            
            return response()->json([
                'success' => true,
                'data' => $notifications
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}