<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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


    // public function tache()
    // {
    //     return $this->belongsTo(Tache::class);
    // }

        public function tache()
        {
            return $this->belongsTo(Tache::class, 'tache_id');
        }

    // public function apprenant() {
    //     return $this->belongsTo(User::class, 'apprenant_id');
    // }


    public function apprenant()
    {
        return $this->belongsTo(User::class, 'apprenant_id');
    }




}
