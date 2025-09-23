<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stage>
 */
class StageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $dateDebut = $this->faker->dateTimeBetween('+0 days', '+1 month');
        $dateFin = (clone $dateDebut)->modify('+2 months');

        return [
              'etudiant_id' => \App\Models\User::factory(),
            'entreprise_id' => \App\Models\Entreprise::factory(),
            'tuteur_id' => \App\Models\User::factory(['role' => 'tuteur']),
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
        ];
    }
}
