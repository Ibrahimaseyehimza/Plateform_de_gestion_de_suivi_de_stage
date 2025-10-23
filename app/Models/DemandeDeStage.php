<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeDeStage extends Model
{
    use HasFactory;


     use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'campagne_id',
        'entreprise_id',
        'adresse_1',
        'adresse_2',
        'telephone',
        'statut',
    ];

    public function etudiant() {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function campagne() {
        return $this->belongsTo(CampagneDeStage::class);
    }

    public function entreprise() {
        return $this->belongsTo(Entreprise::class);
    }
}
