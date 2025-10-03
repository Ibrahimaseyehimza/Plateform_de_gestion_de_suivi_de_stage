<?php

namespace App\Models;

use App\Models\Metier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departement extends Model
{
    use HasFactory;


    protected $fillable = [
        'nom',
        'chef_de_departement_id',
    ];

    public function metiers() {
        return $this->hasMany(Metier::class);
    }
}
