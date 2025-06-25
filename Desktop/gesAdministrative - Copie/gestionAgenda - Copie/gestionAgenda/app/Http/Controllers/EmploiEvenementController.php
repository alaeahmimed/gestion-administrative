<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmploiEvenementController extends Controller
{
    public function index()
    {
        // Logique pour afficher l'emploi du temps/événements
        return view('Admin.emploi-evenement');
    }
    
    public function afficher()
    {
        // Logique pour afficher l'emploi du temps/événements
        return view('Admin.ens-eleve');
    }
}