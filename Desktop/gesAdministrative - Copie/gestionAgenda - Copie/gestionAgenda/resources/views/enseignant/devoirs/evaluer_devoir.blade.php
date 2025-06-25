@extends('layouts.enseignant')

@section('content')
<div class="container-fluid py-5 px-4">

    <!-- Titre -->
    <div class="text-center mb-5">
        <h1 style="font-size: 2.5rem; color: #0077C8; font-weight: bold;">
            Évaluer Devoirs
        </h1>
    </div>

    <!-- Alerte de succès -->
    @if(session('success'))
        <div class="alert alert-success text-center mx-auto" style="max-width: 700px; font-size: 1.1rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Sélection Classe & Devoir -->
    <div class="bg-light p-4 rounded shadow-sm mb-5">
        <form method="GET" action="{{ route('enseignant.evaluer.devoir') }}" class="d-flex flex-wrap justify-content-center gap-3">
            <div style="min-width: 250px;">
                <select name="classe" id="classe" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choisir une classe --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('classe') == $c ? 'selected' : '' }}>
                            {{ $c }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('classe'))
            <div style="min-width: 250px;">
                <select name="devoir_id" id="devoir_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choisir un devoir --</option>
                    @foreach($devoirs as $d)
                        <option value="{{ $d->id }}" {{ request('devoir_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->titre }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </form>
    </div>

    <!-- Liste élèves -->
    @if($eleves->isNotEmpty())
        @if(request('devoir_id'))
            <form method="POST" action="{{ route('enseignant.evaluer.store') }}">
                @csrf
                <input type="hidden" name="devoir_id" value="{{ request('devoir_id') }}">
                <input type="hidden" name="classe" value="{{ request('classe') }}">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>Nom de l'élève</th>
                                <th>Note (/20)</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($eleves as $eleve)
                                <tr>
                                    <td>{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                                    <td>
                                        <input type="number" 
                                               name="notes[{{ $eleve->id }}]" 
                                               value="{{ old('notes.' . $eleve->id, $notes[$eleve->id]['note'] ?? '') }}" 
                                               min="0" max="20" step="0.5" 
                                               class="form-control text-center" required>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="commentaires[{{ $eleve->id }}]" 
                                               value="{{ old('commentaires.' . $eleve->id, $notes[$eleve->id]['commentaire'] ?? '') }}" 
                                               placeholder="Facultatif" 
                                               class="form-control">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold" style="font-size: 1.1rem;">
                        Confirmer l'Évaluation
                    </button>
                </div>
            </form>
        @else
            <div class="alert alert-info text-center">
                Veuillez sélectionner un devoir pour noter les élèves de la classe <strong>{{ request('classe') }}</strong>.
            </div>

            <ul class="list-group mt-3">
                @foreach($eleves as $eleve)
                    <li class="list-group-item">{{ $eleve->nom }} {{ $eleve->prenom }}</li>
                @endforeach
            </ul>
        @endif
    @endif

</div>

<style>
    h1{
        margin-right:70px ;
    }
    .main-content {
        margin-left: 150px;
    }
</style>
@endsection
