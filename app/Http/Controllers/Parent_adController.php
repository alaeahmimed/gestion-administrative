<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Parentt;
use App\Models\Eleve;
use App\Models\User;
use App\Imports\ParentsImport;
use Maatwebsite\Excel\Facades\Excel;

class Parent_adController extends Controller
{
  public function index(Request $request)
{
    $search = $request->input('search');
    
    // Initialisez la requête pour les parents avec leurs relations
    $parents = Parentt::with(['user', 'eleves.user']);
    
    // Appliquez la recherche si elle existe
    if ($search) {
        $parents->where(function ($query) use ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            })->orWhere('cin', 'like', "%{$search}%")
              ->orWhereHas('eleves.user', function($q) use ($search) {
                  $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%");
              });
        });
    }
    
    // Paginez les résultats
    $parents = $parents->paginate(10);
  

    return view('Admin.listerParent', compact('parents','search'));
}

    public function create()
    {
        return view('Admin.addParent');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'login' => 'required|string|email|unique:users,login', // Unique email
            'motDePasse' => 'required|string|min:6',
            'cin' => 'required|string',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'], // Using email as login
            'motDePasse' => bcrypt($validated['motDePasse']),
            'role' => 'parent',
            'login' => $validated['login'], // Adding email to the user
        ]);

        // Créer l'enseignant lié
        Parentt::create([
            'user_id' => $user->id,
            'cin' => $validated['cin']
        ]);

        return redirect()->route('listerParent.index')
                         ->with('success', 'Parent ajouté avec succès.');
    }

   
    public function edit($id)
{
    $parent = Parentt::with('user')->findOrFail($id);
    return view('Admin.editParent', compact('parent'));
}

    public function update(Request $request, $id)
    {
        // Récupérer l'enseignant et son utilisateur
        $parent = Parentt::with('user')->findOrFail($id);
        $user = $parent->user;
    
        // Valider les données (ignorer l'ID du user pour l'unicité de l'email)
        $validated = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'login' => 'required|string|email|unique:users,login,' . $user->id,
            'motDePasse' => 'nullable|string|min:6',
            'cin' => 'required|string',
        ]);
    
        // Mettre à jour l'utilisateur
        $user->nom = $validated['nom'];
        $user->prenom = $validated['prenom'];
        $user->login = $validated['login'];
        if (!empty($validated['motDePasse'])) {
            $user->motDePasse = bcrypt($validated['motDePasse']);
        }
        $user->save();
    
        // Mettre à jour l'enseignant
        $parent->cin = $validated['cin'];
        $parent->save();
    
        return redirect()->route('listerParent.index')
                         ->with('success', 'Parent modifié avec succès.');
    }
    


public function destroy(Parentt $parent)
{
    try {
        DB::transaction(function () use ($parent) {
            // Delete the associated user
            $parent->user()->delete();
            
            // Then delete the teacher record
            $parent->delete();
        });

        return redirect()->route('listerParent.index')
                       ->with('success', 'Parent supprimé avec succès.');
    } catch (\Exception $e) {
        return redirect()->route('listerParent.index')
                       ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
    }
}

public function import(Request $request) 
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048'
    ]);

    $import = new ParentsImport();
    Excel::import($import, $request->file('file'));

    $errors = $import->getErrors();
    $successCount = $import->getSuccessCount();

    if (!empty($errors)) {
        return back()
            ->with('import_errors', $errors)
            ->with('success', $successCount > 0 ? "{$successCount} parents importés avec succès" : null);
    }

    return back()->with('success', "{$successCount} parents importés avec succès");
}

}