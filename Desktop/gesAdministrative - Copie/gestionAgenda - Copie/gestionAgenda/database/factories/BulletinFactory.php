<?php

namespace Database\Factories;

use App\Models\Bulletin;
use App\Models\Eleve;
use App\Models\Administrateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class BulletinFactory extends Factory {
    protected $model = Bulletin::class;

    public function definition(): array {
        return [
            'fichierPdf' => 'bulletin_' . $this->faker->uuid . '.pdf',
            'administrateur_id' => Administrateur::factory(),
            'eleve_id' => Eleve::factory(),
        ];
    }
}
