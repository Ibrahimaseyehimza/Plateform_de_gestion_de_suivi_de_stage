<?php

namespace App\Models;

use App\Models\Apprenant;
use App\Models\ChefDeMetier;
use App\Models\CampagneDeStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Metier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        // 'departement_id',
        // 'chef_de_metier_id',
    ];


     /**
     * Relation : un métier a un chef de métier
     */
    // public function chefDeMetier() {
    //     return $this->belongsTo(ChefDeMetier::class);
    // }

     // Ou si un métier n'a qu'un seul chef :
        public function chefDeMetier()
        {
            return $this->hasOne(ChefDeMetier::class, 'metier_id');
        }

    // public function campagnes() {
    //     return $this->belongsToMany(CampagneDeStage::class);
    // }

     /**
     * Relation : un métier appartient à un département
     */
    // public function departement()
    // {
    //     return $this->belongsTo(Departement::class);
    // }

    // public function campagnes() {
    //     return $this->belongsToMany(CampagneDeStage::class, 'campagne_stage_metier');
    // }

     /**
     * Relation : un métier a plusieurs apprenants
     */
    public function apprenants()
    {
        return $this->hasMany(User::class, 'metier_id')
                    ->where('role', 'apprenant');
    }

    public function entreprises()
    {
        return $this->hasMany(Entreprise::class);
    }

}
