<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Livrable;

use App\Models\Stage;

use App\Models\Entreprise;

use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'apprenant_id',
        'maitre_stage_id',
        'stage_id',
        'entreprise_id',
        'date_echeance',
        // 'priorite',
        'statut',
    ];

    protected $casts = [
        'date_echeance' => 'date',
    ];

    // Relation : La tâche est assignée à un apprenant
    public function apprenant()
    {
        return $this->belongsTo(User::class, 'apprenant_id');
    }

    // Relation : La tâche est créée par un maître de stage
    public function maitre()
    {
        return $this->belongsTo(User::class, 'maitre_stage_id');
    }

    // Alias pour compatibilité avec votre code existant
    public function maitreStage()
    {
        return $this->maitre();
    }

    // Relation : La tâche appartient à un stage
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    // Relation : La tâche appartient à une entreprise
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    // Scope pour filtrer par statut
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTerminee($query)
    {
        return $query->where('statut', 'terminee');
    }
}
