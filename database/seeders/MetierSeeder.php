<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Metier;
use App\Models\ChefDeMetier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class MetierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //     Metier::create([
    //     'nom' => 'Développement Web',
    //     'departement_id' => 1,
    //     'chef_de_metier_id' => 1,
    // ]);

            $metiers = [
                [
                'nom' => 'Developpement Web et Mobile',
                'description' => 'Conception et développement d\'applications web et mobiles.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nom' => 'RT',
                'description' => 'Réseaux et Télécommunications.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nom' => 'ASRI',
                'description' => 'Administration Systèmes et Réseaux Informatiques.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
         ];

         DB::table('metiers')->insert($metiers);

    }
}
