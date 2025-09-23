<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'entreprise_id',
        'tuteur_id',
        'dateDebut',
        'dateFin',
    ];

      public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function tuteur()
    {
        return $this->belongsTo(User::class, 'tuteur_id');
    }   
}
