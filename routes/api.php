<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\LivrableController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\EvaluationController;

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

Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

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









// Route réservée au chef de département
    Route::get('/dashboard/chef-departement', function () {
        return "Bienvenue Chef de Département";
    })->middleware('role:chef_departement');

    // Route réservée au chef de métier
    Route::get('/dashboard/chef-metier', function () {
        return "Bienvenue Chef de Métier";
    })->middleware('role:chef_metier');

    // Route réservée au maître de stage
    Route::get('/dashboard/maitre-stage', function () {
        return "Bienvenue Maître de Stage";
    })->middleware('role:maitre_stage');

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

