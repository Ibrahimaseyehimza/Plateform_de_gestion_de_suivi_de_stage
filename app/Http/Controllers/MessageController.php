<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;

class MessageController extends Controller
{
    // Liste des messages de l'utilisateur connecté
    public function index()
    {
        $user = auth()->user();

        $messages = Message::where('expediteur_id', $user->id)
            ->orWhere('destinataire_id', $user->id)
            ->with(['expediteur', 'destinataire'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        return MessageResource::collection($messages);
    }

    // Envoyer un message
    public function store(Request $request)
    {
        $data = $request->validate([
            'destinataire_id' => 'required|exists:users,id',
            'contenu' => 'required|string',
        ]);

        $message = Message::create([
            'expediteur_id' => auth()->id(),
            'destinataire_id' => $data['destinataire_id'],
            'contenu' => $data['contenu'],
            'date' => now(),
        ]);

        return new MessageResource($message->load(['expediteur','destinataire']));
    }

    // Voir un message précis
    public function show(Message $message)
    {
        // Vérifier que le user est bien concerné
        if ($message->expediteur_id !== auth()->id() && $message->destinataire_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return new MessageResource($message->load(['expediteur','destinataire']));
    }

    // Supprimer un message (par l’expéditeur uniquement)
    public function destroy(Message $message)
    {
        if ($message->expediteur_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Message supprimé avec succès']);
    }
}
