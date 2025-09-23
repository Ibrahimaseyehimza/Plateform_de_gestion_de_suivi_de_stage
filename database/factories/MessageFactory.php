<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expediteur_id' => \App\Models\User::inRandomOrder()->first()->id,
            'destinataire_id' => \App\Models\User::inRandomOrder()->first()->id,
            'contenu' => $this->faker->sentence(),
            'date' => now(),
        ];
    }
}
