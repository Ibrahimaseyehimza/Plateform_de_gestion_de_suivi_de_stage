<?php

// namespace App\Models;

// use App\Models\Metier;
// use App\Models\MaitreDeStage;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

// class Apprenant extends Model
// {
//     use HasFactory;


//     protected $fillable = [
//     'nom',
//     'prenom',
//     'email',
//     'metier_id',
//     'maitre_de_stage_id',
// ];



//     public function metier() {
//         return $this->belongsTo(Metier::class);
//     }

// public function chefDeStage() {
//         return $this->belongsTo(MaitreDeStage::class);
//     }
// }














namespace App\Models;

use App\Models\Metier;
use App\Models\MaitreDeStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Si vous voulez qu'ils se connectent
use Laravel\Sanctum\HasApiTokens;

class Apprenant extends Authenticatable // Pour l'authentification
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'matricule',
        'metier_id',
        // 'maitre_de_stage_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed', // Laravel 10+
    ];

    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }

    public function maitreDeStage()
    {
        return $this->belongsTo(MaitreDeStage::class);
    }
}
