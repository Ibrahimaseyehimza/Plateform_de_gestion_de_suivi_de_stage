<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluation>
 */
class EvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stage_id' => \App\Models\Stage::inRandomOrder()->first()->id,
            'note' => $this->faker->randomFloat(1, 0, 20), // note sur 20
            'commentaire' => $this->faker->sentence(10),
        ];
    }
}
