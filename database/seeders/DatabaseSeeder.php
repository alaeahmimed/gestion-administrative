<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Eleve;
use App\Models\Devoir;
use App\Models\Emploi;
use App\Models\Absence;
use App\Models\Parentt;
use App\Models\Bulletin;
use App\Models\Evenement;
use App\Models\Enseignant;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Administrateur;
use Illuminate\Database\Seeder;
use App\Models\JustificationAbsence;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
  // Dans DatabaseSeeder.php

public function run(): void
{
    // 1. D'abord créer les utilisateurs et rôles de base
    $adminUsers = User::factory()->count(1)->admin()->create();
    $teacherUsers = User::factory()->count(5)->enseignant()->create();
    $parentUsers = User::factory()->count(10)->parent()->create();
    $eleveUsers = User::factory()->count(10)->eleve()->create();

    // 2. Créer les instances liées
    $admins = $adminUsers->map(fn($user) => Administrateur::factory()->create(['user_id' => $user->id]));
 $enseignants = $teacherUsers->map(function ($user) {
    return Enseignant::factory()->create([
        'user_id' => $user->id,
        'classe' => json_encode([fake()->randomElement(['3e', 'Tle', '1ère', '6e', 'CM2'])])
    ]);
});
    $parents = $parentUsers->map(fn($user) => Parentt::factory()->create(['user_id' => $user->id]));
   

    
    
    // 3. Créer les emplois - VERSION CORRIGÉE
    $classes = ['CE2', 'CM1', 'CM2', '6ème', '5ème', '4ème', '3ème'];
    $cycles = ['1', '2', '3'];
    
    foreach ($cycles as $cycle) {
        foreach ($classes as $classe) {
            // Vérifier si un emploi existe déjà pour cette combinaison cycle/classe
            $exists = Emploi::where('cycle', $cycle)
                          ->where('classe', $classe)
                          ->exists();
            
            if (!$exists) {
                Emploi::factory()->create([
                    'cycle' => $cycle,
                    'classe' => $classe,
                    'administrateur_id' => $admins->random()->id,
                ]);
            }
        }
    }
    
    $emplois = Emploi::all(); // Récupérer tous les emplois créés

    // 4. Créer les élèves (avec leurs parents)
    $eleves = $eleveUsers->map(fn($user) => Eleve::factory()->create([
        'user_id' => $user->id,
        'parentt_id' => $parents->random()->id,
        'emploi_id' => $emplois->random()->id,
    ]));

    // ... (le reste de votre seeding reste inchangé)
    DB::table('users')->where('login', 'admin@test.com')->delete();

    // 5. SEULEMENT APRÈS créer les utilisateurs de test
    $adminTest = User::factory()->create([
        'nom' => 'AdminTest',
        'prenom' => 'User',
        'login' => 'admin@test.com',
        'motDePasse' => bcrypt('admin123'),
        'role' => 'admin',
    ]);
    $adminTest->administrateur()->create();

    // ... (le reste de votre code)

    $parentTest = User::factory()->create([
        'nom' => 'ParentTest',
        'prenom' => 'User',
        'login' => 'parent@test.com',
        'motDePasse' => bcrypt('parent123'),
        'role' => 'parent',
    ]);
    $parentInstance = $parentTest->parentt()->create(['cin' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}')]);

    // Maintenant on peut créer l'élève de test
    $eleveTest = Eleve::factory()->create([
        'parentt_id' => $parentInstance->id,
        'emploi_id' => $emplois->random()->id,
    ]);

    $enseignantTest = User::factory()->create([
        'nom' => 'EnseignantTest',
        'prenom' => 'User',
        'login' => 'enseignant@test.com',
        'motDePasse' => bcrypt('enseignant123'),
        'role' => 'enseignant',
    ]);
    $enseignantTest->enseignant()->create([
        'matiere' => 'Mathématiques',
        'classe' =>json_encode(['Tle','3e']),
    ]);

   
}
}






        