<?php

namespace App\Models;

use App\Models\Apprenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaitreDeStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
    ];

    public function apprenants()
    {
        return $this->hasMany(Apprenant::class);
    }
}
