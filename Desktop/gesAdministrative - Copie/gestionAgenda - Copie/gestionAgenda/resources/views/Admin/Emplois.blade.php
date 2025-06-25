@extends('layouts.app')

@section('content')
<div class="container-fluid">

     {{--Bouton Retourner--}}
     <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{route('emploi-evenement.index')}}" class="btn btn-primary" > Retourne</a>
       
     </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Emploi du temps - {{ $selectedClasse }}</h2>  
      <a href="{{route('emplois.create')}}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter un emploi
        </a>
    </div>

    <!-- Filtre par classe -->
    <div class="row mb-4">
        <div class="col-md-4">
        
            <form method="GET" action="{{ route('emplois.index') }}">
                <div class="form-group">
                    <label for="classe">Filtrer par classe :</label>
                    <select name="classe" id="classe" class="form-control" onchange="this.form.submit()">
                        @foreach($classes as $classe)
                            <option value="{{ $classe }}" {{ $selectedClasse == $classe ? 'selected' : '' }}>
                                {{ $classe }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau d'emploi du temps -->
    <div class="table-responsive">
    <table class="table table-bordered table-hover" style="width:100%">
        <thead class="thead-dark">
            <tr>
                <th style="width:15%; text-align:center">Jour</th>
                <th style=" text-align:center">Heure Debut-Heure Fin</th>
                <th style="text-align:center">Cours</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($emplois as $jour => $coursDuJour)
            @foreach($coursDuJour as $emploi)
                <tr>
                    <td>{{ $emploi->jour }}</td>
                    <td style="text-align: center">
                        <div>{{ \Carbon\Carbon::parse($emploi->heureDebut)->format('H:i') }} - {{ \Carbon\Carbon::parse($emploi->heureFin)->format('H:i') }}</div>
                        <div style="margin-top: 5px;">Salle: {{ $emploi->salle }}</div>
                    </td>
                    <td>{{ $emploi->matiere }}</td>
                    <td>
                        <a href="{{ route('emplois.edit', $emploi->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('emplois.destroy', $emploi->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
    </table>
</div>
@endsection

<style>
    /* Styles globaux pour la table */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .table th {
        background-color: #4e73df;
        color: white;
        padding: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table td {
        padding: 10px;
        border: 1px solid #e3e6f0;
    }
    
    /* Style pour les lignes alternées */
    .table tbody tr:nth-child(even) {
        background-color: #f8f9fc;
    }
    
    /* Effet de survol */
    .table-hover tbody tr:hover {
        background-color: #f1f3f9;
    }
    
    /* Style responsive */
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }
        
        .table thead {
            display: none;
        }
        
        .table tr {
            margin-bottom: 15px;
            display: block;
            border: 1px solid #e3e6f0;
        }
        
        .table td {
            display: block;
            text-align: right;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .table td::before {
            content: attr(data-label);
            float: left;
            font-weight: bold;
        }
    }
</style>