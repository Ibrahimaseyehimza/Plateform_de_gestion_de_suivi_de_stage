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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

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

Route::middleware(['auth:sanctum'])->group(function () {

    //  LECTURE SEULE - Accessible à tous les utilisateurs authentifiés

    // Campagnes (lecture)
    Route::get('v1/campagnes', [CampagneDeStageController::class, 'index']);
    Route::get('v1/campagnes/{id}', [CampagneDeStageController::class, 'show']);

    // Entreprises (lecture)
    Route::get('v1/entreprises', [EntrepriseController::class, 'index']);
    Route::get('v1/entreprises/{id}', [EntrepriseController::class, 'show']);
    Route::get('v1/metiers/{metier}/entreprises', [EntrepriseController::class, 'getByMetier']);

    // Stages (lecture)
    Route::get('v1/stages', [StageController::class, 'index']);
    Route::get('v1/stages/{id}', [StageController::class, 'show']);

    // Déconnexion
    Route::post('v1/logout', [AuthController::class, 'logout']);


        // Route::get('v1/campagnes', [CampagneDeStageController::class, 'index']);

        // Route::get('v1/entreprises', [EntrepriseController::class, 'index']);



    // Routes accessibles uniquement aux admins
    Route::middleware('role:admin')->group(function () {
        // Route::post('/stages', [StageController::class, 'store']);
        // Route::delete('/stages/{id}', [StageController::class, 'destroy']);
    });

    // Routes accessibles aux chefs de département et admins
    // Route::middleware('role:admin,chef_departement')->group(function () {
    //     Route::get('/departements', 'DepartementController@index');
    // });

    // Routes accessibles aux étudiants
    // Route::middleware('role:etudiant')->group(function () {
    //     Route::get('/mes-taches', 'TacheController@mesTaches');
    // });

});

// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
//     Route::apiResource('v1/campagnes', CampagneController::class);
//     Route::apiResource('v1/entreprises', EntrepriseController::class);
//     Route::apiResource('v1/stages', StageController::class);
//     // Si tu veux limiter la création/modif/suppression uniquement aux tuteurs/admins :
//     // Route::apiResource('taches', TacheController::class)->except(['index','show']);

//     Route::post('/logout', [AuthController::class, 'logout']);

// });

// Route::middleware(['auth:sanctum', 'role:tuteur,admin'])->group(function () {
//     Route::apiResource('v1/taches', TacheController::class)->except(['index','show']);
// });


// Route::middleware(['auth:sanctum'])->group(function () {

//     Route::apiResource('messages', MessageController::class)->except(['update']);
//     Route::apiResource('v1/evaluations', EvaluationController::class);
//     Route::apiResource('v1/livrables', LivrableController::class)->except(['update']);


//     // Lister les notifications de l’utilisateur connecté
//     Route::get('/notifications', function () {
//         return auth()->user()->notifications;
//     });

    // // Lister uniquement les notifications non lues
    // Route::get('/notifications/unread', function () {
    //     return auth()->user()->unreadNotifications;
    // });

    // // Marquer toutes les notifications comme lues
    // Route::post('/notifications/mark-as-read', function () {
    //     auth()->user()->unreadNotifications->markAsRead();
    //     return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues']);
    // });

    // // Marquer une notification précise comme lue
    // Route::post('/notifications/{id}/mark-as-read', function ($id) {
    //     $notification = auth()->user()->notifications()->findOrFail($id);
    //     $notification->markAsRead();
    //     return response()->json(['message' => 'Notification marquée comme lue']);
    // });
// });

// ============================================================
//  ROUTES CHEF DE DÉPARTEMENT
// ============================================================

        Route::middleware(['auth:sanctum', 'role:chef_departement'])->group(function () {
                // Métiers
            // Route::apiResource('v1/metiers', MetierController::class)->except('update');

            // ==================== MÉTIERS ====================
            Route::post('v1/metiers', [MetierController::class, 'store']);
            Route::put('v1/metiers/{id}', [MetierController::class, 'update']);
            Route::delete('v1/metiers/{id}', [MetierController::class, 'destroy']);

            // ==================== CHEFS DE MÉTIER ====================
            Route::apiResource('v1/chefs-de-metier', ChefDeMetierController::class);

             // ==================== ENTREPRISES ====================
            Route::post('v1/entreprises', [EntrepriseController::class, 'store']);
            Route::put('v1/entreprises/{id}', [EntrepriseController::class, 'update']);
            Route::delete('v1/entreprises/{id}', [EntrepriseController::class, 'destroy']);

            // ==================== RH ====================
            Route::apiResource('v1/rhs', RhController::class);

            // ==================== CAMPAGNES ====================
            Route::post('v1/campagnes', [CampagneDeStageController::class, 'store']);
            Route::put('v1/campagnes/{id}', [CampagneDeStageController::class, 'update']);
            Route::delete('v1/campagnes/{id}', [CampagneDeStageController::class, 'destroy']);
            Route::post('v1/campagnes/{id}/send-mails', [CampagneDeStageController::class, 'sendMails']);

            // ==================== RH ====================
            Route::apiResource('v1/rhs', RhController::class);

            // ==================== CAMPAGNES ====================
            Route::post('v1/campagnes', [CampagneDeStageController::class, 'store']);
            Route::put('v1/campagnes/{id}', [CampagneDeStageController::class, 'update']);
            Route::delete('v1/campagnes/{id}', [CampagneDeStageController::class, 'destroy']);
            Route::post('/v1/campagnes/{id}/send-mails', [CampagneDeStageController::class, 'sendMails']);


                // ==================== STAGES ====================
            Route::post('v1/stages', [StageController::class, 'store']);
            Route::put('v1/stages/{id}', [StageController::class, 'update']);
            Route::delete('v1/stages/{id}', [StageController::class, 'destroy']);
            Route::post('v1/stages/assign', [StageController::class, 'assign']);

            // ==================== UTILISATEURS ====================
            Route::post('v1/users', [UserController::class, 'store']);
            Route::get('v1/users', [UserController::class, 'index']);
            Route::delete('v1/users/{id}', [UserController::class, 'destroy']);

            // Routes supplémentaires pour les chefs de métier
            // Route::get('v1/chefs-de-metier/metiers/list', [ChefDeMetierController::class, 'getMetiers']);
            // Route::get('v1/chefs-de-metier/metier/{metier_id}', [ChefDeMetierController::class, 'getByMetier']);
            // Route::resourece('v1/chefs-de-metier', [ChefDeMetierController::class]);


            // Utilisateurs
            // Route::post('/v1/users', [UserController::class, 'store']); // créer chef de métier / apprenant / RH
            // Route::get('v1/users', [UserController::class, 'index']);
            // Route::delete('/v1/users/{id}', [UserController::class, 'destroy']);

            // Entreprises
            // // Route::apiResource('/v1/entreprises', EntrepriseController::class);
            //     Route::post('v1/entreprises', [EntrepriseController::class, 'store']);
            //     Route::put('v1/entreprises/{id}', [EntrepriseController::class, 'update']);
            //     Route::delete('v1/entreprises/{id}', [EntrepriseController::class, 'destroy']);
                // Route::get('v1/metiers/{metier}/entreprises', [EntrepriseController::class, 'getByMetier']);

            // Route::apiResource('/v1/rhs', RhController::class);


            // Campagnes
            // Route::apiResource('/v1/campagnes', CampagneDeStageController::class);


            // Route::resource('v1/dashboard/campagne', CampagneController::class);
            // Route::resource('v1/dashboard/entreprise', EntrepriseController::class);
            // Route::post('v1/users', [UserController::class, 'store']);

            // Affectations
            // Route::post('/v1/stages/assign', [StageController::class, 'assign']);



            // Route::apiResource('/v1/stages', StageController::class);


    })->middleware('role:chef_departement');


     // ============================================================
        //  ROUTES RH
        // ============================================================
        Route::middleware(['auth:sanctum', 'role:rh'])->group(function () {
            Route::apiResource('v1/maitres', MaitreStageController::class);
            Route::get('v1/rh/campagnes', [RhController::class, 'campagnesActives']);
            Route::get('v1/rh/stages', [RhController::class, 'stages']);
        });


        // ============================================================
        //  ROUTES CHEF DE MÉTIER
        // ============================================================
    Route::middleware(['auth:sanctum', 'role:chef_metier'])->prefix('v1/chef-metier')->group(function () {
        Route::get('v1/campagnes', [ChefDeMetierController::class, 'campagnes']);
        Route::get('v1/entreprises', [ChefDeMetierController::class, 'entreprises']);
        Route::get('v1/stages', [ChefDeMetierController::class, 'stages']);
        Route::get('v1/campagnes/export', [CampagneDeStageController::class, 'export']);


            // Apprenants
        // Route::get('v1/apprenants', [ApprenantController::class, 'index']);
        // Route::post('v1/apprenants', [ApprenantController::class, 'store']);
        Route::get('v1/apprenants', [UserController::class, 'index']);
        Route::post('v1/apprenants', [UserController::class, 'store']);
        // Route::post('v1/apprenants/import', [ApprenantController::class, 'import']);
        Route::post('v1/apprenants/import', [UserController::class, 'import']);
        Route::delete('v1/apprenants/{id}', [ApprenantController::class, 'destroy']);

        // Route::get('v1/etudiants', [ApprenantController::class, 'index']);
        // Route::post('v1/etudiants/import', [ApprenantController::class, 'import'])
            // ->middleware(['auth:sanctum', 'role:chef_metier']);

        // ============================================================
            //  ROUTES POUR ÉTUDIANTS
            // ============================================================
        Route::middleware(['auth:sanctum', 'role:apprenant'])->prefix('/v1/apprenant')->group(function () {
            Route::get('/stage', [StageController::class, 'getMyStage']);
            Route::post('/stage/rapport', [StageController::class, 'uploadRapport']);
        });


            // Routes publiques pour les apprenants
        Route::prefix('apprenant')->group(function () {
            Route::post('login', [ApprenantAuthController::class, 'login']);
        });

        // Routes protégées pour les apprenants
        Route::middleware(['auth:sanctum'])->prefix('apprenant')->group(function () {
            Route::post('logout', [ApprenantAuthController::class, 'logout']);
            Route::get('me', [ApprenantAuthController::class, 'me']);
            Route::post('change-password', [ApprenantAuthController::class, 'changePassword']);
        });

    });
           // Campagnes visibles pour un apprenant connecté
    // Route::get('v1/apprenant/campagnes', [CampagneDeStageController::class, 'campagnesOuvertesPourApprenant']);
// Route::middleware('auth:sanctum')->get('v1/apprenant/campagnes', [CampagneDeStageController::class, 'campagnesOuvertesPourApprenant']);

Route::middleware('auth:apprenant')->get('v1/apprenant/campagnes', [CampagneDeStageController::class, 'campagnesOuvertesPourApprenant']);



    //  Route::middleware(['auth:sanctum', 'role:rh'])->group(function () {
    //     Route::apiResource('/v1/maitres', MaitreStageController::class);
    //     Route::get('/v1/rh/campagnes', [RhController::class, 'campagnesActives']);
    //     Route::get('/v1/rh/stages', [RhController::class, 'stages']);
    // });

    //     Route::middleware(['auth:sanctum', 'role:chef_metier'])->prefix('v1/chef-metier')->group(function () {
    //     // Route::get('v1/campagnes', [ChefDeMetierController::class, 'campagnes']);
    //     Route::get('v1/entreprises', [ChefDeMetierController::class, 'entreprises']);
    //     Route::get('v1/stages', [ChefDeMetierController::class, 'stages']);
    //     Route::get('/v1/campagnes', [CampagneDeStageController::class, 'index']); // lecture seule

    // });
// ->middleware('role:chef_departement');

//     Route::middleware(['auth:sanctum', 'role:apprenant'])->prefix('v1/apprenant')->group(function () {
//     // Route::get('/campagnes', [ApprenantController::class, 'campagnesActives']);
//     Route::get('/campagnes', [ApprenantController::class, 'campagnesDisponibles']);
//     Route::post('/postuler', [ApprenantController::class, 'postuler']);
//     Route::get('/demandes', [ApprenantController::class, 'mesDemandes']);
//     Route::get('/stage', [ApprenantController::class, 'monStage']);
// });


Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {

        // Utilisateurs généraux
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/etudiants/import', [UserController::class, 'import'])
              ->middleware('role:chef_metier');

        // Apprenant
        Route::get('/apprenant/campagnes', [UserController::class, 'campagnesDisponibles']);
        Route::post('/apprenant/postuler', [UserController::class, 'postuler']);
        Route::get('/apprenant/demandes', [UserController::class, 'mesDemandes']);
        Route::get('/apprenant/stage', [UserController::class, 'monStage']);
    });
});



// ============================================================
// ROUTES DASHBOARD (exemples simples)
// ============================================================
Route::get('/dashboard/chef-metier', function () {
        return "Bienvenue Chef de Métier";
    })->middleware('role:chef_metier');

    // Route réservée au maître de stage
    Route::get('/dashboard/maitre-stage', function () {
        return "Bienvenue Maître de Stage";
    })->middleware(' role:maitre_stage');

    // Route réservée aux RH
    Route::get('/dashboard/rh', function () {
        return "Bienvenue RH";
    })->middleware('role:rh');

    // Route réservée aux apprenants
    Route::get('/dashboard/apprenant', function () {
        return "Bienvenue Apprenant";
    })->middleware('role:apprenant');

    // Exemple : RH + chef de département
    Route::get('/dashboard/gestion', function () {
        return "Accès pour RH ou Chef de Département";
    })->middleware('role:rh,chef_departement');

