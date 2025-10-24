<?php

// namespace App\Http\Controllers;

// use App\Models\Livrable;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Storage;
// use App\Http\Resources\LivrableResource;

// class LivrableController extends Controller
// {
//      // Liste des livrables (un étudiant voit seulement les siens via ses tâches)
//     public function index()
//     {
//         $user = auth()->user();

//         if ($user->role === 'etudiant') {
//             $livrables = Livrable::whereHas('tache.stage', function($q) use ($user) {
//                 $q->where('etudiant_id', $user->id);
//             })->paginate(10);
//         } else {
//             $livrables = Livrable::with('tache')->paginate(10);
//         }

//         return LivrableResource::collection($livrables);
//     }

//     // Upload d’un livrable
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'tache_id' => 'required|exists:taches,id',
//             'fichier' => 'required|file|mimes:pdf,docx,zip,png,jpg|max:2048',
//             'commentaire' => 'nullable|string',
//         ]);

//         // Sauvegarder le fichier dans storage/app/public/livrables
//         $path = $request->file('fichier')->store('livrables', 'public');

//         $livrable = Livrable::create([
//             'tache_id' => $data['tache_id'],
//             'fichier' => $path,
//             'commentaire' => $data['commentaire'] ?? null,
//         ]);

//         return new LivrableResource($livrable);
//     }

//     // Voir un livrable
//     public function show(Livrable $livrable)
//     {
//         return new LivrableResource($livrable);
//     }

//     // Supprimer un livrable
//     public function destroy(Livrable $livrable)
//     {
//         // Supprimer fichier du disque
//         Storage::disk('public')->delete($livrable->fichier);

//         $livrable->delete();

//         return response()->json(['message' => 'Livrable supprimé avec succès']);
//     }
// }



// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use App\Models\Livrable;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;

// class LivrableController extends Controller
// {
//     // Lister les livrables
//     public function index(Request $request) {
//         $livrables = Livrable::with(['apprenant', 'tache'])->get();
//         return response()->json($livrables);
//     }

//     // Soumettre un nouveau livrable
//     public function store(Request $request) {
//         $request->validate([
//             'tache_id' => 'required|exists:taches,id',
//             'apprenant_id' => 'required|exists:users,id',
//             'titre' => 'required|string|max:255',
//             'description' => 'nullable|string',
//             'fichier' => 'nullable|file|max:10240', // max 10MB
//         ]);

//         $data = $request->only(['tache_id', 'apprenant_id', 'titre', 'description']);

//         if ($request->hasFile('fichier')) {
//             $data['fichier'] = $request->file('fichier')->store('livrables');
//         }

//         $livrable = Livrable::create($data);

//         return response()->json($livrable, 201);
//     }

//     // Mettre à jour le livrable (note et commentaire par le maître de stage)
//     public function update(Request $request, Livrable $livrable) {
//         $request->validate([
//             'note' => 'nullable|numeric|min:0|max:20',
//             'commentaire' => 'nullable|string',
//             'statut' => 'nullable|in:en_attente,approuve,rejete'
//         ]);

//         $livrable->update($request->only(['note', 'commentaire', 'statut']));

//         return response()->json($livrable);
//     }
// }






namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Livrable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivrableController extends Controller
{
    // Lister les livrables
    // public function index(Request $request)
    // {
    //     $livrables = Livrable::with(['apprenant', 'tache'])->get();
    //     return response()->json($livrables);
    // }

     // 🔹 Voir tous les livrables (pour le maître de stage)
    public function index()
    {
        $livrables = Livrable::with(['tache', 'apprenant'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'livrables' => $livrables,
        ]);
    }

    // Soumettre un nouveau livrable
    public function store(Request $request)
    {
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'nullable|file|max:10240', // max 10MB
        ]);

        // ✅ On récupère l'apprenant connecté, ignore l'apprenant_id du request
        $data = $request->only(['tache_id', 'titre', 'description']);
        $data['apprenant_id'] = $request->user()->id;

        // Gestion du fichier
        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('livrables');
        }

        $livrable = Livrable::create($data);

        return response()->json($livrable, 201);
    }

    // Mettre à jour le livrable (note et commentaire par le maître de stage)
    public function update(Request $request, Livrable $livrable)
    {
        // ✅ Vérifier que l'utilisateur connecté est bien le maître de stage de la tâche
        if ($request->user()->id !== $livrable->tache->maitre_stage_id) {
            return response()->json([
                'message' => 'Accès refusé, vous n’êtes pas le maître de stage de cette tâche'
            ], 403);
        }

        $request->validate([
            'note' => 'nullable|numeric|min:0|max:20',
            'commentaire' => 'nullable|string',
            'statut' => 'nullable|in:en_attente,approuve,rejete'
        ]);

        $livrable->update($request->only(['note', 'commentaire', 'statut']));

        return response()->json($livrable);
    }
}
