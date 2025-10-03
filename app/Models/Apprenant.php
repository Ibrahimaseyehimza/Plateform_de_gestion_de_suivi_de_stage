<?php

namespace App\Models;

use App\Models\Metier;
use App\Models\MaitreDeStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apprenant extends Model
{
    use HasFactory;


    protected $fillable = [
    'nom',
    'prenom',
    'email',
    'metier_id',
    'maitre_de_stage_id',
];



    public function metier() {
        return $this->belongsTo(Metier::class);
    }

public function chefDeStage() {
        return $this->belongsTo(MaitreDeStage::class);
    }
}
