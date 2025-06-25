<?php

namespace Database\Factories;

use App\Models\Devoir;
use App\Models\Enseignant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

class DevoirFactory extends Factory {
    protected $model = Devoir::class;

    public function definition(): array {
        $filename = 'devoirs/' . Str::random(10) . '.pdf';
    Storage::disk('public')->put($filename, 'Contenu factice de devoir');

    return [
        'titre' => $this->faker->sentence,
        'description' => $this->faker->paragraph,
        'dateLimite' => $this->faker->date,
        'fichierJoint' => $filename,
        'classe'=>$this ->faker ->randomElement([
            'CP', 'CE2', 'CM1',     // Primaire
                        '6e', '5e', '4e', '3e'                // Collège
                        ]) ,
        'enseignant_id' => Enseignant::factory(),
    ];
    }
}
