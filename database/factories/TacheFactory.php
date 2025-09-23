<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tache>
 */
class TacheFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stage_id' => \App\Models\Stage::factory(),
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'dateLimite' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
        ];
    }
}
