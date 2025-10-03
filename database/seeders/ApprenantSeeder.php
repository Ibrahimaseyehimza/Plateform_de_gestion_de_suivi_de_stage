<?php

namespace Database\Seeders;

use App\Models\Metier;
use App\Models\Apprenant;
use App\Models\MaitreDeStage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApprenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maitre = MaitreDeStage::first();
         Apprenant::create([
        'nom' => 'Fall',
        'prenom' => 'Ibrahima',
        'email' => 'ibrahima.fall@etudiant.com',
        'metier_id' => 1,
        'maitre_de_stage_id' => $maitre->id,
    ]);
    }
}
