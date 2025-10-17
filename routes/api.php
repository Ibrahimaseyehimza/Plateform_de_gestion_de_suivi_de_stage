<?php

use Illuminate\Http\Request;
use App\Models\CampagneDeStage;
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
use App\Http\Controllers\CampagneDeStageController;
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
//  ROUTES PROTÉGÉES - LECTURE SEULE (tous les utilisateurs authentifiés)
// ============================================================

Route::middleware(['auth:sanctum'])->group(function () {

    // Campagnes (lecture)
    Route::get('v1/campagnes', [CampagneDeStageController::class, 'index']);

    // Entreprises (lecture)
    Route::get('v1/entreprises', [EntrepriseController::class, 'index']);
    Route::get('v1/entreprises/{id}', [EntrepriseController::class, 'show']);
    Route::get('v1/metiers/{metier}/entreprises', [EntrepriseController::class, 'getByMetier']);

    // Stages (lecture)
    Route::get('v1/stages', [StageController::class, 'index']);
    Route::get('v1/stages/{id}', [StageController::class, 'show']);

    // Déconnexion
    Route::post('v1/logout', [AuthController::class, 'logout']);
});

// ============================================================
//  ROUTES APPRENANT
// ============================================================

Route::middleware(['auth:sanctum', 'role:apprenant'])->prefix('v1')->group(function () {

    // 🔹 Campagnes disponibles pour l'apprenant
    Route::get('campagnes/apprenant', [CampagneDeStageController::class, 'campagnesOuvertesPourApprenant']);

    // 🔹 Stage actuel de l'apprenant
    Route::get('apprenant/stage', [StageController::class, 'getMyStage']);

    // 🔹 Upload rapport de stage
    Route::post('apprenant/stage/rapport', [StageController::class, 'uploadRapport']);

    // 🔹 Infos sur l'utilisateur connecté
    Route::get('apprenant/me', [ApprenantAuthController::class, 'me']);

    // 🔹 Changer le mot de passe
    Route::post('apprenant/change-password', [ApprenantAuthController::class, 'changePassword']);

    // 🔹 Logout
    Route::post('apprenant/logout', [ApprenantAuthController::class, 'logout']);


});


Route::middleware(['auth:sanctum'])->group(function () {
    // ✅ Maintenant on peut mettre les routes avec {id}
    Route::get('v1/campagnes/{id}', [CampagneDeStageController::class, 'show']);
});

// ============================================================
//  ROUTES CHEF DE DÉPARTEMENT
// ============================================================

Route::middleware(['auth:sanctum', 'role:chef_departement'])->prefix('v1')->group(function () {

    // ==================== MÉTIERS ====================
    Route::post('metiers', [MetierController::class, 'store']);
    Route::put('metiers/{id}', [MetierController::class, 'update']);
    Route::delete('metiers/{id}', [MetierController::class, 'destroy']);

    // ==================== CHEFS DE MÉTIER ====================
    Route::apiResource('chefs-de-metier', ChefDeMetierController::class);

    // ==================== ENTREPRISES ====================
    Route::post('entreprises', [EntrepriseController::class, 'store']);
    Route::put('entreprises/{id}', [EntrepriseController::class, 'update']);
    Route::delete('entreprises/{id}', [EntrepriseController::class, 'destroy']);

    // ==================== RH ====================
    Route::apiResource('rhs', RhController::class);

    // ==================== CAMPAGNES ====================
    Route::post('campagnes', [CampagneDeStageController::class, 'store']);
    Route::put('campagnes/{id}', [CampagneDeStageController::class, 'update']);
    Route::delete('campagnes/{id}', [CampagneDeStageController::class, 'destroy']);
    Route::post('campagnes/{id}/send-mails', [CampagneDeStageController::class, 'sendMails']);

    // ==================== STAGES ====================
    Route::post('stages', [StageController::class, 'store']);
    Route::put('stages/{id}', [StageController::class, 'update']);
    Route::delete('stages/{id}', [StageController::class, 'destroy']);
    Route::post('stages/assign', [StageController::class, 'assign']);

    // ==================== UTILISATEURS ====================
    Route::post('users', [UserController::class, 'store']);
    Route::get('users', [UserController::class, 'index']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});

// ============================================================
//  ROUTES RH
// ============================================================

Route::middleware(['auth:sanctum', 'role:rh'])->prefix('v1')->group(function () {
    Route::apiResource('maitres', MaitreStageController::class);
    Route::get('rh/campagnes', [RhController::class, 'campagnesActives']);
    Route::get('rh/stages', [RhController::class, 'stages']);

    Route::get('campagnes', [CampagneDeStageController::class, 'indexForRh']);
    Route::post('campagnes/{id}/validation', [CampagneDeStageController::class, 'validerCampagne']);
});

// ============================================================
//  ROUTES CHEF DE MÉTIER
// ============================================================

Route::middleware(['auth:sanctum', 'role:chef_metier'])->prefix('v1/chef-metier')->group(function () {
    Route::get('campagnes', [ChefDeMetierController::class, 'campagnes']);
    Route::get('entreprises', [ChefDeMetierController::class, 'entreprises']);
    Route::get('stages', [ChefDeMetierController::class, 'stages']);
    Route::get('campagnes/export', [CampagneDeStageController::class, 'export']);

    // Apprenants
    Route::get('apprenants', [UserController::class, 'index']);
    Route::post('apprenants', [UserController::class, 'store']);
    Route::post('apprenants/import', [UserController::class, 'import']);
    Route::delete('apprenants/{id}', [ApprenantController::class, 'destroy']);
});

// ============================================================
// ROUTES DASHBOARD (exemples simples)
// ============================================================

Route::get('/dashboard/chef-metier', function () {
    return "Bienvenue Chef de Métier";
})->middleware('role:chef_metier');

Route::get('/dashboard/maitre-stage', function () {
    return "Bienvenue Maître de Stage";
})->middleware('role:maitre_stage');

Route::get('/dashboard/rh', function () {
    return "Bienvenue RH";
})->middleware('role:rh');

Route::get('/dashboard/apprenant', function () {
    return "Bienvenue Apprenant";
})->middleware('role:apprenant');

Route::get('/dashboard/gestion', function () {
    return "Accès pour RH ou Chef de Département";
})->middleware('role:rh,chef_departement');
