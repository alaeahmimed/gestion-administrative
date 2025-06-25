<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Eleve;
use App\Models\Absence;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $enseignant = Auth::user()->enseignant;

    // classes attribuées à l'enseignant (tableau à partir du JSON)
    $classes = collect(json_decode($enseignant->classe ?? '[]'));

        $eleves = collect();
        $selectedClasse = $request->query('classe');
      
 $eleves = Eleve::where('classe', $selectedClasse)->with('user')->get();

        return view('enseignant.absences.index', compact('classes', 'eleves', 'selectedClasse'));
    }

    public function store(Request $request)
{
    $request->validate([
        'dateEnvoi' => 'required|date',
        'absences' => 'array'
    ]);

    $enseignantUser = Auth::user();

    foreach ($request->absences as $eleve_id => $status) {
        if ($status === 'absent') {
            $absence = Absence::create([
                'dateEnvoi' => $request->dateEnvoi,
                'status' => 'non justifiée',
                'eleve_id' => $eleve_id,
                'enseignant_id' => $enseignantUser->enseignant->id,
            ]);

            $eleve = Eleve::findOrFail($eleve_id);

         $message = "L'élève {$eleve->user->nom} {$eleve->user->prenom} est absent(e) le {$request->dateEnvoi}.";


            // Envoyer notification au parent
            if ($eleve->parentt && $eleve->parentt->user) {
                $parentUser = $eleve->parentt->user;

                $parentNotification = Notification::create([
                    'message' => $message,
                    'date' => now(),
                    'sender_id' => $enseignantUser->id,
                    'receiver_id' => $parentUser->id,
                ]);

                // Très important: associer directement
                $parentNotification->users()->attach($parentUser->id, ['vue' => false]);
            }

            // Envoyer notification aux administrateurs
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $adminNotification = Notification::create([
                    'message' => $message,
                    'date' => now(),
                    'sender_id' => $enseignantUser->id,
                    'receiver_id' => $admin->id,
                ]);

                // Très important: associer directement
                $adminNotification->users()->attach($admin->id, ['vue' => false]);
            }
        }
    }

    return redirect()->route('enseignant.absences.index')->with('success', 'Absences enregistrées et notifications envoyées.');
}


      
}
