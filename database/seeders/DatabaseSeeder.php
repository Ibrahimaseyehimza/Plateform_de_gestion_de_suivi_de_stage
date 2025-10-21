<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Stage;
use App\Models\Tache;
use App\Models\Message;
use App\Models\Livrable;
use App\Models\Entreprise;
use App\Models\Evaluation;
use App\Models\MaitreDeStage;
use Database\Seeders\RHSeeder;
use App\Models\CampagneDeStage;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PivotSeeder;
use Database\Seeders\MetierSeeder;
use Database\Seeders\ApprenantSeeder;
use Database\Seeders\EntrepriseSeeder;
use Database\Seeders\DepartementSeeder;
use Database\Seeders\ChefDeMetierSeeder;
use Database\Seeders\CampagneStageSeeder;
use Database\Seeders\MaitreDeStageSeeder;
use Database\Seeders\ChefDeDepartementSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call([
        //     UserSeeder::class,
        // ]);


        // Créer un admin
        // User::factory()->create([
        //     'name' => 'Admin Principal',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'admin',
        // ]);

         // Créer des entreprises
        // Entreprise::factory(10)->create();

        // Créer des campagnes
        // CampagneDeStage::factory(5)->create();

         // Créer 20 étudiants
        // $etudiants = User::factory(20)->create(['role' => 'etudiant']);

        // Créer 5 tuteurs
        // $tuteurs = User::factory(5)->create(['role' => 'tuteur']);

        // Assigner des stages
        // foreach ($etudiants as $etudiant) {
        //     Stage::factory()->create([
        //         'etudiant_id' => $etudiant->id,
        //         'entreprise_id' => Entreprise::inRandomOrder()->first()->id,
        //         'tuteur_id' => $tuteurs->random()->id,
        //     ]);
        // }

        // Créer des tâches pour chaque stage
//         Stage::all()->each(function ($stage) {
//             Tache::factory(3)->create(['stage_id' => $stage->id]);
//         });

//         // Générer 50 messages aléatoires entre utilisateurs
//         Message::factory(50)->create();


//         // Générer des livrables pour les tâches
//         Tache::all()->each(function ($tache) {
//             Livrable::factory(rand(1, 2))->create(['tache_id' => $tache->id]);
//         });

//         // Générer des évaluations pour chaque stage
//         Stage::all()->each(function ($stage) {
//             Evaluation::factory()->create(['stage_id' => $stage->id]);
// });










$this->call([
        // ChefDeDepartementSeeder::class,

        // MaitreDeStageSeeder::class,


        // DepartementSeeder::class,
        // MetierSeeder::class,
        // ChefDeMetierSeeder::class,
        // EntrepriseSeeder::class,

        StageSeeder::class,
        
        // RHSeeder::class,


        // CampagneDeStageSeeder::class,


        // ApprenantSeeder::class,
        // PivotSeeder::class,
    ]);

    // Test le seeder individuellement
        // $this->call(MaitreDeStageSeeder::class);

    }
}
