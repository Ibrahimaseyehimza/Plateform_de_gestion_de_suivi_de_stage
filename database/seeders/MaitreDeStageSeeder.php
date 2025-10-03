<?php

namespace Database\Seeders;

use App\Models\MaitreDeStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaitreDeStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MaitreDeStage::create([
        'nom' => 'Ndoye',
        'prenom' => 'Alioune',
        'email' => 'alioune.ndoye@entreprise.com',
    ]);
    }
}
