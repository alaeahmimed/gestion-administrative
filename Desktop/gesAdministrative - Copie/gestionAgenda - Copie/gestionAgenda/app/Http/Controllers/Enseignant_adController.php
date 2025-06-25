<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Enseignant;
use App\Models\Eleve;

use App\Models\User;
use Illuminate\Http\Request;
use App\Imports\EnseignantsImport;
use Maatwebsite\Excel\Facades\Excel;

class Enseignant_adController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Enseignant::with(['user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%");
                })
                ->orWhere('matiere', 'like', "%{$search}%");
            });
        }

        $enseignants = $query->paginate(10)->appends(['search' => $search]);

        return view('Admin.listerEnseignant', compact('enseignants'));
    }

  public function create()
{
    // Cycles par défaut
    $defaultCycles = ['Primaire', 'College', 'Lycee'];
    
    // Récupérer les cycles existants dans la base
    $dbCycles = DB::table('eleves')
                ->select('cycle')
                ->distinct()
                ->whereNotNull('cycle')
                ->pluck('cycle')
                ->toArray();
    
    // Fusionner les cycles (éviter les doublons)
    $cycles = array_unique(array_merge($defaultCycles, $dbCycles));
    sort($cycles);
    
    // ... reste du code inchangé ...

    
    
    // Récupérer toutes les classes avec leur cycle associé
    $classesByCycle = DB::table('eleves')
        ->select('cycle', 'classe')
        ->whereNotNull('cycle')          // Exclure les valeurs nulles
        ->whereNotNull('classe')         // Exclure les valeurs nulles
        ->distinct()
        ->get()
        ->groupBy('cycle')
        ->map(function ($items) {
            return $items->pluck('classe')->unique()->sort();
        });

    return view('Admin.addEnseignant', [
        'cycles' => $cycles,
        'classesByCycle' => $classesByCycle
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'login' => 'required|string|email|unique:users,login',
        'motDePasse' => 'required|string|min:6',
        'matiere' => 'required|string',
        'classe' => 'required|array', // Assurez-vous que classe est un tableau
        'classe.*' => 'string' // Chaque élément du tableau doit être une string
    ]);

    $user = User::create([
        'nom' => $validated['nom'],
        'prenom' => $validated['prenom'],
        'login' => $validated['login'],
        'motDePasse' => bcrypt($validated['motDePasse']),
        'role' => 'enseignant'
    ]);

    $enseignant = Enseignant::create([
        'user_id' => $user->id,
        'matiere' => $validated['matiere'],
        'classe' => json_encode($validated['classe']) // Encodez le tableau en JSON
    ]);

    return redirect()->route('listerEnseignant.index')
                     ->with('success', 'Enseignant ajouté avec succès.');
}
    public function edit($id)
{
    $enseignant = Enseignant::findOrFail($id);
 $cycles = ['Primaire', 'College', 'Lycee'];
    // Récupération des classes groupées par cycle depuis les élèves (comme tu fais déjà)
    $classesByCycle = Eleve::whereNotNull('cycle')
        ->whereNotNull('classe')
        ->get()
        ->groupBy('cycle')
        ->map(function ($group) {
            return $group->pluck('classe')->unique()->values();
        });

    // Assure-toi de passer cette variable à la vue
    return view('Admin.editEnseignant', compact('enseignant', 'cycles'),[
        'enseignant' => $enseignant,
        'classesByCycle' => $classesByCycle,
        
    ]);
}

   public function update(Request $request, $id)
{
    $enseignant = Enseignant::with('user')->findOrFail($id);

    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'login' => 'required|email|unique:users,login,'.$enseignant->user->id,
        'motDePasse' => 'nullable|string|min:6',
        'matiere' => 'required|string|max:255',
        'cycle' => 'required|string',
        'classe' => 'required|array',
        'classe.*' => 'string'
    ]);

    DB::transaction(function () use ($enseignant, $validated) {
        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login']
        ];

        if (!empty($validated['motDePasse'])) {
            $userData['motDePasse'] = bcrypt($validated['motDePasse']);
        }

        // Mettre à jour l'utilisateur
        $enseignant->user->update($userData);

        // Mettre à jour l'enseignant
        $enseignant->update([
            'matiere' => $validated['matiere'],
            'cycle' => $validated['cycle'],
            'classe' => json_encode($validated['classe'])
        ]);
    });

    return redirect()->route('listerEnseignant.index')
                     ->with('success', 'Enseignant modifié avec succès.');
}

    public function destroy(Enseignant $enseignant)
    {
        try {
            DB::transaction(function () use ($enseignant) {
                $enseignant->user()->delete();
                $enseignant->delete();
            });

            return redirect()->route('listerEnseignant.index')
                             ->with('success', 'Enseignant supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('listerEnseignant.index')
                             ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
  public function import(Request $request) 
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048'
    ]);

    try {
        $import = new EnseignantsImport();
        Excel::import($import, $request->file('file'));

        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();

        if ($successCount === 0 && empty($errors)) {
            return back()->with('error', 'Aucune donnée valide trouvée dans le fichier.');
        }

        $message = $successCount . ' enseignant(s) importé(s) avec succès';
        $alertType = 'success';

        if (!empty($errors)) {
            $message .= ' | ' . count($errors) . ' erreur(s)';
            $alertType = 'warning';
            
            // Stocker les erreurs dans la session pour affichage détaillé
            session()->flash('import_errors', $errors);
        }

        return back()->with($alertType, $message);

    } catch (\Exception $e) {
        return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
    }
}
}