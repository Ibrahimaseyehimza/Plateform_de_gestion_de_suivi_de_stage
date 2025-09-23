<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampagneDeStage>
 */
class CampagneDeStageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $start = $this->faker->dateTimeBetween('+0 days', '+2 months');
        $end = (clone $start)->modify('+3 months');

        return [
            'titre' => 'Campagne ' . $this->faker->word(),
            'dateLancement' => $start,
            'dateCloture' => $end,
        ];
    }
}
