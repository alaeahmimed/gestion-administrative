<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Parentt; // Assurez-vous que c'est le bon nom de modèle
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ParentsImport implements ToCollection, WithHeadingRow
{
    private $errors = [];
    private $successCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Valider les données requises
                if (empty($row['login']) || empty($row['nom']) || empty($row['prenom']) || empty($row['cin'])) {
                    $this->errors[] = [
                        'row' => $index + 2, // +2 car l'en-tête est la ligne 1 et Excel commence à 1
                        'errors' => 'Champs obligatoires manquants (email, nom, prénom ou CIN)'
                    ];
                    continue;
                }

                // Vérifier si l'email existe déjà
                if (User::where('login', $row['login'])->exists()) {
                    $this->errors[] = [
                        'row' => $index + 2,
                        'errors' => 'Email déjà utilisé'
                    ];
                    continue;
                }

                // Vérifier si le CIN existe déjà
                if (Parentt::where('cin', $row['cin'])->exists()) {
                    $this->errors[] = [
                        'row' => $index + 2,
                        'errors' => 'CIN déjà utilisé'
                    ];
                    continue;
                }

                // Créer l'utilisateur
                $user = User::create([
                    'nom' => $row['nom'],
                    'prenom' => $row['prenom'],
                    'login' => $row['login'],
                    'motDePasse' => Hash::make($row['motDePasse'] ?? 'motDePasse'), // Mot de passe par défaut
                    'role' => 'parent',
                    'email_verified_at' => now(), // Optionnel: marquer comme vérifié
                ]);

                // Créer le parent
                Parentt::create([
                    'user_id' => $user->id,
                    'cin' => $row['cin'],
                    'telephone' => $row['telephone'] ?? null,
                    'adresse' => $row['adresse'] ?? null,
                    // autres champs...
                ]);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'errors' => $e->getMessage()
                ];
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}