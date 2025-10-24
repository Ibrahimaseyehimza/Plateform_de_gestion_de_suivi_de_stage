<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'entreprise_id',
        'maitre_stage_id',
        'campagne_id',
        'metier_id',
        'statut',
        'date_debut',
        'date_fin',
        'commentaire',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // Relations
    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function maitreStage()
    {
        return $this->belongsTo(User::class, 'maitre_stage_id');
    }

    public function campagne()
    {
        return $this->belongsTo(CampagneDeStage::class, 'campagne_id');
    }

    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }
}