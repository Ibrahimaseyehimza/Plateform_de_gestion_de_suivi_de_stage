<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RhController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\MetierController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\LivrableController;
use App\Http\Controllers\ApprenantController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\MaitreStageController;
use App\Http\Controllers\ChefDeMetierController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ApprenantTacheController;
use App\Http\Controllers\CampagneDeStageController;
use App\Http\Controllers\chefDeDepartementController;
use App\Http\Controllers\Auth\ApprenantAuthController;

// ============================================================
//  ROUTES PUBLIQUES (sans authentification)
// ============================================================

Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);
Route::post('v1/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('v1/reset-password', [AuthController::class, 'resetPassword']);

// Liste des métiers et départements (publique)
Route::get('v1/metiers', [MetierController::class, 'index']);
Route::get('v1/departements', [DepartementController::class, 'index']);

// ============================================================
//  ROUTES PROTÉGÉES - GLOBALES (tous les utilisateurs authentifiés)
// ============================================================

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    
    // ========================================
    // AUTHENTIFICATION
    // ========================================
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // ========================================
    // NOTIFICATIONS (Tous les rôles)
    // ========================================
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete']);
    Route::delete('/notifications/read/delete-all', [NotificationController::class, 'deleteAllRead']);
    
    // Envoyer une notification (tous les rôles sauf apprenant)
    Route::post('/notifications/send', [NotificationController::class, 'send'])
        ->middleware('role:chef_departement,chef_metier,rh,maitre_stage');
    
    // ========================================
    // LECTURE SEULE (tous les utilisateurs authentifiés)
    // ========================================
    
    // Campagnes (lecture)
    
    Route::get('/campagnes_global', [CampagneDeStageController::class, 'index']);
    Route::get('/campagnes/{id}', [CampagneDeStageController::class, 'show']);

    // Entreprises (lecture)
    Route::get('/entreprises_global', [EntrepriseController::class, 'index']);
    Route::get('/entreprises/{id}', [EntrepriseController::class, 'show']);
    Route::get('/metiers/{metier}/entreprises', [EntrepriseController::class, 'getByMetier']);

    // Stages (lecture)
    Route::get('/stages_global', [StageController::class, 'index']);
    Route::get('/stages/{id}', [StageController::class, 'show']);
});

// ============================================================
//  ROUTES APPRENANT
// ============================================================

// Route::middleware(['auth:sanctum', 'role:apprenant'])->prefix('v1')->group(function () {

//     // 🔹 Campagnes disponibles pour l'apprenant
//     Route::get('campagnes/apprenant', [CampagneDeStageController::class, 'campagnesDisponibles']);

//     // 🔹 Stage actuel de l'apprenant
//     Route::get('apprenant/stage', [StageController::class, 'getMyStage']);

//     // 🔹 Upload rapport de stage
//     Route::post('apprenant/stage/rapport', [StageController::class, 'uploadRapport']);

//     // 🔹 Infos sur l'utilisateur connecté
//     Route::get('apprenant/me', [ApprenantAuthController::class, 'me']);

//     // 🔹 Changer le mot de passe
//     Route::post('apprenant/change-password', [ApprenantAuthController::class, 'changePassword']);

//     Route::post('apprenant/postuler', [UserController::class, 'postuler']);

//     Route::get('apprenant/mes-demandes', [UserController::class, 'mesDemandes']);

//     // 🔹 Logout
//     Route::post('apprenant/logout', [ApprenantAuthController::class, 'logout']);
// });


// ============================================================
//  ROUTES APPRENANT
// ============================================================

Route::middleware(['auth:sanctum', 'role:apprenant'])->group(function () {



    // 🔹 Campagnes disponibles pour l'apprenant

    // Route::get('v1/campagnes/apprenant', [CampagneDeStageController::class, 'campagnesDisponibles']);

    Route::get('v1/campagnes/apprenant', [CampagneDeStageController::class, 'campagnesDisponibles']);

        Route::get('v1/route_campagne_apprenant', [CampagneDeStageController::class, 'campagnesDisponiblesPourApprenant']);



    // 🔹 Stage actuel de l'apprenant

    Route::get('v1/apprenant/stage', [StageController::class, 'getMyStage']);



    // 🔹 Upload rapport de stage

    Route::post('v1/apprenant/stage/rapport', [StageController::class, 'uploadRapport']);



    // 🔹 Infos sur l'utilisateur connecté

    Route::get('v1/apprenant/me', [ApprenantAuthController::class, 'me']);



    // 🔹 Changer le mot de passe

    Route::post('v1/apprenant/change-password', [ApprenantAuthController::class, 'changePassword']);



    Route::post('v1/apprenant/postuler', [UserController::class, 'postuler']);



    Route::get('v1/apprenant/mes-demandes', [UserController::class, 'mesDemandes']);



    // 🔹 Logout

    Route::post('v1/apprenant/logout', [ApprenantAuthController::class, 'logout']);



    Route::get('v1/apprenant/taches', [ApprenantTacheController::class, 'index']);

    // Route::get('v1/apprenant/taches', [ApprenantTacheController::class, 'getTaches']);

    Route::patch('/taches/{id}/terminer', [ApprenantTacheController::class, 'terminer']);

});

Route::middleware(['auth:sanctum', 'role:apprenant'])->group(function () {

    // 🔹 Campagnes disponibles pour l'apprenant
    // Route::get('v1/campagnes/apprenant', [CampagneDeStageController::class, 'campagnesDisponibles']);
    Route::get('v1/campagnes/apprenant', [CampagneDeStageController::class, 'campagnesDisponibles']);
        Route::get('v1/route_campagne_apprenant', [CampagneDeStageController::class, 'campagnesDisponiblesPourApprenant']);

    // 🔹 Stage actuel de l'apprenant
    Route::get('v1/apprenant/stage', [StageController::class, 'getMyStage']);

    // 🔹 Upload rapport de stage
    Route::post('v1/apprenant/stage/rapport', [StageController::class, 'uploadRapport']);

    // 🔹 Infos sur l'utilisateur connecté
    Route::get('v1/apprenant/me', [ApprenantAuthController::class, 'me']);

    // 🔹 Changer le mot de passe
    Route::post('v1/apprenant/change-password', [ApprenantAuthController::class, 'changePassword']);

    Route::post('v1/apprenant/postuler', [UserController::class, 'postuler']);

    Route::get('v1/apprenant/mes-demandes', [UserController::class, 'mesDemandes']);

    // 🔹 Logout
    Route::post('v1/apprenant/logout', [ApprenantAuthController::class, 'logout']);

    Route::get('v1/apprenant/taches', [ApprenantTacheController::class, 'index']);
    // Route::get('v1/apprenant/taches', [ApprenantTacheController::class, 'getTaches']);
    Route::patch('/taches/{id}/terminer', [ApprenantTacheController::class, 'terminer']);
});


// ============================================================
//  ROUTES CHEF DE DÉPARTEMENT
// ============================================================

Route::middleware(['auth:sanctum', 'role:chef_departement'])->prefix('v1')->group(function () {
    // MÉTIERS
    Route::post('/metiers', [MetierController::class, 'store']);
    Route::put('/metiers/{id}', [MetierController::class, 'update']);
    Route::delete('/metiers/{id}', [MetierController::class, 'destroy']);

    // CHEFS DE MÉTIER
    Route::apiResource('/chefs-de-metier', ChefDeMetierController::class);

    // ENTREPRISES
    Route::post('/entreprises', [EntrepriseController::class, 'store']);
    Route::put('/entreprises/{id}', [EntrepriseController::class, 'update']);
    Route::delete('/entreprises/{id}', [EntrepriseController::class, 'destroy']);

    // RH
    Route::apiResource('/rhs', RhController::class);

    // CAMPAGNES
    Route::post('/campagnes', [CampagneDeStageController::class, 'store']);
    Route::put('/campagnes/{id}', [CampagneDeStageController::class, 'update']);
    Route::delete('/campagnes/{id}', [CampagneDeStageController::class, 'destroy']);
    Route::post('/campagnes/{id}/send-mails', [CampagneDeStageController::class, 'sendMails']);
    

    // STAGES
    Route::post('/stages', [StageController::class, 'store']);
    Route::put('/stages/{id}', [StageController::class, 'update']);
    Route::delete('/stages/{id}', [StageController::class, 'destroy']);
    Route::post('/stages/assign', [StageController::class, 'assign']);

    // UTILISATEURS
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Route pour récupérer le stage de l'apprenant connecté
    Route::get('/apprenant/mon-stage', [ApprenantController::class, 'monStage']);
});

// ============================================================
//  ROUTES RH
// ============================================================

Route::middleware(['auth:sanctum', 'role:rh'])->prefix('v1')->group(function () {
    Route::apiResource('/maitres', MaitreStageController::class);
    Route::get('/rh/stages', [RhController::class, 'stages']);

    Route::post('/campagnes/{id}/validation', [CampagneDeStageController::class, 'validerCampagne']);
    

    Route::get('/campagnes_rh', [CampagneDeStageController::class, 'campagnesPourEntreprise']);
    Route::post('/campagnes/{id}/accepter', [CampagneDeStageController::class, 'accepterCampagne']);
    Route::post('/campagnes/{id}/refuser', [CampagneDeStageController::class, 'refuserCampagne']);

    Route::get('/rh/notifications', [RhController::class, 'notifications']);
    Route::post('/notifications/{id}/marquer-lue', [RhController::class, 'marquerCommeLue']);
    Route::put('/notifications/lire-tout', [RHController::class, 'marquerToutesCommeLues']);

    // Étudiants affectés
    Route::get('/rh/etudiants-affectes', [RHController::class, 'etudiantsAffectes']);

    // Maîtres de stage
    Route::get('/rh/maitres-stage', [RHController::class, 'maitresStageDisponibles']);

    // Soumissions
    Route::post('/rh/soumettre-maitre-stage', [RHController::class, 'soumettreAuMaitreStage']);
    Route::get('/rh/historique-soumissions', [RHController::class, 'historiqueSoumissions']);

    // Statistiques
    Route::get('/rh/statistiques', [RHController::class, 'statistiques']);
});

// ============================================================
//  ROUTES CHEF DE MÉTIER
// ============================================================

Route::middleware(['auth:sanctum', 'role:chef_metier'])->prefix('v1/chef-metier')->group(function () {
    Route::get('/campagnes', [ChefDeMetierController::class, 'campagnes']);
    Route::get('/entreprises', [ChefDeMetierController::class, 'entreprises']);
    Route::get('/stages', [ChefDeMetierController::class, 'stages']);
    Route::get('/campagnes/export', [CampagneDeStageController::class, 'export']);

    // Apprenants
    Route::get('/apprenants', [UserController::class, 'index']);
    Route::post('/apprenants', [UserController::class, 'store']);
    Route::post('/apprenants/import', [UserController::class, 'import']);
    Route::delete('/apprenants/{id}', [ApprenantController::class, 'destroy']);
    

    Route::get('/statistiques', [ChefDeMetierController::class, 'statistiques']);
    Route::get('/demandes', [ChefDeMetierController::class, 'demandes']);
    Route::get('/entreprises-disponibles', [ChefDeMetierController::class, 'entreprisesDisponibles']);
    Route::put('/demandes/{id}/affecter', [ChefDeMetierController::class, 'affecter']);

    Route::get('/demandes/demande', [ChefDeMetierController::class, 'accepterEtAffecterEtudiant']);
    Route::post('/demandes/{id}/accepter', [ChefDeMetierController::class, 'accepterEtAffecterEtudiant']);
    Route::post('/demandes/{id}/reorienter', [ChefDeMetierController::class, 'reorienterEtudiant']);

    Route::get('/affectations/export', [ChefDeMetierController::class, 'export']);
    Route::post('/affectations/envoyer-rh', [ChefDeMetierController::class, 'envoyerRh']);
    // Route::get('/affectations', [ChefDeMetierController::class, 'affectations']);
    Route::get('/affectations', [CampagneDeStageController::class, 'campagnesDisponiblesPourChefDeMetier']);

    // Soumissions
    Route::post('/soumettre-maitre-stage', [RHController::class, 'soumettreAuMaitreStage']);
    Route::get('/historique-soumissions', [RHController::class, 'historiqueSoumissions']);
});

// ============================================================
//  ROUTES MAÎTRE DE STAGE
// ============================================================

Route::middleware(['auth:sanctum', 'role:maitre_stage'])->prefix('v1/maitre-stage')->group(function () {
    // Stages supervisés
    Route::get('/stages', [MaitreStageController::class, 'getStages']);
    Route::get('/stages/{id}/rapport', [MaitreStageController::class, 'downloadRapport']);
    Route::put('/stages/{id}/note', [MaitreStageController::class, 'updateNote']);

    // Statistiques
    Route::get('/statistiques', [MaitreStageController::class, 'statistiques']);

    // Étudiants affectés
    Route::get('/etudiants-affectes', [MaitreStageController::class, 'etudiantsAffectes']);

    // Tâches
    Route::get('/taches', [TacheController::class, 'index']);
    Route::post('/taches', [TacheController::class, 'store']);
    Route::put('/taches/{tache}', [TacheController::class, 'update']);
    Route::delete('/taches/{tache}', [TacheController::class, 'destroy']);

    // Livrables
    Route::get('/livrables', [LivrableController::class, 'index']);
    Route::post('/livrables', [LivrableController::class, 'store']);
    Route::put('/livrables/{id}', [LivrableController::class, 'update']);
    Route::delete('/livrables/{id}', [LivrableController::class, 'destroy']);
    
    // Rapports et stagiaires
    Route::get('/rapports', [MaitreStageController::class, 'getRapports']);
    Route::get('/stagiaires', [MaitreStageController::class, 'mesStagiaires']);
    
    // Notifications d'affectation
    Route::get('/notifications-affectations', [MaitreStageController::class, 'notificationsAffectations']);
    Route::post('/notifications/{id}/lire', [MaitreStageController::class, 'marquerNotificationLue']);
});