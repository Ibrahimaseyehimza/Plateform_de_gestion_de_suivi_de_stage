<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RH extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'entreprise_id',
    ];


       // Relation : un RH appartient à une entreprise
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }
}
