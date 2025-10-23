<?php

namespace App\Models;

use App\Models\RH;
use App\Models\Stage;
use App\Models\CampagneDeStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entreprise extends Model
{
    use HasFactory;

      protected $fillable = [
        'nom',
        'adresse',
        'email',
        'telephone',
        'latitude',
        'longitude',
        'metier_id',
    ];


     // Accessor pour obtenir les coordonnées formatées
    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude
            ];
        }
        return null;
    }

    public function stages()
    {
        return $this->hasMany(Stage::class);
    }

    public function rh() {
        return $this->hasOne(RH::class);
    }

    // public function campagnes() {
    //     return $this->belongsToMany(CampagneDeStage::class);
    // }

    // public function campagnes() {
    //     return $this->belongsToMany(CampagneDeStage::class, 'campagne_stage_entreprise');
    // }


    // App\Models\Entreprise.php
    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }

    public function campagnes()
    {
        return $this->belongsToMany(CampagneDeStage::class, 'campagne_stage_entreprise')
                    ->withPivot('capacite_max', 'postulants_count')
                    ->withTimestamps();
    }

    public function etudiantsAffectes()
    {
        return $this->hasMany(\App\Models\DemandeDeStage::class, 'entreprise_id')
            ->whereIn('statut', ['acceptee', 'reorientee']);
    }

    // public function rh()
    // {
    //     return $this->hasOne(\App\Models\User::class)->where('role', 'rh');
    // }

    public function maitreStage()
    {
        return $this->hasOne(User::class, 'entreprise_id')->where('role', 'maitre_stage');
    }

    public function etudiants()
    {
        return $this->hasMany(User::class, 'entreprise_id')->where('role', 'apprenant');
    }

       // Relation avec les utilisateurs (maîtres de stage, apprenants)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relation avec les tâches
    public function taches()
    {
        return $this->hasMany(Tache::class);
    }









}
