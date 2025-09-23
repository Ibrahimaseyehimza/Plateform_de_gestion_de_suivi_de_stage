<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivrableResource extends JsonResource
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
                    'tache_id' => $this->tache_id,
                    'fichier' => url('storage/' . $this->fichier),
                    'commentaire' => $this->commentaire,
                    'created_at' => $this->created_at->toDateTimeString(),
                ];

    }
}
