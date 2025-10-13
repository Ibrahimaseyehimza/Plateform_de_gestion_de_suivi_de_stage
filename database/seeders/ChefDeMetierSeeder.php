<?php

namespace Database\Seeders;

use App\Models\ChefDeMetier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ChefDeMetierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            //      ChefDeMetier::create([
            //     'nom' => 'Diop',
            //     'prenom' => 'Fatou',
            //     'email' => 'fatou.diop@ufr.example.com',
            //     'metier_id' => 1,
            // ]);
        $chefsDeMetier = [
            [
                'nom' => 'Diallo',
                'prenom' => 'Amadou',
                'password' => bcrypt('00000000'),
                'email' => 'amadou@gmail.com',
                'metier_id' => 1, // Développeur Web
            ],

            [
                'nom' => 'Fall',
                'prenom' => 'diakhou',
                'email' => 'diakhou@gmail.com',
                'metier_id' => 2, // Data Scientist
            ],
            [
                'nom' => 'Sarr',
                'prenom' => 'Mariama',
                'email' => 'mariama@gmail.com',
                'metier_id' => 3, // Designer UI/UX
            ],
        ];

        foreach ($chefsDeMetier as $chef) {
            ChefDeMetier::create($chef);
        }
    }

}
