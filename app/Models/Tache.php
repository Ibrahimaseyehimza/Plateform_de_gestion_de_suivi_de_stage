<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'titre',
        'description',
        'dateLimite',
    ];


    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function livrables()
    {
        return $this->hasMany(Livrable::class);
    }
}
