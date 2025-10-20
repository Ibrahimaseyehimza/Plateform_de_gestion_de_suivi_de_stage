<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoumissionMaitreStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'rh_id',
        'maitre_stage_id',
        'campagne_id',
        'message',
        'etudiants_ids',
        'statut',
        'commentaire_maitre'
    ];

    protected $casts = [
        'etudiants_ids' => 'array', // Convertir automatiquement en array
    ];

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function rh()
    {
        return $this->belongsTo(User::class, 'rh_id');
    }

    public function maitreStage()
    {
        return $this->belongsTo(User::class, 'maitre_stage_id');
    }

    public function campagne()
    {
        return $this->belongsTo(CampagneDeStage::class, 'campagne_id');
    }

    // Récupérer les étudiants de cette soumission
    public function etudiants()
    {
        return User::whereIn('id', $this->etudiants_ids)->get();
    }
}
