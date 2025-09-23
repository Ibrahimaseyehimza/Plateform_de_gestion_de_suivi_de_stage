<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Crée un utilisateur spécifique
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('00000000'),
            // 'password_confirmation' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Génère plusieurs utilisateurs aléatoires avec les factories
        // User::factory(5)->create();
    }
}
