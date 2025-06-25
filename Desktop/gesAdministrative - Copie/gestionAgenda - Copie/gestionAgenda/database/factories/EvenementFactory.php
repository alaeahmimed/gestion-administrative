<?php

namespace Database\Factories;

use App\Models\Evenement;
use App\Models\Administrateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvenementFactory extends Factory {
    protected $model = Evenement::class;

    public function definition(): array {
        return [
            'dateDebut' => $this->faker->date,
            'dateFin' => $this->faker->date,
            'heure' => $this->faker->time,
            'description' => $this->faker->sentence,
            'administrateur_id' => Administrateur::factory(),
        ];
    }
}
