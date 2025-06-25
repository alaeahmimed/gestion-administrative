<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Parentt;
use App\Models\Emploi;
use Illuminate\Database\Eloquent\Factories\Factory;

class EleveFactory extends Factory
{
    protected $model = Eleve::class;

    public function definition(): array
    {
        // Définir les classes par cycle
        $cyclesWithClasses = [
            'Primaire' => ['CP', 'CE1', 'CE2', 'CM1', 'CM2'],
            'College' => ['6e', '5e', '4e', '3e'],
            'Lycee' => ['2nde', '1ère', 'Tle']
        ];

        // Choisir un cycle aléatoire
        $cycle = $this->faker->randomElement(array_keys($cyclesWithClasses));
        
        // Choisir une classe correspondant au cycle sélectionné
        $classe = $this->faker->randomElement($cyclesWithClasses[$cycle]);

        return [
            'user_id' => User::factory()->state(['role'=>'enseignant']),
            'code_apogee' => $this->faker->unique()->numberBetween(100000, 999999),
            'classe' => $classe,
            'cycle' => $cycle,
            'parentt_id' => Parentt::factory(),
            'emploi_id' => Emploi::factory(),
        ];
    }
}