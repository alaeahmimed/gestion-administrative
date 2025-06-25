<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'login' => $this->faker->unique()->safeEmail,
            'motDePasse' => bcrypt('password'),
            'role' => $this->faker->randomElement(['admin', 'enseignant', 'parent','eleve']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is a teacher.
     */
    public function enseignant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'enseignant',
        ]);
    }
    
    /**
     * Indicate that the user is a admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a parent.
     */
    public function parent(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'parent',
        ]);
    }

     public function eleve(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'eleve',
        ]);
    }
}
