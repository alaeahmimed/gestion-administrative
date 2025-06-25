<?php

namespace Database\Factories;

use App\Models\Parentt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParenttFactory extends Factory {
    protected $model = Parentt::class;

    public function definition(): array {
        return [
            'cin' => $this->faker->regexify('[A-Z]{1,2}[0-9]{6}'),
            'user_id' => User::factory()->state(['role'=>'parent']),
        ];
    }
}
