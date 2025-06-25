<?php

use App\Models\Parentt;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsEleve;
use App\Http\Middleware\IsParent;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\IsEnseignant;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\Parent_adController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\Eleve_adController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\Administrateur\NotifController;
use App\Http\Controllers\Enseignant_adController;
use App\Http\Controllers\ParentNotificationController;
use App\Http\Controllers\Enseignant\NotificationController;
use App\Http\Controllers\EmploiEvenementController;
use App\Http\Controllers\EmploiController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\BulletinController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/enseignant/absences',[AbsenceController::class,'index'])->name('enseignant.absences.index');

Route::post('/enseignant/absences', [AbsenceController::class,'store'])->name('enseignant.absences.store');
Route::get('/enseignant', [EnseignantController::class, 'index'])->name('enseignant.dashboard');

Route::get('/enseignant/notifications', [NotificationController::class, 'index'])->name('enseignant.notifications.index');
Route::get('/enseignant/notifications/{notification}', [NotificationController::class, 'show'])->name('enseignant.notifications.show');
Route::post('/enseignant/notifications/{notification}/respond', [NotificationController::class, 'respond'])->name('enseignant.notifications.respond');
Route::delete('/enseignant/notifications/{notification}', [NotificationController::class, 'destroy'])->name('enseignant.notifications.destroy');

 Route::prefix('enseignant')->middleware(['auth', IsEnseignant::class])->group(function () {
    Route::get('/evenements', [EnseignantController::class, 'evenement'])
           ->name('enseignant.evenements.index');
           
    Route::get('/evenements/{evenement}/voir', [EnseignantController::class, 'voir'])
           ->name('enseignant.evenements.voir');
});
Route::get('/enseignant/devoirs', [EnseignantController::class, 'devoir'])->name('enseignant.devoirs.devoir');
Route::get('/devoirs/parClasse', [EnseignantController::class, 'afficherParClasse'])->name('devoirs.parClasse');
// Ajouter devoir
Route::get('/enseignant/devoirs/create', [EnseignantController::class, 'create'])->name('enseignant.devoirs.create');
Route::post('/enseignant/devoirs', [EnseignantController::class, 'store'])->name('enseignant.devoirs.store');
Route::get('/enseignant/devoirs/{id}/edit', [EnseignantController::class, 'edit'])->name('enseignant.devoirs.edit');
Route::put('/enseignant/devoirs/{id}', [EnseignantController::class, 'update'])->name('enseignant.devoirs.update');

// Supprimer un devoir
Route::delete('/enseignant/devoirs/{id}/destroy', [EnseignantController::class, 'destroy'])->name('devoirs.destroy');
Route::get('/devoirs/{devoir}/download', [EnseignantController::class, 'download'])
     ->name('devoirs.download');
Route::get('/enseignant/emploi', [EnseignantController::class, 'emploi'])->name('enseignant.emploi.index');
 Route::get('/emploi/view', [EnseignantController::class, 'viewEmploi'])->name('enseignant.emploi.view');







Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/admin/dashboard', function () {
    return view('Admin.dashboard');
})->middleware(['auth', IsAdmin::class])->name('admin.dashboard');


Route::get('/enseignant/dashboard', function () {
    return view('enseignant.dashboard');
})->middleware(['auth', IsEnseignant::class])->name('enseignant.dashboard');


Route::get('/parent/dashboard', function () {
    return view('parent.dashboard');
})->middleware(['auth', IsParent::class])->name('parent.dashboard');

Route::get('/eleve/dashboard', function () {
    return view('eleve.dashboard');
})->middleware(['auth', IsEleve::class])->name('eleve.dashboard');


Route::get('/parent/devoirs', [ParentController::class, 'showDevoirs'])->name('parent.devoirs');
Route::get('/parent/devoirs/eleve/{eleveId}', [ParentController::class, 'showDevoirsForEleve'])->name('parent.devoirs.eleve');
Route::get('/parent/devoirs/fichier/{filename}', [ParentController::class, 'downloadFichier'])->name('fichier.download');
Route::get('/parent/devoirs/{eleveId}/detail/{devoirId}', [ParentController::class, 'showDevoirDetail'])->name('parent.devoirs.detail');
Route::get('/parent/emploi', [ParentController::class, 'emploi'])->name('parent.emploi');
Route::get('/parent/evenement', [ParentController::class, 'afficherEvenement'])->name('parent.evenement');
Route::get('/parent/releve', [ParentController::class, 'releve'])->name('parent.releve');
Route::get('/parent/relev/eleve/{eleveId}', [ParentController::class, 'showReleveForEleve'])->name('parent.releve.eleve');
Route::get('/parent/emploi', [ParentController::class, 'emploi'])->name('parent.emploi');
Route::get('/parent/emploi/view/{id}', [ParentController::class, 'viewPdf'])->name('parent.emploi.view');
Route::get('/parent/notifications', [ParentNotificationController::class, 'notifications'])->name('parent.notifications');
Route::patch('/parent/notifications/{notification}/read', [ParentNotificationController::class, 'markAsRead'])->name('parent.notifications.markAsRead');
Route::delete('/notifications/{id}', [ParentNotificationController::class, 'destroy'])->name('parent.notifications.delete');

// Affichage de la page des deux boutons
Route::get('/parent/messages', [ParentNotificationController::class, 'messages'])->name('parent.messages');

// Formulaires
Route::get('/parent/messages/ask', function () {
    return view('parent.ask_teacher');
})->name('parent.askTeacherForm');

Route::get('/parent/messages/justify', function () {
    $parent = Parentt::where('user_id', Auth::id())->first();

    $absences = collect(); // collection vide par défaut

    if ($parent) {
        foreach ($parent->eleves as $eleve) {
            if (method_exists($eleve, 'absences')) {
                // Récupère toutes les absences de l'élève
                $absences = $absences->merge($eleve->absences);
            }
        }
    }

    return view('parent.justify_admin', compact('absences'));
})->name('parent.justifyAdminForm');



// Traitements
Route::post('/parent/messages/ask', [ParentNotificationController::class, 'askTeacher'])->name('parent.askTeacher');
Route::post('/parent/messages/justify', [ParentNotificationController::class, 'justifyAdmin'])->name('parent.justifyAdmin');


//Route :User Eleve
Route::get('/eleve/devoirs', [EleveController::class, 'mesDevoirs'])->name('eleve.devoir');
Route::get('/eleve/evenements', [EleveController::class, 'afficherEvenement'])->name('eleve.evenement');
Route::get('/eleve/devoirs/{devoir}/download', [EleveController::class, 'download'])
     ->name('devoir.download');
Route::get('/evenement/{evenement}/voir', [EleveController::class, 'voir'])
           ->name('eleve.evenement.voir');
Route::get('/eleve/notes', [EleveController::class, 'mesNotes'])->name('eleve.releve');
Route::get('/bulletin', [EleveController::class, 'bulletin'])->name('eleve.ex_bulletin');
Route::get('/bulletin/view', [EleveController::class, 'viewBulletin'])->name('eleve.ex_bulletin.view');

Route::get('/eleve/emploi', [EleveController::class, 'emploi'])->name('eleve.emploi.index');
Route::get('/eleve/emploi/view', [EleveController::class, 'viewEmploi'])->name('eleve.emploi.view');




Route::get('/admin/dashboard', function () {
    return view('Admin.dashboard', [
        'totalEleves' => Eleve::count(),
        'totalEnseignants' => Enseignant::count(),
        'totalParents' => Parentt::count(),
        // Pas besoin d'envoyer le nom, il est déjà disponible via Auth
    ]);
})->middleware(['auth', IsAdmin::class])->name('admin.dashboard');

Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    // Enseignant Routes
    Route::get('/enseignants', [Enseignant_adController::class, 'index'])->name('listerEnseignant.index');
    Route::get('/enseignants/create', [Enseignant_adController::class, 'create'])->name('addEnseignant.create');
    Route::post('/enseignants', [Enseignant_adController::class, 'store'])->name('addEnseignant.store');
    Route::get('/enseignants/{enseignant}/edit', [Enseignant_adController::class, 'edit'])->name('editEnseignant.edit');
    Route::put('/enseignants/{enseignant}/update', [Enseignant_adController::class, 'update'])->name('editEnseignant.update');
    Route::delete('/enseignants/{enseignant}', [Enseignant_adController::class, 'destroy'])
         ->name('deleteEnseignant.destroy');
 });
Route::post('/admin/enseignants/import', [Enseignant_adController::class, 'import'])
    ->name('enseignants.import')
    ->middleware(['auth']);

    // Parent Routes
    Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/parents', [Parent_adController::class, 'index'])->name('listerParent.index');
    Route::get('/parents/create', [Parent_adController::class, 'create'])->name('addParent.create');
    Route::post('/parents', [Parent_adController::class, 'store'])->name('addParent.store');
    Route::get('/parents/{parent}/edit', [Parent_adController::class, 'edit'])->name('editParent.edit');
    Route::put('/parents/{parent}/update', [Parent_adController::class, 'update'])->name('editParent.update');
    Route::delete('/parents/{parent}', [Parent_adController::class, 'destroy'])->name('deleteParent.destroy');
});

Route::post('/admin/parents/import', [Parent_adController::class, 'import'])
    ->name('parents.import')
    ->middleware(['auth']);

Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
   Route::get('/eleves', [Eleve_adController::class, 'index'])->name('listerEleves.index');
   Route::get('/eleves/create', [Eleve_adController::class, 'create'])->name('addEleve.create');
   Route::post('/eleves', [Eleve_adController::class, 'store'])->name('addEleve.store');
   Route::get('/search-parents', [Eleve_adController::class, 'searchParents'])->name('search.parents');
   Route::get('/eleves/{eleve}/edit', [Eleve_adController::class, 'edit'])->name('editEleve.edit');
    Route::put('/eleves/{eleve}', [Eleve_adController::class, 'update'])->name('editEleve.update');

   Route::delete('/eleves/{eleve}', [Eleve_adController::class, 'destroy'])->name('deleteEleve.destroy');
});
Route::post('/admin/eleves/import', [Eleve_adController::class, 'import'])
    ->name('eleves.import')
    ->middleware(['auth']);



//Evenement/emploi
Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
Route::get('/emploi-evenement', [EmploiEvenementController::class, 'index'])->name('emploi-evenement.index');

//evenement
Route::get('/get-classes-by-cycle', [EvenementController::class, 'getClassesByCycle'])->name('get.classes.by.cycle');
Route::get('/evenements', [EvenementController::class, 'index'])->name('Admin.liste-eve');

Route::get('/evenements/create',[EvenementController::class, 'create'])->name('create-evenement.create') ;
Route::post('/evenements',[EvenementController::class, 'store'])->name('create-evenement.store') ;
Route::get('/evenements/{id}/edit', [EvenementController::class, 'edit'])->name('edit-evenement.edit');
Route::put('/evenements/{id}', [EvenementController::class, 'update'])->name('edit-evenement.update');
Route::get('evenements/{evenement}/download', [EvenementController::class, 'download'])
     ->name('liste-eve.download');
Route::delete('/evenements/{id}', [EvenementController::class, 'destroy'])->name('delete-evenement.destroy');

//emploi
Route::get('/ens-eleve', [EmploiEvenementController::class, 'afficher'])->name('Admin.ens-eleve');
Route::resource('par-emplois', EmploiController::class)->only([
    'index', 'store', 'destroy'
]);

Route::get('emplois/{emploi}/download', [EmploiController::class, 'download'])
     ->name('emplois.download');
});

Route::prefix('admin')->middleware(['auth'])->group(function() {
    Route::get('/ens-emplois', [EmploiController::class, 'afficher'])->name('ens-emplois.index');
    Route::post('/ens-emplois/ajouter', [EmploiController::class, 'ajouter'])->name('ens-emplois.ajouter');
    Route::delete('/ens-emplois/{id}', [EmploiController::class, 'supprimer'])->name('ens-emplois.supprimer');
    Route::get('emplois/{emploi}/voir', [EmploiController::class, 'voir'])
     ->name('emplois.voir');
});



Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
    Route::post('/bulletins', [BulletinController::class, 'store'])->name('bulletins.store');
    Route::delete('/bulletins/{bulletin}', [BulletinController::class, 'destroy'])->name('bulletins.destroy');
    Route::get('/bulletins/download/{bulletin}', [BulletinController::class, 'download'])->name('bulletins.download');
});

// routes/web.php
Route::prefix('admin')->middleware(['auth'])->group(function() {
    Route::get('/notes', [NotesController::class, 'index'])->name('admin.notes');
    Route::get('/classes/{cycle}', [NotesController::class, 'getClassesByCycle']);
Route::get('/eleves/{classe}', [NotesController::class, 'getElevesByClasse']);
Route::post('/notes/save', [NotesController::class, 'saveNotes'])
     ->name('admin.notes.save')
     ->middleware('auth');    
Route::get('/matieres/{classe}', [NotesController::class, 'getMatieresByClasse']);
});

Route::prefix('enseignant')->middleware(['auth'])->group(function() {
    Route::get('/notes', [NotesController::class, 'afficher'])->name('enseignant.notes');
Route::post('/notes/save', [NotesController::class, 'saveNotes'])->name('enseignant.notes.save')->middleware('auth','verified');   
});
Route::get('/enseignant/eleves/{classe}', [NotesController::class, 'getEleves'])
    ->name('enseignant.eleves')
    ->where('classe', '.*'); // This allows slashes in the classe parameter

//Exporter Bulletin
// Routes pour les bulletins parents
Route::prefix('parent')->middleware(['auth', IsParent::class])->group(function () {
    Route::get('/bulletins', [ParentController::class, 'showBulletins'])->name('parent.ex_bulletin');
    Route::get('/bulletins/{eleveId}', [ParentController::class, 'showBulletinsForEleve'])->name('parent.bulletins.eleve');
});

// Dans web.php
Route::get('/parent/bulletins/voir/{bulletin}', [ParentController::class, 'voirBulletin'])
     ->name('parent.bulletins.voir')
     ->middleware(['auth']); // Utilisez le nom enregistré

   Route::get('/evenements/{evenement}/voir', [ParentController::class, 'voir'])
           ->name('parent.evenements.voir');

//notif admin
// Routes pour les notifications de l'admin
Route::prefix('admin')->group(function() {
    Route::get('notifications', [NotifController::class, 'index'])->name('admin.notifications.index');
    Route::delete('notifications/{notificationId}', [NotifController::class, 'destroy'])->name('admin.notifications.delete');
});
Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
    Route::get('/{id}', [NotifController::class, 'show'])->name('show');
    Route::patch('/{id}/accepter', [NotifController::class, 'accepter'])->name('accepter');
    Route::patch('/{id}/refuser', [NotifController::class, 'refuser'])->name('refuser');
    Route::delete('/{id}', [NotifController::class, 'destroy'])->name('delete');
});
Route::get('/admin/notifications/{justification}/download', 
[NotifController::class, 'downloadJustification'])
->name('admin.notifications.download')
->middleware(['auth']); // Retirez 'admin' ou créez le middleware


