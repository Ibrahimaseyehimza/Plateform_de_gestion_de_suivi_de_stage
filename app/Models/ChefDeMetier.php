<?php

namespace App\Models;

use App\Models\Metier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChefDeMetier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'metier_id'
    ];

    public function metier() {
        return $this->belongsTo(Metier::class, 'metier_id');
    }
}
