<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Departement::create([
            'nom' => 'Electronique Informatique télécommnunication',
            'chef_de_departement_id' => 1,

        ]);
    }
}
