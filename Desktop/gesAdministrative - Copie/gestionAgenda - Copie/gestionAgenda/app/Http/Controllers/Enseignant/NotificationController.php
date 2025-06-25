<?php

namespace App\Http\Controllers\Enseignant;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
{
    $userId = Auth::id();

    // Charger uniquement les notifications reçues par l'utilisateur
    $notifications = Notification::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('sender_id', '!=', $userId) // 🔥 Exclure les notifications que l'enseignant a envoyées
        ->with('users')
        ->get();

    return view('enseignant.notifications.index', compact('notifications'));
}


    public function show(Notification $notification)
{
    $userId = Auth::id();

    // Vérifier si c'est bien sa notification
    abort_unless($notification->users->contains('id', $userId), 403);


    $from = User::find($notification->sender_id);

    $enfants = [];
    if ($from && $from->role === 'parent') {
        $enfants = $from->parentt?->eleves ?? [];
    }

    // Marquer comme vue
    DB::table('notification_user')
        ->where('notification_id', $notification->id)
        ->where('user_id', $userId)
        ->update(['vue' => true, 'updated_at' => now()]);

    return view('enseignant.notifications.show', compact('notification', 'from', 'enfants'));
}

public function respond(Request $request, Notification $notification)
{
    $request->validate([
        'reponse' => 'required|string'
    ]);

    // Créer une nouvelle notification pour le parent
    $newNotification = Notification::create([
        'message' => $request->reponse,
        'date' => now(),
        'sender_id' => Auth::id(),
        'receiver_id' => $notification->sender_id,
    ]);

    // Insérer l'entrée dans la table pivot notification_user
    DB::table('notification_user')->insert([
        'notification_id' => $newNotification->id,
        'user_id' => $notification->sender_id,
        'vue' => false, // La réponse n'est pas encore vue par le parent
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Si la notification vient d'un parent, on la supprime complètement
    if ($notification->sender->role === 'parent') {
        // Supprimer la notification de la table notifications
        $notification->delete();

        // Supprimer la relation de la table pivot notification_user
        DB::table('notification_user')
            ->where('notification_id', $notification->id)
            ->where('user_id', Auth::id())
            ->delete();
    }

    return redirect()->route('enseignant.notifications.index')
        ->with('success', 'Réponse envoyée.' . ($notification->sender->role === 'parent' ? ' et notification supprimée.' : ''));
}


public function destroy(Notification $notification)
{
    $userId = Auth::id();

    abort_unless($notification->users->contains('id', $userId), 403);

    // Supprimer la relation dans notification_user
    DB::table('notification_user')
        ->where('notification_id', $notification->id)
        ->where('user_id', $userId)
        ->delete();

    // Optionnellement, supprimer la notification si tu veux la retirer totalement
    $notification->delete();

    return redirect()->route('enseignant.notifications.index')->with('success', 'Notification supprimée.');
}

}
