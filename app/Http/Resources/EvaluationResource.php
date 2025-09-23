<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
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
            'etudiant' => [
                'id' => $this->stage->etudiant->id,
                'nom' => $this->stage->etudiant->nom,
                'email' => $this->stage->etudiant->email,
            ],
            'entreprise' => $this->stage->entreprise->nom,
            'note' => $this->note,
            'commentaire' => $this->commentaire,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
