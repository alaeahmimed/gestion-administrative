<?php

namespace Database\Factories;
use App\Models\Administrateur;
use Illuminate\Support\Str;
use App\Models\Emploi;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmploiFactory extends Factory
{
    protected $model = Emploi::class;

public function definition(): array
{
    // Récupérer toutes les classes déjà utilisées
    $classesExistantes = Emploi::pluck('classe')->toArray();
    
    // Classes disponibles
    $classesDisponibles = array_diff([
        'CE1', 'CE2', 'CM1', 'CM2', '6e', '4e', '3e'
    ], $classesExistantes);

    // Si toutes les classes sont utilisées, en choisir une au hasard
    $classe = empty($classesDisponibles) 
        ? $this->faker->randomElement(['CE1', 'CE2', 'CM1', 'CM2', '6e', '4e', '3e'])
        : $this->faker->randomElement($classesDisponibles);

    return [
       // Dans database/factories/EmploiFactory.php
        'cycle' => $this->faker->randomElement(['1', '2', '3']),
        'classe' => $this->faker->randomElement(['CE2', 'CM1', 'CM2', '6ème', '5ème', '4ème', '3ème']),
        'file_path' => 'storage/emplois/' . Str::uuid() . '.pdf',
    
        'administrateur_id' => Administrateur::factory(),
    ];
}
}