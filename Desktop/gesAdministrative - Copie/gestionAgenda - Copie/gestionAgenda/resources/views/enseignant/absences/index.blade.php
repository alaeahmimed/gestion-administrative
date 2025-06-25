@extends('layouts.enseignant')

@section('content')
<div class="main-content" >

    <!-- Titre principal -->
    <h1 class="text-center mb-3"  style="font-size: 2.4rem; color: #0077C8; font-weight: bold;">
        Feuille de Présence
    </h1>

    <!-- Message de succès -->
    @if(session('success'))
        <div class="alert alert-success text-center mx-auto mb-4" style="max-width: 600px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Sélecteur de classe -->
    <form method="GET" action="{{ route('enseignant.absences.index') }}" class="mb-4 d-flex justify-content-center">
        <div class="input-group" style="max-width: 400px; width: 100%;">
            <select name="classe" onchange="this.form.submit()">
    <option value="">-- Choisir une classe --</option>
    @foreach($classes as $classe)
        <option value="{{ $classe }}" {{ ($selectedClasse == $classe) ? 'selected' : '' }}>
            {{ $classe }}
        </option>
    @endforeach
</select>

        </div>
    </form>

    <!-- Tableau des élèves -->
    @if($selectedClasse && count($eleves))
        <form method="POST" action="{{ route('enseignant.absences.store') }}" class="w-100 d-flex flex-column align-items-center">
            @csrf
            <input type="hidden" name="dateEnvoi" value="{{ date('Y-m-d') }}">

            <div class="table-responsive d-flex justify-content-center" style="width: 100%;">
                <div class="table-container">
                    <table class="table table-bordered table-hover text-center align-middle" style="min-width: 700px; max-width: 800px;">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 20%;">Nom</th>
                                <th style="width: 20%;">Prénom</th>
                                <th style="width: 20%;">Présent</th>
                                <th style="width: 20%;">Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($eleves as $eleve)
                        <tr>
                                    <td>{{ $eleve->user->nom }}</td>
                                    <td>{{ $eleve->user->prenom }}</td>
      
                                    <td>
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="radio" name="absences[{{ $eleve->id }}]" value="present" class="form-check-input">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="radio" name="absences[{{ $eleve->id }}]" value="absent" class="form-check-input">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">Enregistrer les absences</button>
            </div>
        </form>
    @elseif($selectedClasse)
        <p class="text-center text-muted mt-4">Aucun élève trouvé pour cette classe.</p>
    @endif

</div>

<style>
    .main-content {
        margin-left: 80px;
        padding: 20px;
        height: 100%;
        min-width: 0;
        overflow-x: auto;
    }

    .table-primary {
        background-color: #0077C8;
        color: white;
    }

    .btn-primary {
        background-color: #0077C8;
        border-color: #0077C8;
    }

    .btn-primary:hover {
        background-color: #005fa3;
        border-color: #005fa3;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 119, 200, 0.1);
    }
</style>
@endsection
