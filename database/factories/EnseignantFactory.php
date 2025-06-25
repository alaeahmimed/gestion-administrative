<?php

namespace Database\Factories;

use App\Models\Enseignant;
use App\Models\User;
use App\Models\Emploi;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnseignantFactory extends Factory {
    protected $model = Enseignant::class;

    public function definition(): array {
        return [
            'matiere' => $this->faker->word,
            'user_id' => User::factory()->state(['role'=>'enseignant']),
             'classe' =>json_encode([ $this->faker->randomElement([
                        'CP', 'CE1', 'CE2', 'CM1', 'CM2',     // Primaire
                        '6e', '5e', '4e', '3e'                // Collège
                        ]) ]),
            
        ];
    }
}
