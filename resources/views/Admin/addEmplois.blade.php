@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <!-- Colonne principale réduite pour une meilleure lisibilité -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ajouter Emploi du Temps</h5>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('emplois.store') }}" class="needs-validation" novalidate>
                        @csrf


                        <!-- Groupe Jour -->
                        <div class="mb-3 row">
                            <label for="jour" class="col-md-4 col-form-label text-md-end fw-bold">Jour :</label>
                            <div class="col-md-8">
                                <select id="jour" class="form-select" name="jour" required>
                                    <option value="" selected disabled>Sélectionner Jour...</option>
                                    <option value="Lundi">Lundi</option>
                                    <option value="Mardi">Mardi</option>
                                    <option value="Mercredi">Mercredi</option>
                                    <option value="Jeudi">Jeudi</option>
                                    <option value="Vendredi">Vendredi</option>
                                    <option value="Samedi">Samedi</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner un jour.</div>
                            </div>
                        </div>

                        <!-- Groupe Heures -->
                        <div class="mb-3 row">
                            <label for="heureDebut" class="col-md-4 col-form-label text-md-end fw-bold">Heure Début :</label>
                            <div class="col-md-8">
                                <input type="time" id="heureDebut" class="form-control" name="heureDebut" required>
                                <div class="invalid-feedback">Veuillez spécifier une heure de début.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="heureFin" class="col-md-4 col-form-label text-md-end fw-bold">Heure Fin :</label>
                            <div class="col-md-8">
                                <input type="time" id="heureFin" class="form-control" name="heureFin" required>
                                <div class="invalid-feedback">Veuillez spécifier une heure de fin.</div>
                            </div>
                        </div>

                        <!-- Groupe Matière -->
                        <div class="mb-3 row">
                            <label for="matiere" class="col-md-4 col-form-label text-md-end fw-bold">Matière :</label>
                            <div class="col-md-8">
                                <select id="matiere" class="form-select" name="matiere" required>
                                    <option value="" selected disabled>Sélectionner une matière...</option>
                                    <option value="Mathématiques">Mathématiques</option>
                                    <option value="Physique">Physique</option>
                                    <option value="Chimie">Chimie</option>
                                    <option value="Français">Français</option>
                                    <option value="Anglais">Anglais</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner une matière.</div>
                            </div>
                        </div>

                        <!-- Groupe Salle -->
                        <div class="mb-3 row">
                            <label for="salle" class="col-md-4 col-form-label text-md-end fw-bold">N° Salle :</label>
                            <div class="col-md-8">
                                <select id="salle" class="form-select" name="salle" required>
                                    <option value="" selected disabled>Sélectionner Numéro de Salle...</option>
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A26">A26</option>
                                    <option value="A29">A29</option>
                                    <option value="B1">B1</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner une salle.</div>
                            </div>
                            <div class="mb-3 row">
    <label for="date" class="col-md-4 col-form-label text-md-end fw-bold">Date :</label>
    <div class="col-md-8">
        <input type="date" id="date" class="form-control" name="date" required>
        <div class="invalid-feedback">Veuillez spécifier une date.</div>
    </div>
</div>
                        </div>

                        <!-- Groupe Classe -->
                       <!-- Modifier la partie sélection de classe -->
<div class="mb-4 row">
    <label for="classe" class="col-md-4 col-form-label text-md-end fw-bold">Classe :</label>
    <div class="col-md-8">
        <select id="classe" class="form-select" name="classe" required>
            <option value="" selected disabled>Sélectionner une classe...</option>
            @foreach($classes as $classe)
                <option value="{{ $classe }}">{{ $classe }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback">Veuillez sélectionner une classe.</div>
    </div>
</div>
                        <!-- Boutons d'action -->
                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="fas fa-save me-1"></i> Ajouter
                                </button>
                                <a href="{{ route('emplois.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-1"></i> Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation Bootstrap -->
<script>
(function () {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<!-- Style additionnel -->
<style>
.card {
    border-radius: 0.5rem;
}

.card-header {
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1rem 1.5rem;
}

.form-control, .form-select {
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn {
    border-radius: 0.375rem;
    padding: 0.5rem 1.25rem;
}

.invalid-feedback {
    display: none;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.was-validated .form-control:invalid, 
.was-validated .form-select:invalid {
    border-color: #dc3545;
}

.was-validated .form-control:invalid:focus, 
.was-validated .form-select:invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.was-validated .form-control:invalid ~ .invalid-feedback,
.was-validated .form-select:invalid ~ .invalid-feedback {
    display: block;
}
</style>
@endsection