<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expediteur' => [
                'id' => $this->expediteur->id,
                'nom' => $this->expediteur->nom,
                'email' => $this->expediteur->email,
            ],
            'destinataire' => [
                'id' => $this->destinataire->id,
                'nom' => $this->destinataire->nom,
                'email' => $this->destinataire->email,
            ],
            'contenu' => $this->contenu,
            'date' => $this->date,
        ];
    }
}
