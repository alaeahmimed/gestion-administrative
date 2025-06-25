<?php

namespace Database\Factories;

use App\Models\JustificationAbsence;
use App\Models\Absence;
use App\Models\Parentt;
use App\Models\Administrateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class JustificationAbsenceFactory extends Factory {
    protected $model = JustificationAbsence::class;

    public function definition(): array {
        return [
            'raison' => $this->faker->sentence,
            'fichier' => 'justification_' . $this->faker->uuid . '.pdf',
            'absence_id' => Absence::factory(),
            'parentt_id' => Parentt::factory(),
            'administrateur_id' => Administrateur::factory(),
        ];
    }
}
