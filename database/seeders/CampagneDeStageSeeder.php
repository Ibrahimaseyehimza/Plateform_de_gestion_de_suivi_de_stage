<?php

namespace Database\Seeders;

use App\Models\CampagneDeStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampagneDeStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CampagneDeStage::create([
        'titre' => 'Campagne Stage 2025',
        'date_debut' => '2025-10-01',
        'date_fin' => '2025-12-31',
        'metier_id' => 1,
    ]);
    }
}
