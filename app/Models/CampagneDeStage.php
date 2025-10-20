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


    // 🔗 Relation avec Metier
        public function metier()
        {
            return $this->belongsTo(Metier::class, 'metier_id');
        }

    // public function entreprises() {
    //     return $this->belongsToMany(Entreprise::class, 'campagne_stage_entreprise');
    // }

    public function entreprises()
    {
        return $this->belongsToMany(Entreprise::class, 'campagne_stage_entreprise')
            ->withPivot('statut', 'capacite_max', 'message_refus', 'postulants_count')
            ->withTimestamps();
    }

        //Ajouter cette relation
    public function chefDepartement()
    {
        return $this->belongsTo(User::class, 'chef_departement_id');
        // Ajustez 'chef_departement_id' selon le nom de votre colonne
    }



}


