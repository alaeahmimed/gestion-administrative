<?php

namespace Database\Factories;

use App\Models\Administrateur;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministrateurFactory extends Factory {
    protected $model = Administrateur::class;

    public function definition(): array {
        return [
            'user_id' => User::factory()->state(['role'=>'admin']),
        ];
    }
}
