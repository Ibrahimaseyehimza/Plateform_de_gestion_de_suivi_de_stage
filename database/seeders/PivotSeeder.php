<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Lier métier à campagne
        DB::table('campagne_stage_metier')->insert([
            'campagne_stage_id' => 1,
            'metier_id' => 1,
        ]);

        // Lier entreprise à campagne
        DB::table('campagne_stage_entreprise')->insert([
            'campagne_stage_id' => 1,
            'entreprise_id' => 1,
        ]);
    }
}
