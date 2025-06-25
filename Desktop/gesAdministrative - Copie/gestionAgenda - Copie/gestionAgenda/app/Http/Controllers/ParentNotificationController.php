<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absence;
use App\Models\Parentt;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JustificationAbsence;
use Illuminate\Support\Facades\Auth;

class ParentNotificationController extends Controller
{
    public function notifications()
{
    $userId = Auth::id();
    $notifications = Notification::with('sender') // Charger les données associées
        ->join('notification_user', 'notifications.id', '=', 'notification_user.notification_id')
        ->where('notification_user.user_id', $userId)
        ->where('notifications.receiver_id', $userId)
        ->select('notifications.*', 'notification_user.vue')
        ->orderBy('notification_user.created_at', 'desc')
        ->get();

    return view('parent.notifications', compact('notifications'));
}



    public function markAsRead($notificationId)
    {
        $userId = Auth::id();

        // Mise à jour dans la table pivot
        DB::table('notification_user')
            ->where('user_id', $userId)
            ->where('notification_id', $notificationId)
            ->update(['vue' => true]);

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    public function messages()
{
    // Récupérer le message de succès de la session
    $successMessage = session('success');

    return view('parent.messages', compact('successMessage'));
}



    public function askTeacher(Request $request)
{
    $request->validate([
        'teacher_search' => 'required|string',
        'message' => 'required|string',
    ]);

    $teacherUser = User::where('role', 'enseignant')
        ->where(function($query) use ($request) {
            $query->where('nom', 'like', '%' . $request->teacher_search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->teacher_search . '%');
        })
        ->first();

    if (!$teacherUser) {
        return back()->withErrors(['teacher_search' => 'Enseignant introuvable.']);
    }

    // Créer la notification
    $notification = Notification::create([
        'message' => $request->message,
        'date' => now(),
        'sender_id' => Auth::id(),
        'receiver_id' => $teacherUser->id, // OK car dans table notifications il y a receiver_id
    ]);

    // Associer dans table PIVOT (notification_user)
    $teacherUser->notifications()->attach($notification->id);

    return redirect()->route('parent.messages')->with('success', 'Votre question a été envoyée à l\'enseignant.');

}


public function justifyAdmin(Request $request) 
{
    $request->validate([
        'absence_id' => 'required|exists:absences,id',
        'raison' => 'required|string',
        'fichier' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    // Obtenir le parent connecté
    $parent = Parentt::where('user_id', Auth::id())->with('user')->firstOrFail();
    
    if (!$parent->user) {
        return back()->withErrors(['parent' => "L'utilisateur parent associé est introuvable."]);
    }

    $adminUsers = User::where('role', 'admin')->get();

    if ($adminUsers->isEmpty()) {
        return back()->with('error', 'Aucun administrateur trouvé');
    }

    // Stockage du fichier s'il existe
    $fichierPath = $request->hasFile('fichier') 
        ? $request->file('fichier')->store('justifications', 'public') 
        : null;

    // Création de la justification
    $justification = JustificationAbsence::create([
        'raison' => $request->raison,
        'fichier' => $fichierPath,
        'absence_id' => $request->absence_id,
        'parentt_id' => $parent->id,
        'statut' => 'en attente',
    ]);

    // Récupération de l'absence avec l'élève
    $absence = Absence::with('eleve')->findOrFail($request->absence_id);
    
    if (!$absence->eleve) {
        return back()->withErrors(['absence' => "L'élève associé à cette absence est introuvable."]);
    }

    $absence->update(['statut' => 'en attente']);

    // Préparation du contenu de la notification
    $notificationData = [
        'type' => 'justification',
        'justification_id' => $justification->id,
        'absence_id' => $request->absence_id,
        'parent_name' => $parent->user->nom ?? 'Parent inconnu',
        'message' => "Justification d'absence pour " . ($absence->eleve->nom ?? 'Élève inconnu') . " - {$request->raison}",
        'has_actions' => true
    ];

    // Envoi de la notification à tous les administrateurs
    foreach ($adminUsers as $admin) {
        $notification = Notification::create([
            'message' => json_encode($notificationData),
            'date' => now(),
            'sender_id' => Auth::id(),
            'receiver_id' => $admin->id,
            'type' => 'justification', // Important !
        ]);

        $admin->notifications()->attach($notification->id, ['vue' => false]);
    }

    return redirect()->route('parent.messages')
         ->with('success', 'Justification envoyée à l\'administration.');
}

}