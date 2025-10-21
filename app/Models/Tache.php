<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'statut',
    ];


    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function livrables()
    {
        return $this->hasMany(Livrable::class);
    }

     public function maitre()
    {
        return $this->belongsTo(User::class, 'maitre_stage_id');
    }

    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }



}
