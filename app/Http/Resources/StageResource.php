<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'          => $this->id,
            'etudiant'    => [
                'id' => $this->etudiant->id,
                'nom' => $this->etudiant->nom,
                'email' => $this->etudiant->email,
            ],
            'entreprise'  => $this->entreprise->nom,
            'tuteur'      => $this->tuteur ? $this->tuteur->nom : null,
            'dateDebut'   => $this->dateDebut,
            'dateFin'     => $this->dateFin,
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
