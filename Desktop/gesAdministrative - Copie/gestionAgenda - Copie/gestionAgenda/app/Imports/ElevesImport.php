<?php
namespace App\Imports;

use App\Models\{Eleve, User, Parentt};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow};
use Illuminate\Support\Facades\{Hash, Validator, DB};

class ElevesImport implements ToCollection, WithHeadingRow
{
    private $successCount = 0;
    private $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $data = $this->validateRow($row);
                
                DB::transaction(function () use ($data) {
                    // Création de l'utilisateur élève
                    $user = User::create([
                        'nom' => $data['nom'],
                        'prenom' => $data['prenom'],
                        'login' => $data['login'] ?? ($data['nom'].$data['prenom'].rand(100,999)),
                        'motDePasse' => Hash::make($data['motdepasse'] ?? 'password123'),
                        'role' => 'eleve'
                    ]);

                    // Trouver le parent par nom et prénom
                    $parent = null;
                    if (!empty($data['parent_nom']) && !empty($data['parent_prenom'])) {
                        $parent = Parentt::whereHas('user', function($q) use ($data) {
                            $q->where('nom', $data['parent_nom'])
                              ->where('prenom', $data['parent_prenom']);
                        })->first();

                        if (!$parent) {
                            throw new \Exception("Parent non trouvé: {$data['parent_nom']} {$data['parent_prenom']}");
                        }
                    }

                    // Création de l'élève
                    Eleve::create([
                        'user_id' => $user->id,
                        'nom' => $data['nom'],
                        'prenom' => $data['prenom'],
                        'classe' => $data['classe'],
                        'cycle' => $this->determineCycle($data['classe']),
                        'parentt_id' => $parent ? $parent->id : null,
                        'code_apogee' => 'IMP-'.time().'-'.$this->successCount
                    ]);

                    $this->successCount++;
                });
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'message' => $e->getMessage()
                ];
            }
        }
    }

    private function validateRow($row)
    {
        $data = [
            'nom' => $row['nom'] ?? null,
            'prenom' => $row['prenom'] ?? null,
            'login' => $row['login'] ?? null,
            'classe' => $row['classe'] ?? null,
            'motdepasse' => $row['motdepasse'] ?? null,
            'parent_nom' => $row['parent_nom'] ?? null,
            'parent_prenom' => $row['parent_prenom'] ?? null
        ];

        $validator = Validator::make($data, [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'classe' => 'required|string|max:255',
            'parent_nom' => 'nullable|string|max:255',
            'parent_prenom' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255|unique:users,login',
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        return $data;
    }

    private function determineCycle($classe)
    {
        if (preg_match('/(CM|CE|CP)/i', $classe)) {
            return 'primaire';
        } elseif (preg_match('/(6|5|4|3)ème/i', $classe)) {
            return 'college';
        } else {
            return 'lycee';
        }
    }

    public function getSuccessCount() { return $this->successCount; }
    public function getErrors() { return $this->errors; }
}