<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Devoir;
use App\Models\Emploi;
use App\Models\Evenement;
use App\Models\Notes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EleveController extends Controller
{
    public function dashboard()
{
    return view('eleve.dashboard'); // Ce fichier doit exister dans resources/views/eleve/dashboard.blade.php
}


    public function mesDevoirs()
{
    // Get the authenticated user
    $user = auth()->user();
    
    // Get the associated eleve record
    $eleve = Eleve::where('user_id', $user->id)->first();
    
    // Get devoirs for this eleve's class
    $devoirs = Devoir::where('classe', $eleve->classe)
            
                ->get();
    
    return view('eleve.devoir', [
        'eleve' => $eleve,
        'devoirs' => $devoirs
    ]);
}


public function download(Devoir $devoir)
{
    

    // Correction du chemin - retirez 'justifications/' du nom de fichier
    $filename = basename($devoir->fichierJoint);
    $path = storage_path('app/public/devoirs/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->download($path); // Pour afficher dans le navigateur
}


 public function afficherEvenement()
{
    $user = auth()->user();
    $eleve = Eleve::where('user_id', $user->id)->first();
    
    // Vérifiez d'abord si l'élève existe
    if (!$eleve) {
        return redirect()->back()->with('error', 'Élève non trouvé');
    }

    // Correction 1: Utilisez whereHas pour la relation avec les élèves
    $evenements = Evenement::whereHas('eleves', function($query) use ($eleve) {
            $query->where('classe', $eleve->classe);
        })
        ->orderBy('dateDebut', 'desc')
        ->get();
    
    // Debug: Vérifiez ce qui est récupéré
    // dd($evenements);

    return view('eleve.evenement', [
        'eleve' => $eleve,
        'evenements' => $evenements
    ]);
}


public function voir(Evenement $evenement)
{
    // Correction du chemin - retirez 'justifications/' du nom de fichier
    $filename = basename($evenement->image);
    $path = storage_path('app/public/evenements/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->file($path); // Pour afficher dans le navigateur
}

public function bulletin()
{
    // Récupérer l'élève connecté via la relation user
    $eleve = Eleve::where('user_id', auth()->id())->firstOrFail();

    // Récupérer le bulletin correspondant à l'élève
    $bulletin = DB::table('bulletins')
        ->where('eleve_id', $eleve->id)
        ->first();

    return view('eleve.ex_bulletin', [
        'bulletin' => $bulletin,
        'eleve' => $eleve
    ]);
}

public function viewBulletin()
{
    $eleve = Eleve::where('user_id', auth()->id())->firstOrFail();

    $bulletin = DB::table('bulletins')
        ->where('eleve_id', $eleve->id)
        ->first();

    if (!$bulletin) {
        abort(404, "Aucun bulletin trouvé pour cet élève");
    }

    // Utilisation du champ 'fichierPdf' comme défini dans la migration
    $path = storage_path('app/public/' . $bulletin->fichierPdf);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable");
    }

    return response()->file($path);
}



    public function mesNotes()
    {
    

    $eleve = Eleve::where('user_id', auth()->id())->firstOrFail();
        // Récupérer les notes de cet élève
        $notes = Notes::where('eleve_id', $eleve->id)->get();

        // Retourner la vue avec les données
        return view('eleve.releve', compact('eleve', 'notes'));
    }

 public function emploi()
    {
        // Récupérer l'élève connecté
        $eleve = Eleve::where('user_id', Auth::id())->firstOrFail();

        // Récupérer l'emploi du temps correspondant à son cycle et sa classe
        $emploi = DB::table('emplois')
            ->where('cycle', $eleve->cycle)
            ->where('classe', $eleve->classe)
            ->first();

        return view('eleve.emploi.index', [
            'emploi' => $emploi,
            'eleve' => $eleve
        ]);
    }

    public function viewEmploi()
    {
        $eleve = Eleve::where('user_id', Auth::id())->firstOrFail();

        $emploi = DB::table('emplois')
            ->where('cycle', $eleve->cycle)
            ->where('classe', $eleve->classe)
            ->first();

        if (!$emploi) {
            abort(404, "Aucun emploi du temps trouvé pour cette classe");
        }

        $path = storage_path('app/public/' . $emploi->file_path);

        if (!file_exists($path)) {
            abort(404, "Fichier introuvable");
        }

        return response()->file($path);
    }
}
