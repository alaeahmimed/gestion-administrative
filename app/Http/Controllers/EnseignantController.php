<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Eleve;
use App\Models\Devoir;
use App\Models\Enseignant;
use App\Models\Emploi;
use App\Models\Evenement;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EnseignantController extends Controller
{
    public function index(Request $request){
        return view('enseignant.dashboard');
    }

    public function absence(Request $request){
        return view('enseignant.absences.index');
    }

public function devoir()
{
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    
    // Récupération propre des classes
    $classes = collect();
    try {
        $classes = collect(json_decode($enseignant->classe, true) ?? [])
                  ->unique()
                  ->sort()
                  ->values();
    } catch (\Exception $e) {
        // Fallback si le JSON est invalide
        $classes = collect([$enseignant->classe])->filter();
    }

    return view('enseignant.devoirs.devoir', [
        'classes' => $classes,
        'devoirs' => collect(),
        'classeSelectionnee' => null,
         'mode' => 'liste',
    ]);
}

public function afficherParClasse(Request $request)
{
    
    $request->validate(['classe' => 'required|string']);
    
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    $classeSelectionnee = $request->classe;

    // Récupération robuste des classes de l'enseignant
    $classesEnseignant = $this->getClassesEnseignant($enseignant);

    if (!$classesEnseignant->contains($classeSelectionnee)) {
        abort(403, "Accès refusé : Vous n'êtes pas autorisé à accéder à la classe $classeSelectionnee");
    }

   $devoirs = Devoir::forEnseignant($enseignant->id)
           ->forClasse($classeSelectionnee)
           ->with('eleves')
           ->paginate(10);


    return view('enseignant.devoirs.devoir', [
        'classes' => $classesEnseignant->sort()->values(),
        'devoirs' => $devoirs,
        'classeSelectionnee' => $classeSelectionnee,
         'mode' => 'liste',
    ]);
}

// Nouvelle méthode helper pour gérer la récupération des classes
protected function getClassesEnseignant(Enseignant $enseignant)
{
    try {
        // Essayer de décoder comme JSON
        $classes = json_decode($enseignant->classe, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return collect($classes)->filter()->unique();
        }
        
        // Si ce n'est pas un JSON valide, traiter comme une chaîne simple
        return collect([$enseignant->classe])->filter();
    } catch (\Exception $e) {
        return collect();
    }
}

   public function create()
{
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    $classesEnseignant = $this->getClassesEnseignant($enseignant);
    
    if ($classesEnseignant->isEmpty()) {
        return redirect()->back()->with('error', 'Aucune classe assignée à votre compte.');
    }

    return view('enseignant.devoirs.partiels.create', [
        'classes' => $classesEnseignant->sort()->values()
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'nullable|string',
        'dateLimite' => 'required|date',
        'fichierJoint' => 'nullable|file|mimes:pdf,docx,zip',
        'classe' => 'required|string',
    ]);

    // Vérifier que la classe sélectionnée fait partie des classes enseignées
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    $classesEnseignant = $this->getClassesEnseignant($enseignant);

    if (!$classesEnseignant->contains($request->classe)) {
        abort(403, "Accès refusé : Vous n'êtes pas autorisé à créer un devoir pour cette classe.");
    }

    $filePath = null;
    if ($request->hasFile('fichierJoint')) {
        $file = $request->file('fichierJoint');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('devoirs', $fileName, 'public');
    }

    $devoir = Devoir::create([
        'titre' => $request->titre,
        'description' => $request->description,
        'dateLimite' => $request->dateLimite,
        'fichierJoint' => $filePath,
        'enseignant_id' => $enseignant->id,
        'classe' => $request->classe, // Correction: 'string' remplacé par 'classe'
    ]);

    $eleves = Eleve::where('classe', $request->classe)->pluck('id');
    $devoir->eleves()->attach($eleves);

    return redirect()->route('devoirs.parClasse', ['classe' => $request->classe])
             ->with('success', 'Devoir ajouté avec succès.');
}public function edit($id)
{
    $devoir = Devoir::findOrFail($id);
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    
    // Vérification que l'enseignant est bien l'auteur du devoir
    if ($devoir->enseignant_id !== $enseignant->id) {
        abort(403, "Vous n'êtes pas autorisé à modifier ce devoir");
    }

    $classe = request('classe');
    $classesEnseignant = $this->getClassesEnseignant($enseignant);

    return view('enseignant.devoirs.partiels.update', [
        'devoir' => $devoir,
        'classe' => $classe,
        'classes' => $classesEnseignant // Pour afficher les classes disponibles
    ]);
}

public function update(Request $request, $id)
{
    $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'nullable|string',
        'dateLimite' => 'required|date|after_or_equal:today',
        'fichierJoint' => 'nullable|file|mimes:pdf,docx,zip|max:2048',
        'classe' => 'required|string'
    ]);

    $devoir = Devoir::findOrFail($id);
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();

    // Vérification d'accès
    if ($devoir->enseignant_id !== $enseignant->id) {
        abort(403, "Action non autorisée");
    }

    // Vérification que la classe fait partie des classes enseignées
    $classesEnseignant = $this->getClassesEnseignant($enseignant);
    if (!$classesEnseignant->contains($request->classe)) {
        abort(403, "Vous n'enseignez pas dans cette classe");
    }

    // Gestion du fichier
    if ($request->hasFile('fichierJoint')) {
        // Suppression de l'ancien fichier
        if ($devoir->fichierJoint) {
            Storage::disk('public')->delete($devoir->fichierJoint);
        }
        
        // Enregistrement du nouveau fichier
        $file = $request->file('fichierJoint');
        $fileName = time() . '_' . $file->getClientOriginalName(); // Ajout d'un timestamp pour éviter les conflits
        $devoir->fichierJoint = $file->storeAs('devoirs', $fileName, 'public');
    }

    // Mise à jour des données
    $devoir->update([
        'titre' => $request->titre,
        'description' => $request->description,
        'dateLimite' => $request->dateLimite,
        'classe' => $request->classe
    ]);

    return redirect()
           ->route('devoirs.parClasse', ['classe' => $request->classe])
           ->with('success', 'Devoir modifié avec succès.');
}

    public function destroy($id)
    {
        $devoir = Devoir::findOrFail($id);

        if ($devoir->fichierJoint) {
            Storage::disk('public')->delete($devoir->fichierJoint);
        }

        $devoir->delete();

        return redirect()->back()->with('success', 'Devoir supprimé avec succès.');
    }


public function download(Devoir $devoir)
{
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    
    // Vérifier que l'enseignant a accès à ce devoir
    if ($devoir->enseignant_id !== $enseignant->id) {
        abort(403, 'Accès non autorisé à ce devoir');
    }

    // Correction du chemin - retire 'devoirs/' du nom de fichier
    $filename = basename($devoir->fichierJoint);
    $path = storage_path('app/public/devoirs/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable");
    }

    // Pour afficher le fichier plutôt que l'afficher
  return response()->file($path); 
}
  
//evenement
public function evenement(Request $request)
{
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    $classesEnseignant = $this->getClassesEnseignant($enseignant);
    
    $classeSelectionnee = $request->classe;
    
    if (!$classeSelectionnee && $classesEnseignant->isNotEmpty()) {
        $classeSelectionnee = $classesEnseignant->first();
    }
    
    $evenements = collect();
    
    if ($classeSelectionnee) {
        // Vérification plus permissive des classes
        if (!in_array($classeSelectionnee, $classesEnseignant->toArray())) {
            abort(403, "Accès refusé : Vous n'êtes pas autorisé à accéder à cette classe");
        }
        
        $evenements = Evenement::whereHas('eleves', function($query) use ($classeSelectionnee) {
                $query->where('classe', $classeSelectionnee);
            })
            ->with('eleves')
            ->orderBy('dateDebut', 'desc')
            ->paginate(10);
    }
    
    return view('enseignant.evenements.index', [
        'classes' => $classesEnseignant->sort()->values(),
        'evenements' => $evenements,
        'classeSelectionnee' => $classeSelectionnee
    ]);
}

public function voir(Evenement $evenement)
{
    // Autoriser soit les admins soit les enseignants associés à la classe
    $user = auth()->user();
    
    if (!$user->isEnseignant()) {
        // Pour les enseignants, vérifier s'ils enseignent dans la classe concernée
        $enseignant = $user->enseignant;
        
        // Si l'événement n'a pas de classe ou si l'enseignant n'enseigne pas dans cette classe
        if (empty($evenement->classe) || 
            !in_array($evenement->classe, $enseignant->classes ?? [])) {
            abort(403, 'Accès non autorisé');
        }
    }

    $filename = basename($evenement->image);
    $path = storage_path('app/public/evenements/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable");
    }

    return response()->file($path);
}
    public function evaluerDevoirs(Request $request)
{
    $classe = $request->input('classe');
    $devoir_id = $request->input('devoir_id');

    $classes = Eleve::select('classe')->distinct()->pluck('classe');
    $devoirs = Devoir::all(); // ou filtrer par enseignant connecté

    $eleves = collect();
    $notes = [];

    if ($classe && $devoir_id) {
        // Récupérer les élèves de la classe sélectionnée
        $eleves = Eleve::where('classe', $classe)->get();

        foreach ($eleves as $eleve) {
            $pivot = $eleve->devoirs()->where('devoir_id', $devoir_id)->first()?->pivot;
            $notes[$eleve->id] = [
                'note' => $pivot?->note,
                'commentaire' => $pivot?->commentaire,
            ];
        }
    }

    return view('enseignant.devoirs.evaluer_devoir', compact('classes', 'devoirs', 'eleves', 'notes'));
}



 public function emploi()
{
    // Récupérer l'enseignant connecté
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    
    // Récupérer l'emploi du temps spécifique à cet enseignant
    $emploi = DB::table('emplois')
        ->where('emploisable_type', 'App\\Models\\Enseignant')
        ->where('emploisable_id', $enseignant->id)
        ->first();

    return view('enseignant.emploi.index', [
        'emploi' => $emploi,
        'enseignant' => $enseignant
    ]);
}
public function viewEmploi()
{
    $enseignant = Enseignant::where('user_id', Auth::id())->firstOrFail();
    
    $emploi = DB::table('emplois')
        ->where('emploisable_type', 'App\\Models\\Enseignant')
        ->where('emploisable_id', $enseignant->id)
        ->first();

    if (!$emploi) {
        abort(404, "Aucun emploi du temps trouvé pour cet enseignant");
    }

    $path = storage_path('app/public/' . $emploi->file_path);
    
    if (!file_exists($path)) {
        abort(404, "Fichier introuvable");
    }
    
    return response()->file($path);
}
    

}
