<?php

namespace App\Models;

use App\Models\Metier;
use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampagneDeStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'metier_id',
        'statut',
    ];


    // public function metiers() {
    //     return $this->belongsToMany(Metier::class);
    // }

    // public function entreprises() {
    //     return $this->belongsToMany(Entreprise::class);
    // }


    // public function metiers() {
    //     return $this->belongsToMany(Metier::class, 'campagne_stage_metier');
    // }

    // 🔗 Relation avec Metier
        public function metier()
        {
            return $this->belongsTo(Metier::class, 'metier_id');
        }

    public function entreprises() {
        return $this->belongsToMany(Entreprise::class, 'campagne_stage_entreprise');
    }

        //Ajouter cette relation
    public function chefDepartement()
    {
        return $this->belongsTo(User::class, 'chef_departement_id');
        // Ajustez 'chef_departement_id' selon le nom de votre colonne
    }



}


