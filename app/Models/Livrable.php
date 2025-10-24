<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livrable extends Model
{
    use HasFactory;

     protected $fillable = [
        // 'tache_id',
        // 'fichier',
        // 'commentaire',

        'tache_id',
        'apprenant_id',
        'titre',
        'description',
        'fichier',
        'statut',
        'note',
        'commentaire',
    ];

    protected $appends = ['fichier_url'];

    public function getFichierUrlAttribute()
    {
        if (!$this->fichier) return null;
        return asset('storage/' . $this->fichier);
    }


    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function apprenant() {
        return $this->belongsTo(User::class, 'apprenant_id');
    }



}
