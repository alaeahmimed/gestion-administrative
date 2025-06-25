<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Enseignant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EnseignantsImport implements ToCollection, WithHeadingRow
{
    private $errors = [];
    private $successCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Nettoyer les données
                $row = array_map('trim', $row->toArray());
                
                // Valider les données requises
                $validator = Validator::make($row, [
                    'nom' => 'required|string|max:255',
                    'prenom' => 'required|string|max:255',
                    'login' => 'required|email|unique:users,login',
                    'matiere' => 'required|string|max:255',
                    'classe' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = [
                        'row' => $index + 2, // +2 pour la ligne Excel (1 = en-tête)
                        'message' => implode(' | ', $validator->errors()->all())
                    ];
                    continue;
                }

                // Créer l'utilisateur
                $user = User::create([
                    'nom' => $row['nom'],
                    'prenom' => $row['prenom'],
                    'login' => $row['login'],
                    'motDePasse' => Hash::make($row['motdepasse'] ?? 'password123'),
                    'role' => 'enseignant',
                ]);

                // Gérer les classes (séparées par des virgules si besoin)
                $classes = !empty($row['classe']) ? 
                    array_map('trim', explode(',', $row['classe'])) : 
                    [];

                // Créer l'enseignant
                Enseignant::create([
                    'user_id' => $user->id,
                    'matiere' => $row['matiere'],
                    'classe' => !empty($classes) ? json_encode($classes) : null,
                ]);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'message' => 'Erreur système: ' . $e->getMessage()
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