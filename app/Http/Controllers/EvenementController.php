<?php 
namespace App\Http\Controllers; 
use App\Models\Evenement; 
use App\Models\Eleve; 
use App\Models\Administrateur; 
use App\Models\User;
use App\Models\Notification;
use App\Models\Parentt;
use Illuminate\Http\Request;  
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class EvenementController extends Controller {   
    
    public function index(Request $request) {  
        $selectedClass = $request->input('classe');       
        $classes = Eleve::select('classe')         
                        ->distinct()         
                        ->orderBy('classe')         
                        ->pluck('classe');      
        $query = Evenement::query()         
                        ->withCount('eleves')         
                        ->orderBy('dateDebut', 'desc');     
        if ($selectedClass) {         
            $query->whereHas('eleves', function($q) use ($selectedClass) {       
                $q->where('classe', $selectedClass);        
            });   
        }     
        $evenements = $query->paginate(10);    
        
        return view('Admin.liste-eve', compact('evenements', 'classes', 'selectedClass')); 
    }             
    
    public function download(Evenement $evenement)
{
     if (!auth()->user()->isAdmin()) { // Adaptez à votre logique
        abort(403, 'Accès non autorisé');
    }

    // Correction du chemin - retirez 'justifications/' du nom de fichier
    $filename = basename($evenement->image);
    $path = storage_path('app/public/evenements/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->file($path); // Pour afficher dans le navigateur
}

    public function create() {   
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
    
        return view('Admin.create-evenement',  [
        'cycles' => $cycles,
        'classesByCycle' => $classesByCycle
    ]); 
    } 
    
public function edit($id)
{
    $evenement = Evenement::findOrFail($id);

    // Récupérer les cycles (exemple)
   $cycles = Eleve::distinct()->pluck('cycle');

// Récupérer les classes par cycle
$classesByCycle = [];

foreach ($cycles as $cycle) {
    // On récupère les classes distinctes pour ce cycle dans eleves
    $classesByCycle[$cycle] = Eleve::where('cycle', $cycle)
                                  ->distinct()
                                  ->pluck('classe');
}

    return view('Admin.edit-evenement', compact('evenement', 'classesByCycle', 'cycles'));
}


public function store(Request $request)
{
    $validated = $request->validate([
        'description' => 'required|string|max:255',
        'dateDebut' => 'required|date',
        'dateFin' => 'required|date|after_or_equal:dateDebut',
        'heure' => 'required',
        'image' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,zip|max:2048',
        'classe' => 'required|array',
        'classe.*' => 'string'
    ]);

    try {
        // Récupération de l'admin
        $adminId = auth()->user()->administrateur->id ?? Administrateur::first()->id;
        if (!$adminId) {
            throw new \Exception("Aucun administrateur trouvé");
        }

        // Gestion du fichier (optionnel)
        $filePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->extension();
                $filePath = $file->storeAs('evenements', $fileName, 'public');
            }
        }

        // Création de l'événement
        $evenement = Evenement::create([
            'description' => $validated['description'],
            'dateDebut' => $validated['dateDebut'],
            'dateFin' => $validated['dateFin'],
            'heure' => $validated['heure'],
            'image' => $filePath,
            'classe' => json_encode($validated['classe']),
            'administrateur_id' => $adminId
        ]);

        // Association des élèves
        $elevesQuery = in_array('all', $validated['classe'])
            ? Eleve::query()
            : Eleve::whereIn('classe', $validated['classe']);
            
        $evenement->eleves()->attach($elevesQuery->pluck('id'));

        return redirect()->route('Admin.liste-eve')->with('success', 'Événement créé avec succès');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la création : '.$e->getMessage());
    }
}

public function update(Request $request, $id)
{
    $evenement = Evenement::findOrFail($id);

    $validated = $request->validate([
        'description' => 'required|string|max:255',
        'dateDebut' => 'required|date',
        'dateFin' => 'required|date|after_or_equal:dateDebut',
        'heure' => 'required',
        'image' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,zip|max:2048',
        'classe' => 'required|array',
        'classe.*' => 'string'
    ]);

    try {
        // Gestion du fichier
        if ($request->hasFile('image')) {
            // Suppression ancien fichier
            if ($evenement->image && Storage::disk('public')->exists($evenement->image)) {
                Storage::disk('public')->delete($evenement->image);
            }

            // Upload nouveau fichier
            $file = $request->file('image');
            if ($file->isValid()) {
                $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->extension();
                $evenement->image = $file->storeAs('evenements', $fileName, 'public');
            }
        }

        // Mise à jour des données
        $evenement->update([
            'description' => $validated['description'],
            'dateDebut' => $validated['dateDebut'],
            'dateFin' => $validated['dateFin'],
            'heure' => $validated['heure']
        ]);

        // Mise à jour des élèves
        $elevesQuery = in_array('all', $validated['classe'])
            ? Eleve::query()
            : Eleve::whereIn('classe', $validated['classe']);
            
        $evenement->eleves()->sync($elevesQuery->pluck('id'));

        return redirect()->route('Admin.liste-eve')->with('success', 'Événement mis à jour avec succès');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la mise à jour : '.$e->getMessage());
    }
}

    public function destroy($id) {     
        try {    
            $evenement = Evenement::findOrFail($id);  
            $evenement->delete();         
            
            return redirect()->route('Admin.liste-eve')                        
                            ->with('success', 'Événement supprimé avec succès');  
        } catch (\Exception $e) {   
            return redirect()->route('Admin.liste-eve')               
                            ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());  
        }
    }
    
    /**
     * Send notifications to teachers and parents about the event
     * 
     * @param Evenement $evenement The event
     * @param string $classe The selected class or 'all'
     * @param bool $isUpdate Whether this is an update notification
     */
 protected function sendEventNotifications(Evenement $evenement, string $classe, bool $isUpdate = false) {
    $message = $isUpdate 
        ? "Un événement a été mis à jour : {$evenement->description} (du {$evenement->dateDebut} au {$evenement->dateFin})"
        : "L'établissement a organisé un événement : {$evenement->description} du {$evenement->dateDebut} au {$evenement->dateFin} à {$evenement->heure}";

    // 1. Notifier TOUS les enseignants (sans filtre)
    $teacherUsers = User::where('role', 'enseignant')->get();

    // 2. Notifier les parents des élèves concernés (seulement si une classe est sélectionnée)
    $parentUsers = collect();

    if ($classe !== 'all') {
        $eleves = $evenement->eleves()->where('classe', $classe)->get();
        $parentUsers = User::where('role', 'parent')
            ->whereHas('parentt', function($q) use ($eleves) {
                $q->whereHas('eleves', function($q2) use ($eleves) {
                    $q2->whereIn('id', $eleves->pluck('id'));
                });
            })
            ->get();
    }

    // Combiner tous les destinataires (enseignants + parents concernés)
    $recipients = $teacherUsers->merge($parentUsers);

    // Envoyer les notifications (version corrigée sans chunk problématique)
    foreach ($recipients as $recipient) {
        $notification = Notification::create([
            'message' => $message,
            'date' => now(),
            'sender_id' => auth()->id(),
            'receiver_id' => $recipient->id,
            'type' => 'evenement',
            'data' => json_encode([
                'event_id' => $evenement->id,
                'is_update' => $isUpdate,
                'classe' => $classe !== 'all' ? $classe : 'Toutes classes'
            ])
        ]);

        DB::table('notification_user')->insert([
            'notification_id' => $notification->id,
            'user_id' => $recipient->id,
            'vue' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
}