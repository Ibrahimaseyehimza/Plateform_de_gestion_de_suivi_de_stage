<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TacheResource extends JsonResource
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
            'stage_id' => $this->stage_id,
            'titre' => $this->titre,
            'description' => $this->description,
            'dateLimite' => $this->dateLimite,
            'livrables' => $this->livrables->map(function ($livrable) {
                return [
                    'id' => $livrable->id,
                    'fichier' => $livrable->fichier,
                    'commentaire' => $livrable->commentaire,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
