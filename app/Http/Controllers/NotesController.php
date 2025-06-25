<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Notes;
use App\Models\Enseignant;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    public function index()
{
    // Récupération des cycles avec vérification
    $cycles = Eleve::select('cycle')
                 ->distinct()
                 ->pluck('cycle')
                 ->filter() // Enlève les valeurs vides
                 ->values(); // Réindexe le tableau

    if ($cycles->isEmpty()) {
        // Si aucun cycle trouvé, on en crée un fictif pour le debug
        $cycles = collect(['Cycle 1', 'Cycle 2']);
    }

    return view('admin.notes', compact('cycles'));
}

public function getClassesByCycle($cycle)
{
    // Ajout de logs pour debug
    \Log::info("Demande de classes pour le cycle: $cycle");
    
    $classes = Eleve::where('cycle', $cycle)
                  ->select('classe')
                  ->distinct()
                  ->pluck('classe')
                  ->toArray();

    \Log::info("Classes trouvées: " . json_encode($classes));
    
    return response()->json($classes);
}

public function getMatieresByClasse($classe)
{
    try {
        \Log::info("Recherche des matières pour la classe: $classe");

        // 1. Nettoyage du nom de classe
        $classe = trim($classe);
        
        // 2. Requête plus robuste
        $enseignants = Enseignant::where('classe', $classe)
            ->orWhere('classe', 'LIKE', "%$classe%")
            ->get();

        \Log::info("Nombre d'enseignants trouvés: " . $enseignants->count());

        // 3. Extraction des matières avec fallback
        $matieres = $enseignants->flatMap(function($enseignant) {
            \Log::debug("Matière de l'enseignant ID ".$enseignant->id.": ".$enseignant->matiere);
            
            if (empty($enseignant->matiere)) {
                return [];
            }

            // Si c'est déjà un tableau
            if (is_array($enseignant->matiere)) {
                return $enseignant->matiere;
            }

            // Si c'est du JSON
            $json = json_decode($enseignant->matiere, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }

            // Si c'est une chaîne séparée par des virgules
            return array_map('trim', explode(',', $enseignant->matiere));
        })
        ->filter() // Enlève les valeurs vides
        ->unique() // Supprime les doublons
        ->values(); // Réindexe le tableau

        // 4. Fallback explicite
        

        \Log::info("Matières retournées: ", $matieres->toArray());
        
        return response()->json($matieres);
        
    } catch (\Exception $e) {
        \Log::error("Erreur dans getMatieresByClasse: " . $e->getMessage());
        return response()->json(['Mathématiques', 'Français', 'Physique']);
    }
}


  
public function getElevesByClasse(Request $request, $classe)
{
    $matiere = $request->query('matiere');
    
    \Log::info("Récupération des élèves pour classe: $classe et matière: $matiere");
    
    // Récupération des élèves avec leurs utilisateurs associés
    $eleves = Eleve::with('user')
                ->where('classe', $classe)
                ->get();
    
    \Log::info("Nombre d'élèves trouvés: " . $eleves->count());
    
    // Pour le debug, vérifiez les données des élèves
    \Log::debug("Données des élèves:", $eleves->toArray());
    
    // Préparation des données de réponse
    $result = $eleves->map(function($eleve) use ($matiere) {
        $note = Notes::where('eleve_id', $eleve->id)
                  ->where('matiere', $matiere)
                  ->first();
        
        return [
            'id' => $eleve->id,
            'nom' => $eleve->user->nom ?? 'N/A',
            'prenom' => $eleve->user->prenom ?? 'N/A',
            'notes' => $note ? $note->only(['cc1', 'cc2', 'cc3', 'projet']) : null
        ];
    });
    
    \Log::info("Résultat final envoyé au front:", $result->toArray());
    
    return response()->json($result);
}

    public function saveNotes(Request $request)
{
    $user = auth()->user();
    
    if (!($user->isAdmin() || $user->isEnseignant())) {
        abort(403, 'Accès non autorisé');
    }

    // Décoder les notes si elles viennent en JSON
    $notes = is_string($request->notes) ? json_decode($request->notes, true) : $request->notes;

    $request->merge(['notes' => $notes]);

    $request->validate([
        'classe' => 'required|string',
        'matiere' => 'required|string',
        'notes' => 'required|array',
        'notes.*' => 'array',
        'notes.*.cc1' => 'nullable|numeric|min:0|max:20',
        'notes.*.cc2' => 'nullable|numeric|min:0|max:20',
        'notes.*.cc3' => 'nullable|numeric|min:0|max:20',
        'notes.*.projet' => 'nullable|numeric|min:0|max:20',
    ]);

    $user = auth()->user();
    
    // Vérification spécifique pour les enseignants
    if ($user->isEnseignant()) {
        $enseignant = $user->enseignant;
        
        // Vérifie que la matière correspond à celle de l'enseignant
        if ($enseignant->matiere !== $request->matiere) {
            return back()->with('error', 'Vous ne pouvez pas modifier cette matière');
        }

        // Vérifie que la classe est autorisée
        $classesEnseignant = json_decode($enseignant->classe, true) ?? [];
        if (!in_array($request->classe, $classesEnseignant)) {
            return back()->with('error', 'Accès non autorisé à cette classe');
        }
    }

    try {
        foreach ($request->notes as $eleveId => $noteData) {
            // Vérifie que l'élève appartient bien à la classe spécifiée (sécurité supplémentaire)
            $eleve = Eleve::findOrFail($eleveId);
            
            if ($eleve->classe !== $request->classe) {
                continue; // Skip si incohérence
            }

            Notes::updateOrCreate(
                [
                    'eleve_id' => $eleveId,
                    'matiere' => $request->matiere
                ],
                [
                    'cc1' => $noteData['cc1'] ?? null,
                    'cc2' => $noteData['cc2'] ?? null,
                    'cc3' => $noteData['cc3'] ?? null,
                    'projet' => $noteData['projet'] ?? null
                ]
            );
        }

        return back()->with('success', 'Notes enregistrées avec succès!');

    } catch (\Exception $e) {
        \Log::error("Erreur sauvegarde notes", [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);
        return back()->with('error', 'Erreur lors de la sauvegarde');
    }
}
  public function afficher()
{
    $enseignant = Enseignant::where('user_id', auth()->id())->first();

    if (!$enseignant) {
        abort(403, "Accès refusé");
    }

    // Décoder le JSON et convertir en collection
    $classes = collect(json_decode($enseignant->classe, true) ?? []);

    return view('enseignant.notes', compact('classes'));
}

public function getEleves(Request $request, $classe)
{
    $enseignant = auth()->user()->enseignant;
    
    if (!$enseignant) {
        return response()->json(['error' => 'Enseignant non trouvé'], 403);
    }

    // Vérification des classes autorisées (version optimisée)
    $classesEnseignant = is_array($enseignant->classe) 
        ? $enseignant->classe 
        : json_decode($enseignant->classe, true) ?? [$enseignant->classe];

    $classeTrouvee = collect($classesEnseignant)->contains(function ($classeAuth) use ($classe) {
        return strcasecmp(trim($classeAuth), trim($classe)) === 0;
    });

    if (!$classeTrouvee) {
        return response()->json(['error' => 'Accès non autorisé à cette classe'], 403);
    }

    try {
        // 1. Récupérer d'abord les élèves de la classe
        $eleves = Eleve::with('user')
            ->where('classe', $classe)
            ->get();

        // 2. Pour chaque élève, récupérer ses notes pour la matière de l'enseignant
        $result = $eleves->map(function($eleve) use ($enseignant) {
            $note = Notes::where('eleve_id', $eleve->id)
                      ->where('matiere', $enseignant->matiere)
                      ->first();

            return [
                'id' => $eleve->id,
                'nom' => $eleve->user->nom ?? 'Inconnu',
                'prenom' => $eleve->user->prenom ?? 'Inconnu',
                'notes' => $note ? $note->only(['cc1', 'cc2', 'cc3', 'projet']) : null
            ];
        });

        if ($result->isEmpty()) {
            \Log::info("Aucun élève avec notes trouvé", [
                'classe' => $classe,
                'matiere' => $enseignant->matiere
            ]);
        }

        return response()->json($result);

    } catch (\Exception $e) {
        \Log::error("Erreur getEleves", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Erreur serveur'], 500);
    }
}


}