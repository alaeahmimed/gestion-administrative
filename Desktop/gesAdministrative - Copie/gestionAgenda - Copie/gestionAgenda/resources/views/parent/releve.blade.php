@extends('layouts.parent')

@section('content')
    <div class="container mt-4">

        @if(session('error'))
            <div class="alert alert-danger text-center mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Vérifie si $eleves est défini et n'est pas vide --}}
        @if(isset($eleves) && $eleves->isNotEmpty())

            {{-- Formulaire de sélection d'élève --}}
            <div class="p-3 mb-3">
                <form action="{{ route('parent.releve') }}" method="GET" class="d-flex flex-column align-items-center">
                    <div class="form-group w-100 d-flex justify-content-center" >
                        <select name="eleve_id" id="eleve" class="custom-select" aria-label="Sélection de l'élève" style="min-width:max-content;">
                            <option value="" disabled {{ !isset($eleve) ? 'selected' : '' }} hidden>Veuillez choisir un élève...</option>
                            @foreach($eleves as $eleveOption)
                                <option value="{{ $eleveOption->id }}"
                                    {{ isset($eleve) && $eleve->id == $eleveOption->id ? 'selected' : '' }}>
                                    {{ $eleveOption->user->nom }} {{ $eleveOption->user->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-block" style="min-width:max-content;">Voir les notes</button>
                </form>
            </div>

            {{-- Si un élève est sélectionné, afficher ses notes --}}
            @if(isset($eleve))
                <div class="card p-4 shadow-sm">
                    <h3 class="text-center mb-4">Releve de notes de {{ $eleve->user->nom }} {{ $eleve->user->prenom }}</h3>

                    @if(isset($notes) && $notes->isNotEmpty())
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Matiere</th>
                                    <th>CC1</th>
                                    <th>CC2</th>
                                    <th>CC3</th>
                                    <th>Projet</th>
                                </tr>
                            </thead>
                            <tbody>
    @foreach($notes as $note)
        <tr>
            <td>{{ $note->matiere }}</td>
            <td>{{ $note->cc1 }}</td>
            <td>{{ $note->cc2 }}</td>
            <td>{{ $note->cc3 }}</td>
            <td>{{ $note->projet }}</td>
            
        </tr>
    @endforeach
</tbody>

                        </table>
                    @else
                        <p class="text-center">Aucun notes n'est disponible pour cet élève.</p>
                    @endif
                </div>
            @endif

        @else
            <p class="text-center text-warning">Aucun enfant trouvé.</p>
        @endif

    </div>

    <style>
        /* Style personnalisé pour le select */
        .form-group {
            margin-bottom: 1rem;
        }

        .custom-select {
            background-color: #ffffff;
            font-size: 1rem;
            padding: 12px;
            width: 50%;
            height: 48px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.3s ease;
            display: block;
        }

        /* Style pour le bouton */
        .btn-block {
            width: 50%;
            padding: 12px;
            font-size: 1.1rem;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            height: 45px;
            transition: background-color 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .btn-block:hover {
            background-color: #0056b3;
        }

        /* Autres styles */
        .table {
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .thead-dark {
            background-color: #343a40;
            color: white;
        }

        .alert {
            font-size: 1.1rem;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
@endsection
