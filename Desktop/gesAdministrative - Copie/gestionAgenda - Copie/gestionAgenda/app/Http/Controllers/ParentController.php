<?php
namespace App\Http\Controllers;
use App\Models\Emploi;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Notes;
use App\Models\Evenement;
use App\Models\Bulletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParentController extends Controller
{
    public function showDevoirs(Request $request)
    {
        $user = Auth::user();
        $eleves = $user->parentt->eleves;

        if (is_null($eleves) || $eleves->isEmpty()) {
            return redirect()->route('parent.devoirs')->with('error', 'Aucun enfant trouvé.');
        }

        if ($request->has('eleve_id')) {
            $eleveId = $request->input('eleve_id');
            return $this->showDevoirsForEleve($eleveId);
        }

        if ($eleves->count() == 1) {
            $devoirs = $eleves->first()->devoirs;
            return view('parent.devoirs', [
                'devoirs' => $devoirs,
                'eleves' => $eleves
            ]);
        }

        return view('parent.devoirs', [
            'eleves' => $eleves
        ]);
    }

    public function showDevoirsForEleve($eleveId)
    {
        $user = Auth::user();
        $eleves = $user->parentt->eleves;

        $eleve = Eleve::findOrFail($eleveId);
        $devoirs = $eleve->devoirs;

        return view('parent.devoirs', [
            'eleves' => $eleves,
            'eleve' => $eleve,
            'devoirs' => $devoirs,
        ]);
    }

    public function showDevoirDetail($eleveId, $devoirId)
{
    $user = Auth::user();
    $eleves = $user->parentt->eleves; // Toujours tous les enfants

    $eleve = Eleve::findOrFail($eleveId);

    // On récupère le devoir spécifique avec la note et commentaire du pivot
    $devoir = $eleve->devoirs()->where('devoir_id', $devoirId)->firstOrFail();

    // Récupérer la note et le commentaire depuis le pivot
    $note = $devoir->pivot->note ?? null;
    $commentaire = $devoir->pivot->commentaire ?? null;

    return view('parent.detail-devoir', [
        'devoir' => $devoir,
        'eleve' => $eleve,
        'eleves' => $eleves,
        'note' => $note,
        'commentaire' => $commentaire,
    ]);
}



    public function downloadFichier($filename)
    {
    // Correction du chemin - retirez 'justifications/' du nom de fichier
    $filename = basename($devoir->fichierJoint);
    $path = storage_path('app/public/devoirs/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->download($path); // Pour afficher dans le navigateur
}

    public function showBulletins(Request $request)
{
    $user = Auth::user();
    
    if (!$user->parentt) {
        return redirect()->route('login')
               ->with('error', 'Accès réservé aux parents.');
    }

    // On récupère les élèves (en tant qu'utilisateurs liés aux Eleves)
    $eleves = $user->parentt->eleves()->with('user')->get();

    if ($eleves->isEmpty()) {
        return view('parent.ex_bulletin')->with('info', 'Aucun enfant trouvé dans votre compte.');
    }

    $selectedEleve = null;
    $bulletins = collect();

    if ($request->has('eleve_id')) {
        $selectedEleve = $eleves->firstWhere('user.id', $request->eleve_id);

        if ($selectedEleve) {
            $bulletins = $selectedEleve->bulletin()->orderBy('created_at', 'desc')->get();
        }
    } elseif ($eleves->count() === 1) {
        $selectedEleve = $eleves->first();
        $bulletins = $selectedEleve->bulletin()->orderBy('created_at', 'desc')->get();
    }

    return view('parent.ex_bulletin', [
        'eleves' => $eleves,
        'selectedEleve' => $selectedEleve,
        'bulletins' => $bulletins
    ]);
}

public function voirBulletin(Bulletin $bulletin)
{
     if (!auth()->user()->isParent()) { // Adaptez à votre logique
        abort(403, 'Accès non autorisé');
    }

    // Correction du chemin - retirez 'justifications/' du nom de fichier
    $filename = basename($bulletin->fichierPdf);
    $path = storage_path('app/public/bulletins/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->file($path); // Pour afficher dans le navigateur
}

    
public function afficherEvenement(Request $request)
{
    $user = Auth::user();
    
    if (!$user->parentt) {
        return redirect()->route('login')
               ->with('error', 'Accès réservé aux parents.');
    }

    $eleves = $user->parentt->eleves()->with('user')->get();
    $selectedEleve = null;
    $evenements = collect();

    if ($request->has('eleve_id')) {
        $selectedEleve = $eleves->first(function($eleve) use ($request) {
            return $eleve->user && $eleve->user->id == $request->eleve_id;
        });

        if ($selectedEleve) {
            $evenements = $selectedEleve->evenements()->orderBy('dateDebut')->get();
        }
    } elseif ($eleves->count() === 1) {
        $selectedEleve = $eleves->first();
        $evenements = $selectedEleve->evenements()->orderBy('dateDebut')->get();
    }

    return view('parent.evenement', [
        'eleves' => $eleves,
        'selectedEleve' => $selectedEleve,
        'evenements' => $evenements
    ]);
}

   
    

public function voir(Evenement $evenement)
{
    // Autoriser soit les admins soit les parents associés à la classe
    $user = auth()->user();
    
    if (!$user->isParent()) {
        // Pour les parents, vérifier s'ils enseignent dans la classe concernée
        $parent = $user->parent;
        
        // Si l'événement n'a pas de classe ou si l'parent n'enseigne pas dans cette classe
        if (empty($evenement->classe) || 
            !in_array($evenement->classe, $parent->classes ?? [])) {
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


public function releve(Request $request)
{
        $user = Auth::user();
        $eleves = $user->parentt->eleves;

        if (is_null($eleves) || $eleves->isEmpty()) {
            return redirect()->route('parent.releve')->with('error', 'Aucun enfant trouvé.');
        }

        if ($request->has('eleve_id')) {
            $eleveId = $request->input('eleve_id');
            return $this->showReleveForEleve($eleveId);
        }

        if ($eleves->count() == 1) {
            $notes = $eleves->first()->notes;
            return view('parent.releve', [
                'notes' => $notes,
                'eleves' => $eleves
            ]);
        }

        return view('parent.releve', [
            'eleves' => $eleves
        ]);
    }
    public function showReleveForEleve($eleveId)
    {
        $user = Auth::user();
        $eleves = $user->parentt->eleves;

        $eleve = Eleve::findOrFail($eleveId);
        $notes = $eleve->notes;

        return view('parent.releve', [
            'eleves' => $eleves,
            'eleve' => $eleve,
            'notes' => $notes,
        ]);
    }

    public function emploi(Request $request)
{
    $user = Auth::user();
    
    if (!$user->parentt) {
        return redirect()->route('login')->with('error', 'Accès réservé aux parents.');
    }

    $eleves = $user->parentt->eleves()->with('user')->get();

    if ($eleves->isEmpty()) {
        return view('parent.ex_bulletin')->with('info', 'Aucun enfant trouvé.');
    }

    // Debug: Log les élèves trouvés
    \Log::info('Elèves trouvés:', $eleves->toArray());

    $selectedEleveId = $request->input('eleve_id', $eleves->first()->id);
    $selectedEleve = $eleves->firstWhere('id', $selectedEleveId);

    if (!$selectedEleve) {
        return redirect()->route('parent.dashboard')->with('error', 'Élève introuvable.');
    }

    // Debug: Log les infos de l'élève sélectionné
    \Log::info('Elève sélectionné:', [
        'id' => $selectedEleve->id,
        'cycle' => $selectedEleve->cycle,
        'classe' => $selectedEleve->classe
    ]);

    $emploi = Emploi::where('cycle', $selectedEleve->cycle)
                  ->where('classe', $selectedEleve->classe)
                  ->first();

    // Debug: Log l'emploi trouvé
    \Log::info('Emploi trouvé:', $emploi ? $emploi->toArray() : ['message' => 'Aucun emploi trouvé']);

    return view('parent.emploi', compact('eleves', 'selectedEleve', 'emploi'));
}

public function viewPdf($id)
{
    $emploi = Emploi::find($id);
    
    if (!$emploi) {
        return redirect()->back()->with('error', 'Emploi du temps introuvable.');
    }

    $possiblePaths = [
        storage_path('app/public/emplois/'.$emploi->file_path),
        storage_path('app/public/'.$emploi->file_path),
        public_path('storage/emplois/'.$emploi->file_path)
    ];

    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return response()->file($path);
        }
    }

    \Log::error("Fichier introuvable", [
        'id' => $id,
        'paths_tested' => $possiblePaths,
        'storage_files' => scandir(storage_path('app/public/emplois'))
    ]);

    return redirect()->back()
           ->with('error', 'Fichier PDF introuvable. Contactez l\'administration.');
}
}