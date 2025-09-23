<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampagneDeStage extends Model
{
    use HasFactory;

    protected $fillable = [
                            'titre',
                             'dateLancement',
                              'dateCloture'

                            ];


}


