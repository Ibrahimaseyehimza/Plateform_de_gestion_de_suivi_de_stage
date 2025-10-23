<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'apprenant_id',
        'entreprise_id',
        'maitre_stage_id',
        'priorite',
        'statut',
        'date_echeance',
    ];


      protected $casts = [
        'date_echeance' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

       // Relation avec l'apprenant (étudiant)
    public function user()
    {
        return $this->belongsTo(User::class, 'apprenant_id')
            ->select('id', 'name', 'prenom', 'email', 'matricule');
    }

    // Relation avec le maître de stage
    public function maitreStage()
    {
        return $this->belongsTo(User::class, 'maitre_stage_id')
            ->select('id', 'name', 'prenom', 'email');
    }


     public function maitre()
    {
        return $this->belongsTo(User::class, 'maitre_stage_id');
    }

    // Relation avec l'entreprise
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }


    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function livrables()
    {
        return $this->hasMany(Livrable::class);
    }


    // ✅ Relation avec l'apprenant (User)
    public function apprenant()
    {
        return $this->belongsTo(User::class, 'apprenant_id');
    }

    // public function stage()
    // {
    //     return $this->belongsTo(Stage::class);
    // }
}