<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {



        // ✅ 5. Créer des stages
        $stages = [
            [
                'titre' => 'Développement d’une application web de gestion interne',
                'description' => 'Développement full stack Laravel + Vue.js pour une entreprise locale.',
                'date_debut' => Carbon::create(2025, 7, 1),
                'date_fin' => Carbon::create(2025, 9, 30),
                'etat' => 'validé',
                'campagne_id' => 1,
                'entreprise_id' => 1,
                'etudiant_id' => 6,
                'rapport_url' => 'https://drive.google.com/rapport_stage_1.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('stages')->insert($stages);
    }
}
