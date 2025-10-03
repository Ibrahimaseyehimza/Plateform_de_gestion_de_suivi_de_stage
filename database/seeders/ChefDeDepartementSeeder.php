<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChefDeDepartement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ChefDeDepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChefDeDepartement::create([
        'nom' => 'Sow',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.sow@ufr.example.com',
         'departement_id' => 1,
    ]);
    }
}
