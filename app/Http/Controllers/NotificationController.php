<?php
namespace App\Http\controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /api/v1/notifications
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            // Récupérer les notifications (table database notifications de Laravel)
            $notifications = $user->notifications()
                ->latest()
                ->take(20)
                ->get()
                ->map(function($notif) {
                    return [
                        'id' => $notif->id,
                        'title' => $notif->data['title'] ?? 'Notification',
                        'message' => $notif->data['message'] ?? '',
                        'is_read' => $notif->read_at !== null,
                        'created_at' => $notif->created_at,
                        'sender' => $notif->data['sender'] ?? null,
                    ];
                });
            
            $unreadCount = $user->unreadNotifications()->count();
            
            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => $unreadCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur chargement notifications: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des notifications',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    // PATCH /api/v1/notifications/{id}/read
    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = $request->user()
                ->notifications()
                ->findOrFail($id);
            
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée'
            ], 404);
        }
    }
    
    // PATCH /api/v1/notifications/read-all
    public function markAllAsRead(Request $request)
    {
        try {
            $request->user()->unreadNotifications->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications marquées comme lues'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }
    
    // DELETE /api/v1/notifications/{id}
    public function destroy(Request $request, $id)
    {
        try {
            $notification = $request->user()
                ->notifications()
                ->findOrFail($id);
            
            $notification->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification supprimée'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée'
            ], 404);
        }
    }
}