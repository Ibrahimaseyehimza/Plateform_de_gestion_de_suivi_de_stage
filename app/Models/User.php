<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Tache;
use App\Models\Metier;
use App\Models\Entreprise;
use App\Models\Departement;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // ✅ AJOUT ICI

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ✅ maintenant ce trait est reconnu

    const ROLE_CHEF_DEPARTEMENT = 'chef_departement';
    const ROLE_CHEF_METIER = 'chef_metier';
    const ROLE_MAITRE_STAGE = 'maitre_stage';
    const ROLE_RH = 'rh';
    const ROLE_APPRENANT = 'apprenant';

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'matricule',
        'password',
        'role',
        'must_change_password',
        'metier_id',
        'entreprise_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }

    // public function entreprise()
    // {
    //     return $this->belongsTo(Entreprise::class, 'entreprise_id');
    // }

    public function isRH()
    {
        return $this->role === self::ROLE_RH;
    }

        // Tâches créées (pour maître de stage)
    public function tachesCreees()
    {
        return $this->hasMany(Tache::class, 'maitre_stage_id');
    }

    // Tâches assignées (pour apprenant)
    public function tachesAssignees()
    {
        return $this->hasMany(Tache::class, 'apprenant_id');
    }


    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    // public function livrables()
    // {
    //     return $this->hasMany(Livrable::class);
    // }

    public function livrables()
    {
        return $this->hasMany(Livrable::class, 'apprenant_id');
    }
}
