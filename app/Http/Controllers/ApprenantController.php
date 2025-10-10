<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Maatwebsite\Excel\Excel;
// use App\Imports\EtudiantsImport;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;

// class ApprenantController extends Controller
// {
//      // ➕ Création manuelle d'un étudiant
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'prenom' => 'required|string|max:255',
//             'email' => 'required|email|unique:users',
//             'matricule' => 'required|string|unique:users',
//             'metier_id' => 'required|exists:metiers,id',
//         ]);

//         $user = User::create([
//             'name' => $validated['nom'],
//             'prenom' => $validated['prenom'],
//             'email' => $validated['email'],
//             'password' => Hash::make($validated['matricule']),
//             'matricule' => $validated['matricule'],
//             'role' => 'apprenant',
//             'metier_id' => $validated['metier_id'],
//         ]);

//         return response()->json(['success' => true, 'data' => $user], 201);
//     }

//     // 📥 Importation depuis Excel
//     public function import(Request $request)
//     {
//          // Debug
//     \Log::info('Request has file: ' . ($request->hasFile('file') ? 'yes' : 'no'));
//     \Log::info('All request data: ' . json_encode($request->all()));
//     \Log::info('Files: ' . json_encode($request->allFiles()));
//         $request->validate([
//             'file' => 'required|file|mimes:xlsx,csv'
//         ]);

//         Excel::import(new EtudiantsImport, $request->file('file'));

//         return response()->json(['success' => true, 'message' => 'Importation réussie']);
//     }
// }














// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Maatwebsite\Excel\Facades\Excel;  // ✅ Facade, pas la classe
// use App\Imports\EtudiantsImport;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;

// class ApprenantController extends Controller
// {
//     // 📋 Récupérer tous les étudiants
//     public function index()
//     {
//         $etudiants = User::where('role', 'apprenant')
//             ->with('metier') // Charger la relation métier
//             ->orderBy('created_at', 'desc')
//             ->get();

//         return response()->json([
//             'success' => true,
//             'data' => $etudiants
//         ], 200);
//     }

//     // ➕ Création manuelle d'un étudiant
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'prenom' => 'required|string|max:255',
//             'email' => 'required|email|unique:users',
//             'matricule' => 'required|string|unique:users',
//             'metier_id' => 'required|exists:metiers,id',
//         ]);

//         $user = User::create([
//             'name' => $validated['nom'],
//             'prenom' => $validated['prenom'],
//             'email' => $validated['email'],
//             'password' => Hash::make($validated['matricule']),
//             'matricule' => $validated['matricule'],
//             'role' => 'apprenant',
//             'metier_id' => $validated['metier_id'],
//         ]);

//         return response()->json(['success' => true, 'data' => $user], 201);
//     }

//     // 📥 Importation depuis Excel
//     // public function import(Request $request)
//     // {
//     //     try {
//     //         $request->validate([
//     //             'file' => 'required|file|mimes:xlsx,csv'
//     //         ]);

//     //         Excel::import(new EtudiantsImport, $request->file('file'));

//     //         return response()->json([
//     //             'success' => true,
//     //             'message' => 'Importation réussie'
//     //         ], 200);

//     //     } catch (\Exception $e) {
//     //         \Log::error('Erreur import: ' . $e->getMessage());

//     //         return response()->json([
//     //             'success' => false,
//     //             'message' => 'Erreur lors de l\'importation',
//     //             'error' => $e->getMessage()
//     //         ], 500);
//     //     }
//     // }


//     public function import(Request $request)
// {
//     try {
//         // Debug détaillé
//         \Log::info('=== DEBUT IMPORT ===');
//         \Log::info('Has file: ' . ($request->hasFile('file') ? 'OUI' : 'NON'));
//         \Log::info('All files: ', $request->allFiles());
//         \Log::info('Content-Type: ' . $request->header('Content-Type'));

//         // Validation
//         $request->validate([
//             'file' => 'required|file|mimes:xlsx,csv|max:10240' // Max 10MB
//         ]);

//         $file = $request->file('file');

//         \Log::info('File name: ' . $file->getClientOriginalName());
//         \Log::info('File size: ' . $file->getSize());
//         \Log::info('File mime: ' . $file->getMimeType());

//         Excel::import(new EtudiantsImport, $file);

//         return response()->json([
//             'success' => true,
//             'message' => 'Importation réussie'
//         ], 200);

//     } catch (\Illuminate\Validation\ValidationException $e) {
//         \Log::error('Validation error: ', $e->errors());
//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur de validation',
//             'errors' => $e->errors()
//         ], 422);

//     } catch (\Exception $e) {
//         \Log::error('Import error: ' . $e->getMessage());
//         \Log::error('Stack trace: ' . $e->getTraceAsString());

//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de l\'importation',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }
// }












namespace App\Http\Controllers;

use App\Models\Apprenant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ApprenantsImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ApprenantController extends Controller
{
    // 📋 Récupérer tous les apprenants
    public function index()
    {
        $apprenants = Apprenant::with('metier', 'maitreDeStage')
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('Nombre d\'apprenants trouvés: ' . $apprenants->count());

        return response()->json([
            'success' => true,
            'data' => $apprenants
        ], 200);
    }

    // ➕ Création manuelle d'un apprenant
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:apprenants',
            'matricule' => 'required|string|unique:apprenants',
            'metier_id' => 'required|exists:metiers,id',
        ]);

        $apprenant = Apprenant::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'matricule' => $validated['matricule'],
            'password' => Hash::make($validated['matricule']),
            'metier_id' => $validated['metier_id'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $apprenant
        ], 201);
    }

    // 📥 Importation depuis Excel
    public function import(Request $request)
    {
        try {
            \Log::info('=== DEBUT IMPORT APPRENANTS ===');
            \Log::info('Has file: ' . ($request->hasFile('file') ? 'OUI' : 'NON'));

            $request->validate([
                'file' => 'required|file|mimes:xlsx,csv|max:10240'
            ]);

            $file = $request->file('file');

            \Log::info('File name: ' . $file->getClientOriginalName());
            \Log::info('File size: ' . $file->getSize());

            Excel::import(new ApprenantsImport, $file);

            $count = Apprenant::count();
            \Log::info("Nombre total d'apprenants après import: {$count}");

            return response()->json([
                'success' => true,
                'message' => 'Importation réussie',
                'total_apprenants' => $count
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🗑️ Supprimer un apprenant
    public function destroy($id)
    {
        $apprenant = Apprenant::findOrFail($id);
        $apprenant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Apprenant supprimé avec succès'
        ], 200);
    }
}
