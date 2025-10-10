<?php

// namespace App\Http\Controllers;

// use App\Models\Stage;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use App\Http\Resources\StageResource;

// class StageController extends Controller
// {
//      // Liste des stages
//     public function index()
//     {
//         return StageResource::collection(Stage::with(['etudiant','entreprise','tuteur'])->paginate(10));
//     }

//     // Créer un stage (affecter un étudiant à une entreprise)
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'etudiant_id'   => 'required|exists:users,id',
//             'entreprise_id' => 'required|exists:entreprises,id',
//             'tuteur_id'     => 'nullable|exists:users,id',
//             'dateDebut'     => 'required|date',
//             'dateFin'       => 'required|date|after:dateDebut',
//         ]);

//         $stage = Stage::create($data);

//         return new StageResource($stage->load(['etudiant','entreprise','tuteur']));
//     }

//     // Détails d’un stage
//     public function show(Stage $stage)
//     {
//         return new StageResource($stage->load(['etudiant','entreprise','tuteur']));
//     }

//     // Mettre à jour un stage
//     public function update(Request $request, Stage $stage)
//     {
//         $data = $request->validate([
//             'tuteur_id' => 'nullable|exists:users,id',
//             'dateDebut' => 'nullable|date',
//             'dateFin'   => 'nullable|date|after:dateDebut',
//         ]);

//         $stage->update($data);

//         return new StageResource($stage->load(['etudiant','entreprise','tuteur']));
//     }

//     // Supprimer un stage
//     public function destroy(Stage $stage)
//     {
//         $stage->delete();

//         return response()->json(['message' => 'Stage supprimé avec succès']);
//     }
// }











namespace App\Http\Controllers;

use App\Models\Stage;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class StageController extends Controller
{
    public function index()
    {
        $stages = Stage::with(['campagne', 'entreprise', 'etudiant'])->get();
        return response()->json(['success' => true, 'data' => $stages], 200);
    }

        public function store(Request $request)
        {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'campagne_id' => 'required|exists:campagne_de_stages,id',
                'entreprise_id' => 'required|exists:entreprises,id',
                'etudiant_id' => 'required|exists:users,id',
            ]);

            $stage = Stage::create($validated);
            return response()->json(['success' => true, 'data' => $stage], 201);
        }

        public function update(Request $request, Stage $stage)
        {
            $stage->update($request->all());
            return response()->json(['success' => true, 'data' => $stage]);
        }

        public function destroy(Stage $stage)
        {
            $stage->delete();
            return response()->json(['success' => true, 'message' => 'Stage supprimé']);
        }



            public function indexForMaitre(Request $request)
        {
            $user = $request->user();

            // Récupérer tous les stages dont ce maître est responsable
            $stages = Stage::with(['etudiant', 'entreprise', 'campagne'])
                ->where('tuteur_id', $user->id)
                ->get();

            return response()->json(['success' => true, 'data' => $stages], 200);
        }

        public function downloadRapport(Stage $stage)
        {
            if (!$stage->rapport_path || !Storage::exists($stage->rapport_path)) {
                return response()->json(['error' => 'Aucun rapport trouvé.'], 404);
            }

            return Storage::download($stage->rapport_path);
        }

        public function updateNote(Request $request, Stage $stage)
    {
        $request->validate([
            'note' => 'required|numeric|min:0|max:20',
        ]);

        $stage->update(['note' => $request->note]);

        return response()->json(['success' => true, 'message' => 'Note enregistrée.']);
    }


    public function getMyStage(Request $request)
    {
        $user = $request->user();

        $stage = Stage::with(['entreprise', 'tuteur', 'campagne'])
            ->where('etudiant_id', $user->id)
            ->first();

        if (!$stage) {
            return response()->json(['message' => 'Aucun stage trouvé pour cet étudiant.'], 404);
        }

        return response()->json(['success' => true, 'data' => $stage]);
    }

    public function uploadRapport(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'rapport' => 'required|mimes:pdf|max:5120', // 5 Mo max
        ]);

        $stage = Stage::where('etudiant_id', $user->id)->first();

        if (!$stage) {
            return response()->json(['error' => 'Aucun stage trouvé pour cet étudiant.'], 404);
        }

        if ($request->hasFile('rapport')) {
            $path = $request->file('rapport')->store('rapports', 'public');
            $stage->update(['rapport_path' => $path]);
        }

        return response()->json(['success' => true, 'message' => 'Rapport envoyé avec succès !']);
    }

}
