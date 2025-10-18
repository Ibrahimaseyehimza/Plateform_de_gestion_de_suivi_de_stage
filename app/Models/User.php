<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Metier;
use App\Models\Entreprise;
use App\Models\Departement;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    const ROLE_CHEF_DEPARTEMENT = 'chef_departement';
    const ROLE_CHEF_METIER = 'chef_metier';
    const ROLE_MAITRE_STAGE = 'maitre_stage';
    const ROLE_RH = 'rh';
    const ROLE_APPRENANT = 'apprenant';

    protected $fillable = [
        'name',
        // 'prenom',
        'email',
        'matricule',
        'password',
        'role',
        'must_change_password',
        // 'departement_id',
        'metier_id',
        'entreprise_id',
        // 'invitation_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations
    // public function departement()
    // {
    //     return $this->belongsTo(Departement::class);
    // }

    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }

    // public function entreprise()
    // {
    //     return $this->belongsTo(Entreprise::class);  // ✅ Corrigé : bolongsTo → belongsTo
    // }

    /**
     * Vérifier si l'utilisateur est un RH
     */
    public function isRH()
    {
        return $this->role === 'rh';
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }
}
