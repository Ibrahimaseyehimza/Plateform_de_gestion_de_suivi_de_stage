<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livrable>
 */
class LivrableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tache_id' => \App\Models\Tache::inRandomOrder()->first()->id,
            'fichier' => 'livrables/' . $this->faker->uuid . '.pdf', // chemin fictif
            'commentaire' => $this->faker->sentence(),
        ];
    }
}
