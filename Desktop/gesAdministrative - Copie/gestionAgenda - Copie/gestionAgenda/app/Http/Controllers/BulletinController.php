<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulletinController extends Controller
{
   public function index(Request $request)
{
    $user = Auth::user();

    if (!$user->isAdmin() || !$user->administrateur) {
        return back()->with('error', 'Accès réservé aux administrateurs');
    }

    $classes = Eleve::select('classe')->distinct()->orderBy('classe')->pluck('classe');
    $selectedClass = $request->input('classe');

    // Modified query with join to users table
    $elevesQuery = Eleve::with(['bulletin' => function ($q) {
            $q->latest(); // Prendre le dernier bulletin (au cas où)
        }])
        ->join('users', 'users.id', '=', 'eleves.user_id')
        ->select('eleves.*') // Important pour éviter les colonnes ambiguës
        ->orderBy('users.nom')
        ->orderBy('users.prenom');

    if ($selectedClass) {
        $elevesQuery->where('classe', $selectedClass);
    }

    $eleves = $elevesQuery->paginate(10);

    return view('Admin.bulletins', [
        'classes' => $classes,
        'selectedClass' => $selectedClass,
        'eleves' => $eleves,
    ]);
}

public function store(Request $request)
    {
        // Validation avec contrôle d'unicité côté code
        $request->validate([
            'fichierPdf' => 'required|mimes:pdf|max:2048',
            'eleve_id' => [
                'required',
                'exists:eleves,id',
                function ($attribute, $value, $fail) {
                    if (Bulletin::where('eleve_id', $value)->exists()) {
                        $fail('Cet élève a déjà un bulletin.');
                    }
                },
            ],
        ]);

        $user = Auth::user();
        $administrateur = DB::table('administrateurs')
            ->where('user_id', $user->id)
            ->first();

        if (!$administrateur) {
            Log::warning('Tentative d\'accès non autorisée par user: ' . $user->id);
            return back()->with('error', 'Accès réservé aux administrateurs');
        }

        try {
            $file = $request->file('fichierPdf');
            $filename = 'bulletin_' . $request->eleve_id . '_' . time() . '.pdf';
            $path = $file->storeAs('bulletins', $filename, 'public');

            Bulletin::create([
                'fichierPdf' => $path,
                'eleve_id' => $request->eleve_id,
                'administrateur_id' => $administrateur->id,
            ]);

            Log::info('Bulletin importé avec succès pour élève: ' . $request->eleve_id);
            return redirect()->route('bulletins.index')
                ->with('success', 'Bulletin importé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur import bulletin - Elève: ' . $request->eleve_id . ' - Erreur: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erreur technique lors de l\'import. Veuillez réessayer.');
        }
    }
    /*
    // Option alternative : supprimer l'ancien bulletin automatiquement avant d'ajouter le nouveau
    public function store(Request $request)
    {
        $request->validate([
            'fichierPdf' => 'required|mimes:pdf|max:2048',
            'eleve_id' => 'required|exists:eleves,id',
        ]);

        $user = Auth::user();
        $administrateur = DB::table('administrateurs')
            ->where('user_id', $user->id)
            ->first();

        if (!$administrateur) {
            Log::warning('Tentative d\'accès non autorisée par user: ' . $user->id);
            return back()->with('error', 'Accès réservé aux administrateurs');
        }

        // Supprimer l'ancien bulletin si existe
        $existingBulletin = Bulletin::where('eleve_id', $request->eleve_id)->first();
        if ($existingBulletin) {
            if (Storage::disk('public')->exists($existingBulletin->fichierPdf)) {
                Storage::disk('public')->delete($existingBulletin->fichierPdf);
            }
            $existingBulletin->delete();
        }

        try {
            $file = $request->file('fichierPdf');
            $filename = 'bulletin_' . $request->eleve_id . '_' . time() . '.pdf';
            $path = $file->storeAs('bulletins', $filename, 'public');

            Bulletin::create([
                'fichierPdf' => $path,
                'eleve_id' => $request->eleve_id,
                'administrateur_id' => $administrateur->id,
            ]);

            Log::info('Bulletin importé avec succès pour élève: ' . $request->eleve_id);
            return redirect()->route('bulletins.index')
                ->with('success', 'Bulletin importé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur import bulletin - Elève: ' . $request->eleve_id . ' - Erreur: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erreur technique lors de l\'import. Veuillez réessayer.');
        }
    }
    */

   public function download(Bulletin $bulletin)
{
    // Vérification que le fichier existe dans le storage
    if (!Storage::disk('public')->exists($bulletin->fichierPdf)) {
        abort(404, "Fichier introuvable");
    }

    // Récupération du chemin complet
    $path = Storage::disk('public')->path($bulletin->fichierPdf);
    
    // Retourne le fichier pour affichage dans le navigateur
    return response()->file($path);
}

    public function destroy(Bulletin $bulletin)
    {
        try {
            DB::transaction(function () use ($bulletin) {
                // Supprimer le fichier s'il existe
                if ($bulletin->fichierPdf && Storage::disk('public')->exists($bulletin->fichierPdf)) {
                    Storage::disk('public')->delete($bulletin->fichierPdf);
                }

                // Supprimer l'enregistrement
                $bulletin->delete();
            });

            return redirect()->route('bulletins.index')
                ->with('success', 'Bulletin supprimé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur suppression bulletin: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression du bulletin');
        }
    }
}