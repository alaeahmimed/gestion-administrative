<?php

namespace Database\Factories;

use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Enseignant;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceFactory extends Factory {
    protected $model = Absence::class;

    public function definition(): array {
        return [
            'status' =>$this->faker->randomElement(['non justifiée', 'justifiée']),
            'dateEnvoi' =>$this->faker->date,
            'enseignant_id' => Enseignant::factory(),
            'eleve_id' => Eleve::factory(),
        ];
    }
}
