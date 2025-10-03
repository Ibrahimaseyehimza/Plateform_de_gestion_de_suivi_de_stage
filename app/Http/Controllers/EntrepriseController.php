<?php

// namespace App\Http\Controllers;

// use App\Models\Entreprise;
// use Illuminate\Http\Request;
// use App\Http\Requests\EntrepriseRequest;

// class EntrepriseController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         // return Entreprise::paginate(10);

//         $entreprises = Entreprise::paginate(10);;

//         return response()->json([
//             'success' => true,
//             'data' => $entreprises
//         ], 200);
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     // public function store(Request $request)
//     // {
//     //     $request->validate([
//     //         'nom' => 'required|string|max:255',
//     //         'localisation' => 'nullable|string',
//     //         'secteur' => 'nullable|string',
//     //     ]);

//     //     return Entreprise::create($request->all());
//     // }

//             public function store(EntrepriseRequest $request)
//             {
//                 $entreprise = Entreprise::create([
//                     'nom' => $request->nom,
//                     'adresse' => $request->adresse,
//                     'email' => $request->email,
//                     'telephone' => $request->telephone,
//                     'latitude' => $request->latitude,
//                     'longitude' => $request->longitude,
//                 ]);

//                 return response()->json([
//                     'success' => true,
//                     'data' => $entreprise
//                 ], 201);
//             }

//     /**
//      * Display the specified resource.
//      */
//     // public function show($id)
//     // {
//     //     return Entreprise::findOrFail($id);
//     // }

//      /**
//      * Afficher une entreprise spécifique
//      */
//     public function show(Entreprise $entreprise)
//     {
//         return response()->json([
//             'success' => true,
//             'data' => $entreprise
//         ], 200);
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     //  public function update(Request $request, $id)
//     // {
//     //     $entreprise = Entreprise::findOrFail($id);
//     //     $entreprise->update($request->all());
//     //     return $entreprise;
//     // }

//     public function update(EntrepriseRequest $request, Entreprise $entreprise)
//     {
//         try {
//             $entreprise->update([
//                 'nom' => $request->nom,
//                 'adresse' => $request->adresse,
//                 'email' => $request->email,
//                 'telephone' => $request->telephone,
//                 'latitude' => $request->latitude ?? $entreprise->latitude,
//                 'longitude' => $request->longitude ?? $entreprise->longitude,
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Entreprise modifiée avec succès',
//                 'data' => $entreprise
//             ], 200);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Erreur lors de la modification de l\'entreprise',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     //  public function destroy($id)
//     // {
//     //     Entreprise::destroy($id);
//     //     return response()->json(['message' => 'Entreprise supprimée']);
//     // }

//     public function destroy(Entreprise $entreprise)
//     {
//         try {
//             $entreprise->delete();

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Entreprise supprimée avec succès'
//             ], 200);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Erreur lors de la suppression de l\'entreprise',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }
// }




















namespace App\Http\Controllers;

use App\Http\Requests\CreateEntrepriseRequest;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Http\Requests\EntrepriseRequest;
use App\Http\Requests\UpdateEntrepriseRequest;

class EntrepriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entreprises = Entreprise::all();

        return response()->json([
            'success' => true,
            'data' => $entreprises
        ], 200);
    }

    public function getByMetier($metierId)
    {
        $entreprises = Entreprise::where('metier_id', $metierId)->get();

        return response()->json([
            'success' => true,
            'data' => $entreprises
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEntrepriseRequest $request)
    {
        try {
            $entreprise = Entreprise::create([
                'nom' => $request->nom,
                'adresse' => $request->adresse,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entreprise créée avec succès',
                'data' => $entreprise
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'entreprise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entreprise $entreprise)
    {
        return response()->json([
            'success' => true,
            'data' => $entreprise
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntrepriseRequest $request, Entreprise $entreprise)
    {
        try {
            // Même logique que store() pour uniformiser
            $entreprise->update([
                'nom' => $request->nom,
                'adresse' => $request->adresse,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entreprise modifiée avec succès',
                'data' => $entreprise
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification de l\'entreprise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entreprise $entreprise)
    {
        try {
            $entreprise->delete();

            return response()->json([
                'success' => true,
                'message' => 'Entreprise supprimée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'entreprise',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
