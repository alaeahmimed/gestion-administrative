<?php

namespace App\Http\Controllers\Administrateur;

use App\Models\Justification;
use App\Models\JustificationAbsence;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Notifications reçues par l'admin connecté
        $notifications = Auth::user()->notifications()
            ->orderBy('notification_user.created_at', 'desc')
            ->paginate(10);
            
        return view('admin.notifications.index', compact('notifications'));
    }
    
    public function markAsRead($notificationId)
    {
        Auth::user()->notifications()
            ->where('notification_id', $notificationId)
            ->update(['vue' => true]);
            
        return back()->with('success', 'Notification marquée comme lue');
    }
    
    public function destroy($notificationId)
    {
        Auth::user()->notifications()->detach($notificationId);
        
        // Supprimer la notification si plus aucun utilisateur n'est lié
        if (Notification::find($notificationId)->users()->count() === 0) {
            Notification::destroy($notificationId);
        }
        
        return back()->with('success', 'Notification supprimée');
    }
    
    // Méthode pour envoyer une notification (à utiliser depuis d'autres contrôleurs)
    public static function sendJustificationNotification($parentId, $justificationId)
    {
        $admins = User::whereHas('administrateur')->get();
        $justification = Justification::findOrFail($justificationId);
    
        // Format spécial pour identifier clairement les justifications
        $notification = Notification::create([
            'sender_id' => $parentId,
            'type' => 'justification',
            'message' => json_encode([
                'type' => 'justification',
                'justification_id' => $justificationId,
                'text' => $justification->contenu,
            ]),
            'date' => now(),
        ]);
        
    

        foreach ($admins as $admin) {
            $admin->notifications()->attach($notification->id, ['vue' => false]);
        }
    
        return $notification;
    }



    public function show($id)
{
    $justification = JustificationAbsence::with('parentt.user', 'absence.eleve')->findOrFail($id);

    return view('admin.notifications.show', compact('justification'));
}


public function accepter($id)
{
    $justification = JustificationAbsence::findOrFail($id);
    $absence = $justification->absence;
    $absence->status = 'justifiee';
    $absence->save();

    return redirect()->route('admin.notifications.index', $id)->with('success', 'Justification acceptée');
}

public function refuser($id)
{
    $justification = JustificationAbsence::findOrFail($id);
    $absence = $justification->absence;
    $absence->status = 'non justifiee';
    $absence->save();

    return redirect()->route('admin.notifications.index', $id)->with('error', 'Justification refusée');
}



public function downloadJustification(JustificationAbsence $justification)
{
    // Vérification simple que l'utilisateur est admin
    if (!auth()->user()->isAdmin()) { // Adaptez à votre logique
        abort(403, 'Accès non autorisé');
    }

    // Correction du chemin - retirez 'justifications/' du nom de fichier
    $filename = basename($justification->fichier);
    $path = storage_path('app/public/justifications/' . $filename);

    if (!file_exists($path)) {
        abort(404, "Fichier introuvable: " . $path);
    }

    return response()->file($path); // Pour afficher dans le navigateur
}
}