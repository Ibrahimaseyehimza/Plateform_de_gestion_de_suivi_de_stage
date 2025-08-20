<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
Route::post('v1/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('v1/reset-password', [AuthController::class, 'resetPassword']);

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

    Route::post('/logout', [AuthController::class, 'logout']);
});
