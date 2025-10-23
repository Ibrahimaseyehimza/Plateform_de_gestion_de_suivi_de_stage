<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'maitre_stage_id',
        'etudiant_id',
        'titre',
        'description',
        'date_echeance',
        'priorite',  // ✅ Ajouté
        'statut',    // ✅ Ajouté
    ];

    protected $casts = [
        'date_echeance' => 'date',
    ];

    // Relations
    public function maitreStage()
    {
        return $this->belongsTo(User::class, 'maitre_stage_id');
    }

    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}