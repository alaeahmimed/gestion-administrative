<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Parentt;
use Illuminate\Http\Request;
use App\Imports\ElevesImport;
use Maatwebsite\Excel\Facades\Excel;

class Eleve_adController extends Controller
{
    public function index(Request $request)
{
    $classes = Eleve::select('classe')
        ->distinct()
        ->orderBy('classe')
        ->pluck('classe');
    
    $selectedClass = $request->input('classe');
    
    $eleves = Eleve::with(['parentt', 'bulletin', 'user'])
        ->when($selectedClass, fn($query) => $query->where('classe', $selectedClass))
        ->join('users', 'users.id', '=', 'eleves.user_id') // Jointure avec la table users
        ->orderBy('users.nom') // Tri par nom dans users
        ->orderBy('users.prenom') // Tri par prénom dans users
        ->select('eleves.*') // Sélectionner les colonnes de eleves
        ->paginate(10)
        ->appends(['classe' => $selectedClass]);

    return view('Admin.listerEleves', compact('eleves', 'classes', 'selectedClass'));
}
public function store(Request $request)
{
    \DB::enableQueryLog(); // Active le logging des requêtes

    try {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'cycle' => 'required|in:Primaire,College,Lycee',
            'classe' => 'required|string|max:255',
            'parentt_id' => 'required|exists:parentts,id',
            'login' => 'required|string|max:255|unique:users,login',
            'motDePasse' => 'required|string|min:6'
        ]);

        \Log::info('Données validées:', $validated);

        DB::beginTransaction();

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'],
            'motDePasse' => \Hash::make($validated['motDePasse']),
            'role' => 'eleve'
        ]);

        \Log::info('Utilisateur créé:', $user->toArray());

        // Création de l'élève
        $eleve = Eleve::create([
            'user_id' => $user->id,
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'classe' => $validated['classe'],
            'cycle' => $validated['cycle'],
            'parentt_id' => $validated['parentt_id'],
            'code_apogee' => 'ELEVE-'.strtoupper(substr($validated['nom'], 0, 3)).'-'.time()
        ]);

        \Log::info('Élève créé:', $eleve->toArray());
        \Log::info('Requêtes exécutées:', \DB::getQueryLog());

        DB::commit();

        return redirect()->route('listerEleves.index')
               ->with('success', 'Élève ajouté avec succès');

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Erreur de validation:', $e->errors());
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Erreur complète:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->withInput()
               ->with('error', 'Erreur lors de l\'ajout: '.$e->getMessage());
        
    }
}
public function create()
{
    $parents = Parentt::with('user')->get(); // Only if you need parents for selection
    return view('Admin.addEleve', compact('parents'));
}


    public function edit(Eleve $eleve)
{
    // Chargement minimal des relations nécessaires
    $eleve->load(['user', 'parentt.user']);
    
    return view('Admin.editEleve', [
        'eleve' => $eleve,
    ]);
}

   public function update(Request $request, Eleve $eleve)
{
    // Validation des données
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'cycle' => 'required|in:primaire,college,lycee',
        'classe' => 'required|string|max:50',
        'login' => 'required|string|max:255|unique:users,login,'.$eleve->user->id,
        'motDePasse' => 'nullable|string|min:8',
        'parentt_id' => 'required|exists:parentts,id',
    ]);

    try {
        DB::beginTransaction();

        // Mettre à jour l'utilisateur associé
        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'],
        ];

        // Mettre à jour le mot de passe seulement si fourni
        if (!empty($validated['motDePasse'])) {
            $userData['motDePasse'] = Hash::make($validated['motDePasse']);
        }

        $eleve->user->update($userData);

        // Mettre à jour les données spécifiques à l'élève
        $eleve->update([
            'cycle' => $validated['cycle'],
            'classe' => $validated['classe'],
            'parentt_id' => $validated['parentt_id'],
        ]);

        DB::commit();

        return redirect()->route('listerEleves.index')
            ->with('success', 'Élève mis à jour avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
    }
}

    public function destroy(Eleve $eleve)
{
    try {
        DB::transaction(function () use ($eleve) {
            // Supprimer d'abord l'utilisateur associé s'il existe
            if (method_exists($eleve, 'user')) {  // <-- Ici, il manquait la parenthèse fermante
                $eleve->user()->delete();
            }
            
            // Ensuite supprimer l'élève
            $eleve->delete();
        });

        return redirect()->route('listerEleves.index')
            ->with('success', 'Élève supprimé avec succès.');
    } catch (\Exception $e) {
        return redirect()->route('listerEleves.index')
            ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
    }
}
  public function searchParents(Request $request)
{
    $query = $request->input('query');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }

    $parents = Parentt::with(['user', 'eleves'])
        ->where(function($q) use ($query) {
            $q->whereHas('user', function($q2) use ($query) {
                $q2->where('nom', 'like', "%$query%")
                   ->orWhere('prenom', 'like', "%$query%");
            })
            ->orWhere('cin', 'like', "%$query%");
        })
        ->limit(10)
        ->get();

    return response()->json(
        $parents->map(function($parent) {
            $classes = $parent->eleves->pluck('classe')->unique();
            return [
                'id' => $parent->id,
                'nom' => $parent->user->nom,
                'prenom' => $parent->user->prenom,
                'cin' => $parent->cin,
                'classes_str' => $classes->join(', ')
            ];
        })
    );
}

    public function import(Request $request) 
{
    // Debug: Vérifiez si la méthode est appelée
    \Log::info('Import method called', ['file' => $request->hasFile('file')]);

    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048'
    ]);

    try {
        // Debug: Vérifiez le fichier reçu
        \Log::info('File received', [
            'name' => $request->file('file')->getClientOriginalName(),
            'size' => $request->file('file')->getSize()
        ]);

        $import = new ElevesImport();
        Excel::import($import, $request->file('file'));

        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();

        if ($successCount === 0 && empty($errors)) {
            return back()->with('error', 'Aucune donnée valide trouvée dans le fichier.');
        }

        if (!empty($errors)) {
            return back()->with([
                'import_errors' => $errors,
                'import_success' => $successCount
            ]);
        }

        return back()->with('success', $successCount . ' élève(s) importé(s) avec succès');

    } catch (\Exception $e) {
        \Log::error('Import error', ['error' => $e->getMessage()]);
        return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
    }
}



}