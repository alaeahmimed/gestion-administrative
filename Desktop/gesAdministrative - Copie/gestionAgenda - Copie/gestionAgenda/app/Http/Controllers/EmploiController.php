<?php

namespace App\Http\Controllers;
use App\Models\Enseignant;
use App\Models\Emploi;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmploiController extends Controller
{
    /**
     * Affiche la liste des emplois du temps avec filtres
     */
  public function index(Request $request)
{
    // Récupération des cycles et classes
    $cycles = Eleve::select('cycle')->distinct()->orderBy('cycle')->pluck('cycle');
    // Récupération des classes filtrées si un cycle est sélectionné
    $classesQuery = Eleve::select('classe')->distinct()->orderBy('classe');
    
    if ($request->cycle) {
        $classesQuery->where('cycle', $request->cycle);
    }
    
    $classes = $classesQuery->pluck('classe');
    // Récupération des emplois
    $emplois = Emploi::query()
        ->when($request->cycle, fn($q) => $q->where('cycle', $request->cycle))
        ->when($request->classe, fn($q) => $q->where('classe', $request->classe))
        ->withCount('eleves')
        ->get();

    // Formatage des données pour la vue
    $groupedEmplois = [];
    foreach ($emplois as $emploi) {
        $groupedEmplois[$emploi->cycle][$emploi->classe] = [
            'id' => $emploi->id,
            'file_path' => $emploi->file_path,
            'eleves_count' => $emploi->eleves_count
        ];
    }

    // Récupération de toutes les classes pour afficher celles sans emploi
    $allClasses = Eleve::select('cycle', 'classe')
        ->distinct()
        ->when($request->cycle, fn($q) => $q->where('cycle', $request->cycle))
        ->when($request->classe, fn($q) => $q->where('classe', $request->classe))
        ->get()
        ->groupBy('cycle');

    return view('Admin.par-emplois.index', [
        'cycles' => $cycles,
        'classes' => $classes,
        'selectedCycle' => $request->cycle,
        'selectedClass' => $request->classe,
        'groupedClasses' => $groupedEmplois,
        'allClasses' => $allClasses,
        'hasEmplois' => count($groupedEmplois) > 0
    ]);
}
    /**
     * Stocke un nouvel emploi du temps
     */
/**
 * Stocke un nouvel emploi du temps
 */
public function store(Request $request)
{
    // Validation
    $validated = $request->validate([
        'cycle' => 'required|string',
        'classe' => 'required|string',
        'file_path' => 'required|file|mimes:pdf|max:2048'
    ]);

    try {
        // Suppression ancien fichier
        $existing = Emploi::where('cycle', $request->cycle)
                        ->where('classe', $request->classe)
                        ->first();
        
        if ($existing) {
            Storage::delete('public/'.$existing->file_path);
            $existing->delete();
        }

        // Stockage du fichier
       
        $file = $request->file('file_path');
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->move(storage_path('app/public/emplois'), $fileName);
        // Enregistrement en base
        Emploi::create([
            'cycle' => $request->cycle,
            'classe' => $request->classe,
            'file_path' => 'emplois/'.$fileName,
            'administrateur_id' => auth()->id()
        ]);

        return redirect()->route('par-emplois.index')
                       ->with('success', 'Fichier importé avec succès!');

    } catch (\Exception $e) {
        \Log::error("Erreur d'import : ".$e->getMessage());
        return back()->with('error', "Échec de l'import: ".$e->getMessage());
    }
}


public function download(Emploi $emploi)
{
     if (!auth()->user()->isAdmin()) { // Adaptez à votre logique
        abort(403, 'Accès non autorisé');
    }

    // Correction du chemin - retirez 'justifications/' du nom de fichier
    $filename = basename($emploi->file_path);
    $path = storage_path('app/public/emplois/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->file($path); // Pour afficher dans le navigateur
}
/** 
     * Supprime un emploi du temps
     */
    public function destroy($id)
    {
        $emploi = Emploi::findOrFail($id);

        // Supprimer le fichier
        Storage::delete($emploi->file_path);

        // Supprimer l'enregistrement
        $emploi->delete();

        return redirect()->route('par-emplois.index')
                         ->with('success', 'Emploi du temps supprimé avec succès!');
    }



    /**
     * Affiche la liste des emplois du temps des enseignants avec recherche
     */
  public function ajouter(Request $request)
{
    $request->validate([
        'enseignant_id' => 'required|exists:enseignants,id',
        'file_path' => 'required|file|mimes:pdf|max:2048'
    ]);

    try {
        $enseignant = Enseignant::with('user')->findOrFail($request->enseignant_id);

        // Chemin absolu du répertoire de stockage
        $storagePath = storage_path('app/public/emplois/enseignants');
        
        // Création du répertoire si inexistant
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Suppression de l'ancien fichier
        if ($enseignant->emploiTemps) {
            $oldFilePath = storage_path('app/public/'.$enseignant->emploiTemps->file_path);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
            $enseignant->emploiTemps()->delete();
        }

        // Génération du nom de fichier
        $fileName = 'emploi_'.$enseignant->id.'_'.time().'.pdf';
        
        // Stockage avec vérification
        $file = $request->file('file_path');
        $file->move($storagePath, $fileName);

        // Vérification que le fichier a bien été déplacé
        if (!file_exists($storagePath.'/'.$fileName)) {
            throw new \Exception("Échec du stockage du fichier");
        }

        // Enregistrement en base
        $emploi = $enseignant->emploiTemps()->create([
            'file_path' => 'emplois/enseignants/'.$fileName,
            'administrateur_id' => auth()->id(),
            'cycle' => null,
            'classe' => null
        ]);

        return redirect()->route('ens-emplois.index')
                       ->with('success', 'Emploi du temps importé avec succès');

    } catch (\Exception $e) {
        return back()->with('error', "Erreur: ".$e->getMessage());
    }
}
public function afficher(Request $request)
{
    $search = $request->input('search');
    
    $enseignants = Enseignant::with(['user', 'emploiTemps'])
        ->when($search, function($query) use ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nom', 'like', '%'.$search.'%')
                  ->orWhere('prenom', 'like', '%'.$search.'%');
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('Admin.ens-emplois.index', [
        'enseignants' => $enseignants,
        'search' => $search
    ]);
}
    /**
     * Supprime un emploi du temps d'enseignant
     */
    public function supprimer($id)
    {
        $emploi = Emploi::findOrFail($id);

        try {
            // Supprimer le fichier
            Storage::delete('public/'.$emploi->file_path);

            // Supprimer l'enregistrement
            $emploi->delete();

            return redirect()->route('ens-emplois.afficher')
                             ->with('success', 'Emploi du temps supprimé avec succès!');

        } catch (\Exception $e) {
            \Log::error("Erreur suppression emploi: ".$e->getMessage());
            return back()->with('error', 'Échec de la suppression: '.$e->getMessage());
        }
    }

public function voir(Emploi $emploi)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Accès non autorisé');
    }

    // Chemin complet du fichier
    $path = storage_path('app/public/' . $emploi->file_path);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->file($path);
}
   
}
